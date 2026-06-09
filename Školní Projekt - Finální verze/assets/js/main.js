(function(){
	// -----------------------------
	// Helpers
	// -----------------------------
	const CZK = new Intl.NumberFormat('cs-CZ');
	const formatCzk = (n) => `${CZK.format(Math.round(n))} Kč`;
	const safeJson = (raw, fallback) => {
		try{ return JSON.parse(raw); }catch(_){ return fallback; }
	};

	// -----------------------------
	// Mobile menu
	// -----------------------------
	const btn = document.querySelector('[data-mobile-toggle]');
	const panel = document.querySelector('[data-mobile-panel]');
	if(btn && panel){
		btn.addEventListener('click', () => {
			const open = panel.getAttribute('data-open') === 'true';
			panel.setAttribute('data-open', String(!open));
			panel.style.display = open ? 'none' : 'block';
			btn.setAttribute('aria-expanded', String(!open));
		});
	}

	// Category filter (chips)
	const chips = document.querySelectorAll('[data-filter]');
	const groups = document.querySelectorAll('[data-group]');
	const items = document.querySelectorAll('[data-category]');

	if(chips.length && (groups.length || items.length)){
		const setActive = (val) => {
			chips.forEach(c => c.setAttribute('aria-pressed', String(c.dataset.filter === val)));

			// Prefer grouped sections (category heading + grid together)
			if(groups.length){
				groups.forEach(g => {
					const show = (val === 'all') || (g.dataset.group === val);
					g.style.display = show ? '' : 'none';
				});
				return;
			}

			// Fallback: hide/show individual product cards
			items.forEach(it => {
				const show = (val === 'all') || (it.dataset.category === val);
				it.style.display = show ? '' : 'none';
			});
		};

		chips.forEach(c => c.addEventListener('click', () => setActive(c.dataset.filter)));
		setActive('all');
	}


	// -----------------------------
	// Cart (localStorage)
	// -----------------------------
	const CART_KEY = 'neonshop_cart_v1';
	const getCart = () => {
		const cart = safeJson(localStorage.getItem(CART_KEY), []);
		return Array.isArray(cart) ? cart : [];
	};
	const saveCart = (cart) => localStorage.setItem(CART_KEY, JSON.stringify(cart));
	const cartCount = (cart) => cart.reduce((sum, it) => sum + (Number(it.qty) || 0), 0);
	const cartTotal = (cart) => cart.reduce((sum, it) => sum + (Number(it.price) || 0) * (Number(it.qty) || 0), 0);

	const clampQty = (n) => {
		const x = Number(n);
		if(!Number.isFinite(x)) return 1;
		return Math.max(1, Math.min(99, Math.round(x)));
	};

	const setQty = (id, qty) => {
		const cart = getCart();
		const it = cart.find(x => x.id === String(id));
		if(!it) return;
		it.qty = clampQty(qty);
		saveCart(cart);
		updateCartBadge();
	};

	const removeItem = (id) => {
		const cart = getCart().filter(x => x.id !== String(id));
		saveCart(cart);
		updateCartBadge();
	};

	const updateCartBadge = () => {
		const badge = document.querySelector('.badge[href*="kosik-krok1"]');
		if(!badge) return;
		const count = cartCount(getCart());
		badge.textContent = `🛒 ${count}`;
		badge.setAttribute('aria-label', `Košík, ${count} položek`);
	};

	const addToCart = (item) => {
		const cart = getCart();
		const existing = cart.find(x => x.id === item.id);
		if(existing){
			existing.qty = (Number(existing.qty) || 0) + 1;
		} else {
			cart.push({
				id: String(item.id),
				name: String(item.name || 'Produkt'),
				price: Number(item.price) || 0,
				qty: 1
			});
		}
		saveCart(cart);
		updateCartBadge();
	};

	// Bind "Add to cart" buttons/links
	const addBtns = document.querySelectorAll('[data-add-to-cart]');
	if(addBtns.length){
		addBtns.forEach(el => {
			el.addEventListener('click', (e) => {
				e.preventDefault();
				const id = el.getAttribute('data-product-id') || el.dataset.productId;
				const name = el.getAttribute('data-product-name') || el.dataset.productName;
				const price = el.getAttribute('data-product-price') || el.dataset.productPrice;
				if(!id) return;
				addToCart({ id, name, price });

				// Small UX feedback (non-blocking)
				const prev = el.textContent;
				el.textContent = 'Přidáno ✓';
				el.classList.add('is-added');
				setTimeout(() => {
					el.textContent = prev;
					el.classList.remove('is-added');
				}, 900);
			});
		});
	}

	// Render cart tables (krok 1–3 + potvrzení)
	const cartTbody = document.querySelector('[data-cart-items]');
	const cartTotalEl = document.querySelector('[data-cart-total]');
	if(cartTbody && cartTotalEl){
		const layout = cartTbody.getAttribute('data-cart-layout') || 'npq';
		const mode = cartTbody.getAttribute('data-cart-mode') || 'static'; // static | editable

		const renderCartTable = () => {
			const cart = getCart();
			cartTbody.innerHTML = '';

			// Column count for empty-row colspan
			const cols = (mode === 'editable') ? 3 : ((layout === 'np') ? 2 : 3);

			if(cart.length === 0){
				cartTbody.innerHTML = `<tr><td colspan="${cols}" class="small">Košík je prázdný. Přidej si něco z produktů 🙂</td></tr>`;
				cartTotalEl.textContent = formatCzk(0);
				return;
			}

			cart.forEach(it => {
				const tr = document.createElement('tr');

				if(mode === 'editable'){
					tr.innerHTML = `
						<td>${it.name}</td>
						<td class="nowrap">${formatCzk(it.price)}</td>
						<td class="nowrap">
							<div class="qty" role="group" aria-label="Počet kusů">
								<button type="button" class="qty-btn" data-qty-dec data-id="${it.id}" aria-label="Snížit">−</button>
								<input class="qty-input" type="number" min="1" max="99" inputmode="numeric" value="${clampQty(it.qty)}" data-qty-input data-id="${it.id}" aria-label="Počet">
								<button type="button" class="qty-btn" data-qty-inc data-id="${it.id}" aria-label="Zvýšit">+</button>
							</div>
							<button type="button" class="link-danger" data-remove data-id="${it.id}">Odebrat</button>
						</td>
					`.trim();
				} else if(layout === 'nqp'){
					tr.innerHTML = `
						<td>${it.name}</td>
						<td class="nowrap">${it.qty}×</td>
						<td class="nowrap">${formatCzk(it.price)}</td>
					`.trim();
				} else if(layout === 'np'){
					tr.innerHTML = `
						<td>${it.name}</td>
						<td class="nowrap">${formatCzk(it.price)}</td>
					`.trim();
				} else {
					// npq (default): name, price, qty
					tr.innerHTML = `
						<td>${it.name}</td>
						<td class="nowrap">${formatCzk(it.price)}</td>
						<td class="nowrap">× ${it.qty}</td>
					`.trim();
				}

				cartTbody.appendChild(tr);
			});

			cartTotalEl.textContent = formatCzk(cartTotal(getCart()));
		};

		// Editable cart interactions (krok 1)
		if(mode === 'editable'){
			cartTbody.addEventListener('click', (e) => {
				const t = e.target;
				if(!(t instanceof Element)) return;
				const id = t.getAttribute('data-id');
				if(!id) return;

				if(t.hasAttribute('data-qty-inc')){
					const cart = getCart();
					const it = cart.find(x => x.id === String(id));
					setQty(id, (Number(it?.qty) || 1) + 1);
					renderCartTable();
				}
				if(t.hasAttribute('data-qty-dec')){
					const cart = getCart();
					const it = cart.find(x => x.id === String(id));
					setQty(id, (Number(it?.qty) || 1) - 1);
					renderCartTable();
				}
				if(t.hasAttribute('data-remove')){
					removeItem(id);
					renderCartTable();
				}
			});

			cartTbody.addEventListener('change', (e) => {
				const t = e.target;
				if(!(t instanceof HTMLInputElement)) return;
				if(!t.hasAttribute('data-qty-input')) return;
				const id = t.getAttribute('data-id');
				if(!id) return;
				setQty(id, t.value);
				renderCartTable();
			});
		}

		renderCartTable();
	}

	// Update badge on every page load
	updateCartBadge();
})();
