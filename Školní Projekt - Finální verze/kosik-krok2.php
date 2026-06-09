<!DOCTYPE html>
<html lang="cs">
 <head>
	<meta charset="utf-8"/>
	<meta content="width=device-width, initial-scale=1" name="viewport"/>
	<title>
	 HCV Fanshop
	</title>
	<link href="assets/css/main.css" rel="stylesheet"/>
 </head>
 <body>
	<header class="site-header">
	 <div class="container header-inner">
		<a aria-label="Domů" class="brand" href="index.html">
		 <span aria-hidden="true" class="brand-badge">
		 </span>
		 <span>
			HC Vítkovice Ridera Fanshop
		 </span>
		</a>
		<form action="vyhledavani.html" class="searchbar" method="get" role="search">
		 <input aria-label="Hledat produkty" name="q" placeholder="Hledat produkty…" type="search"/>
		</form>
		<nav aria-label="Hlavní menu" class="nav">
		 <a href="index.html">
			Domov
		 </a>
		 <a href="kategorie.html">
			Kategorie
		 </a>
		 <a href="produkty.php">
			Produkty
		 </a>
		 <a href="o-nas.php">
			O nás
		 </a>
		 <a href="kontakt.html">
			Kontakt
		 </a>
		</nav>
		<div class="header-actions">
		 <a aria-label="Košík" class="badge" href="kosik-krok1.html">
			🛒 0
		 </a>
		 <button aria-controls="mobilePanel" aria-expanded="false" class="icon-btn mobile-toggle" data-mobile-toggle="" type="button">
			☰
		 </button>
		</div>
	 </div>
	 <div class="container mobile-panel" data-mobile-panel="" data-open="false" id="mobilePanel" style="display:none">
		<nav aria-label="Mobilní menu" class="stack">
		 <a href="index.html">
			Home
		 </a>
		 <a href="kategorie.html">
			Kategorie
		 </a>
		 <a href="produkty.php">
			Produkty
		 </a>
		 <a href="o-nas.php">
			O nás
		 </a>
		 <a href="kontakt.html">
			Kontakt
		 </a>
		 <a href="kosik-krok1.html">
			Košík
		 </a>
		</nav>
	 </div>
	</header>
	<div class="container" style="padding-top: 18px;">
	 <nav aria-label="Breadcrumb" class="breadcrumb">
		<a href="index.html">
		 Home
		</a>
		<span aria-hidden="true">
		 ›
		</span>
		<a href="kosik-krok1.html">
		 Košík
		</a>
		<span aria-hidden="true">
		 ›
		</span>
		<span>
		 Krok 2
		</span>
	 </nav>
	</div>
	<main>
	 <section class="section">
		<div class="container stack">
		 <h1>
			Košík – krok 2: Doprava a platba
		 </h1>
		 <div class="grid grid-2">
			<div class="card card-pad stack">
			 <form action="kosik-krok3.html" class="stack" method="get">
				<div class="notice">
				 <strong>
					Doprava
				 </strong>
				</div>
				<div class="stack" style="gap:12px;">
				 <label>
					<input name="doprava" required="" type="radio"/>
					Kurýr (99 Kč)
				 </label>
				 <label>
					<input name="doprava" type="radio"/>
					Zásilkovna (79 Kč)
				 </label>
				 <label>
					<input name="doprava" type="radio"/>
					Osobní odběr (0 Kč)
				 </label>
				</div>
				<div class="notice" style="margin-top:16px;">
				 <strong>
					Platba
				 </strong>
				</div>
				<div class="stack" style="gap:12px;">
				 <label>
					<input name="platba" required="" type="radio"/>
					Kartou online
				 </label>
				 <label>
					<input name="platba" type="radio"/>
					Bankovní převod
				 </label>
				 <label>
					<input name="platba" type="radio"/>
					Dobírka (+39 Kč)
				 </label>
				</div>
				<div class="between wrap" style="margin-top: 16px;">
				 <a class="btn" href="kosik-krok1.html">
					Zpět
				 </a>
				 <button class="btn btn-primary" type="submit">
					Pokračovat
				 </button>
				</div>
			 </form>
			</div>
			<aside class="card">
			 <div class="card-pad">
				<h3>
				 Shrnutí
				</h3>
				<table aria-label="Položky v košíku" class="table">
				 <thead>
					<tr>
					 <th>
						Položka
					 </th>
					 <th>
						Cena
					 </th>
					</tr>
				 </thead>
				 <tbody data-cart-items="">
				 </tbody>
				</table>
			 </div>
			 <div class="total-row">
				<strong>
				 Celkem
				</strong>
				<strong data-cart-total="">
				 0 Kč
				</strong>
			 </div>
			</aside>
		 </div>
		 <p class="small">
			* Příklad – záleží na zvolené dopravě/platbě.
		 </p>
		</div>
	 </section>
	</main>
		<footer class="site-footer">
	 <div class="container footer-grid">
		<div>
		 <div class="footer-title">
			HC Vítkovice Ridera fanshop
		 </div>
		 <p class="small">
			Český klub ledního hokeje, který sídlí v Ostravě v Moravskoslezském kraji.
		 </p>
		</div>
		<div>
		 <div class="footer-title">
			Rychlé odkazy
		 </div>
		 <div class="footer-links">
			<a href="kategorie.html">
			 Kategorie
			</a>
			<a href="produkty.php">
			 Produkty
			</a>
			<a href="kosik-krok1.html">
			 Košík
			</a>
			<a href="kontakt.html">
			 Kontakt
			</a>
		 </div>
		</div>
		<div>
		 <div class="footer-title">
			Kontakt
		 </div>
		 <div class="footer-links">
			<span class="small">
			 Email: info@HCV.cz
			</span>
			<span class="small">
			 Tel: +420 123 456 789
			</span>
			<span class="small">
			 Po–Pá 9:00–21:00
			</span>
		 </div>
		</div>
	 </div>
	 <div class="container" style="margin-top: 22px;">
		<div class="small">
		 © 2026 HCV ESHOP
		</div>
	 </div>
	</footer>
	<script src="assets/js/main.js">
	</script>
 </body>
</html>
