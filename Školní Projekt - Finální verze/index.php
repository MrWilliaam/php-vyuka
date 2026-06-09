<?php
declare(strict_types=1);

// Načtení učitelových funkcí a databáze
require_once __DIR__ . '/src/bootstrap.php';

// Inicializace košíku
$cart = new Cart();
$cartItemCount = $cart->getTotalQuantity();

// TENTO ŘÁDEK PŘIDEJ (připojení k produktům):
$productRepo = new ProductRepository();

// Zpracování akce "přidat do košíku"
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_to_cart'])) {
    $productId = (int) $_POST['product_id'];
    $product = $productRepo->getById($productId);

    if ($product !== null) {
        $cart->add(
            productId: $product->id,
            productName: $product->name,
            unitPrice: $product->price,
            image: $product->image,
        );
    }
    // Přesměrování zpět, aby se formulář neodeslal dvakrát
    header('Location: ' . $_SERVER['REQUEST_URI']);
    exit;
}

// Název záložky v prohlížeči
$pageTitle = 'Domů - HC Vítkovice Ridera Fanshop';

// Vložení samotné šablony hlavičky
require __DIR__ . '/partials/header.php';
?>
    <main>
     <section class="hero">
        <div class="container">
         <div class="card hero-card">
            <div class="hero-inner">
             <div>
                <h1>
                 HC Vítkovice Fanshop
                </h1>
                <p>
                 Oblečení pro fanoušky HCV
                </p>
                <div class="hero-cta">
                 <a class="btn btn-primary" href="kategorie.php">
                    Prohlédnout kategorie
                 </a>
                 <a class="btn" href="produkty.php">
                    Zobrazit produkty
                 </a>
                </div>
             </div>
            </div>
         </div>
        </div>
     </section>
     <section class="section">
        <div class="container stack">
         <div class="between wrap">
            <h2>
             Kategorie
            </h2>
            <a class="btn" href="kategorie.php">
             Všechny kategorie
            </a>
         </div>
         <div class="grid grid-3">
            <a class="tile" href="produkty.php#mikiny-bundy">
             <div class="tile-title">
                Mikiny a Bundy
             </div>
            </a>
            <a class="tile" href="produkty.php#tricka">
             <div class="tile-title">
                Trička
             </div>
            </a>
            <a class="tile" href="produkty.php#saly">
             <div class="tile-title">
                Šály
             </div>
            </a>
            <a class="tile" href="produkty.php#cepice">
             <div class="tile-title">
                Čepice
             </div>
            </a>
            <a class="tile" href="produkty.php#dresy">
             <div class="tile-title">
                Dresy
             </div>
            </a>
            <a class="tile" href="produkty.php#doplnky">
             <div class="tile-title">
                Doplňky
             </div>
            </a>
         </div>
        </div>
     </section>
     <section class="section">
        <div class="container stack">
         <div class="between wrap">
            <h2>
             Doporučené produkty
            </h2>
            <a class="btn" href="produkty.php">
             Všechny produkty
            </a>
         </div>
         
         <div class="grid grid-4">
            <?php 
            // ==========================================
            // TADY ZAČÍNÁ NOVÝ PHP CYKLUS
            // Vytáhneme 4 doporučené produkty z databáze
            // ==========================================
            $featuredProducts = $productRepo->getFeatured(limit: 4);
            
            foreach ($featuredProducts as $product): ?>
                <article class="card product-card" data-category="<?= htmlspecialchars($product->categoryName ?? '') ?>">
                 
                 <?php if (!empty($product->image)): ?>
                    <img class="product-media" src="<?= htmlspecialchars($product->image) ?>" alt="<?= htmlspecialchars($product->name) ?>" style="object-fit: cover; width: 100%;">
                 <?php else: ?>
                    <div aria-hidden="true" class="product-media"></div>
                 <?php endif; ?>

                 <div class="product-body">
                    <div class="product-title">
                     <?= htmlspecialchars($product->name) ?>
                    </div>
                    <div class="price-row">
                     <div class="price">
                        <?= number_format($product->price, 0, ',', ' ') ?> Kč
                     </div>
                     <div class="meta">
                        Skladem
                     </div>
                    </div>
                    <div class="between">
                     <a class="btn" href="produkt.php?slug=<?= htmlspecialchars($product->slug ?? '') ?>">
                        Detail
                     </a>
                     
                     <form method="post" style="margin: 0;">
                        <input type="hidden" name="product_id" value="<?= $product->id ?>">
                        <button type="submit" name="add_to_cart" class="btn btn-primary">
                            Přidat
                        </button>
                     </form>
                    </div>
                 </div>
                </article>
            <?php endforeach; ?>
         </div>

        </div>
     </section>
    </main>
<?php require __DIR__ . '/partials/footer.php'; ?>