<?php
declare(strict_types=1);

// 1) Inicializace aplikace
require_once __DIR__ . '/src/bootstrap.php';

// 2) Připojení na databázi a do košíku
$productRepo = new ProductRepository();
$cart = new Cart();

// 3) Zpracování přidání do košíku (stejné jako v ukazka.php)
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
    header('Location: ' . $_SERVER['REQUEST_URI']);
    exit;
}

// 4) Načtení úplně všech produktů z databáze (zde předpokládám metodu getAll())
// Učitel v ukázce použil getFeatured(), ale pro stránku produktů zřejmě potřebujeme getAll()
// Pokud ji repozitář nemá, upravíme to.
$allProducts = $productRepo->getAll(); 

// 5) Přidělení proměnných, na které se těší hlavička
$pageTitle = 'Produkty - HC Vítkovice Ridera Fanshop';
$cartItemCount = $cart->getTotalQuantity();

// 6) Vložení hlavičky
require __DIR__ . '/partials/header.php';
?>

<div class="container" style="padding-top: 18px;">
    <nav aria-label="Breadcrumb" class="breadcrumb">
        <a href="index.php">Home</a>
        <span aria-hidden="true">›</span>
        <span>Produkty</span>
    </nav>
</div>

<main>
    <section class="section">
        <div class="container stack">
            <div class="between wrap">
                <div class="stack" style="gap:10px;">
                    <h1>Produkty</h1>
                    <div class="help">Filtrovat podle kategorie:</div>
                </div>
                <a class="btn" href="kategorie.php">Kategorie</a>
            </div>
            
            <div aria-label="Filtr kategorií" class="chips" role="group">
                <button aria-pressed="true" class="chip" data-filter="all" type="button">Vše</button>
            </div>

            <section class="category-block">
                <div class="grid grid-4">
                    <?php 
                    // ==========================================
                    // HLAVNÍ KOUZLO - Výpis produktů
                    // ==========================================
                    foreach ($allProducts as $product): ?>
                        <article class="card product-card" data-category="<?= htmlspecialchars($product->categoryName ?? '') ?>">
                            <div class="product-body">
                                <div class="product-title">
                                    <?= htmlspecialchars($product->name) ?>
                                </div>
                                <p class="help" style="margin:6px 0 10px;">
                                    <?= htmlspecialchars($product->description ?? 'Základ do šatníku fanouška.') ?>
                                </p>
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
            </section>
        </div>
    </section>
</main>

<?php require __DIR__ . '/partials/footer.php'; ?>