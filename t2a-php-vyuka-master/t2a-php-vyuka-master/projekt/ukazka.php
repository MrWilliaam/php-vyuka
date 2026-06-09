<?php

declare(strict_types=1);

/**
 * UKÁZKOVÁ STRÁNKA – jak používat repozitáře, košík a partials
 *
 * Spuštění:
 *   1. Nejdřív vytvořte databázi:  php projekt/database/init.php
 *   2. Spusťte PHP server:         php -S localhost:8080 -t projekt
 *   3. Otevřete v prohlížeči:      http://localhost:8080/ukazka.php
 *
 * Co tato stránka ukazuje:
 *   - Načtení všech tříd přes bootstrap.php
 *   - Znovupoužití částí stránky (header, footer, product-card) přes require
 *   - Práce s ProductRepository (načtení doporučených produktů)
 *   - Práce s Cart (přidání do košíku, zobrazení počtu položek)
 *   - Vypsání dat z DTO objektů v HTML šabloně
 */

// 1) Načteme všechny třídy
require_once __DIR__ . '/src/bootstrap.php';

// 2) Vytvoříme instance repozitáře a košíku
$productRepo = new ProductRepository();
$cart = new Cart();

// 3) Zpracování akce "přidat do košíku" (přišla z formuláře)
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

    // Přesměrování zpět (Post/Redirect/Get pattern – zabrání opakovanému odeslání formuláře)
    header('Location: ' . $_SERVER['REQUEST_URI']);
    exit;
}

// 4) Načteme data pro šablonu
$featuredProducts = $productRepo->getFeatured(limit: 6);
$cartItemCount = $cart->getTotalQuantity();

// 5) Proměnné pro header partial
$pageTitle = 'SportShop – ukázka';

?>
<?php
// ============================================================
// HEADER – společná hlavička pro všechny stránky
// Partial očekává proměnné: $pageTitle, $cartItemCount
// ============================================================
require __DIR__ . '/partials/header.php';
?>

<main class="container">
    <h1 class="section-title">Doporučené produkty</h1>

    <div class="products-grid">
        <?php
        // ============================================================
        // PRODUCT CARD – opakovaně použitá komponenta
        // Partial očekává proměnnou: $product (ProductDTO)
        // ============================================================
        foreach ($featuredProducts as $product):
            require __DIR__ . '/partials/product-card.php';
        endforeach;
        ?>
    </div>

    <!-- ============================================================
         INFO BOX – vysvětlení pro studenty
         ============================================================ -->
    <div class="info-box">
        <h2>Jak fungují partials?</h2>
        <p>
            Místo kopírování HTML hlavičky a patičky do každého souboru
            použijete <code>require</code> a PHP vloží obsah automaticky:
        </p>
        <pre><code>&lt;?php
// Proměnné, které partial potřebuje
$pageTitle = 'Hlavní stránka';
$cartItemCount = $cart->getTotalQuantity();

// Vložení hlavičky (otevře &lt;html&gt;, &lt;head&gt;, &lt;header&gt;)
require __DIR__ . '/partials/header.php';
?&gt;

&lt;!-- Zde je obsah konkrétní stránky --&gt;

&lt;?php
// Vložení patičky (uzavře &lt;footer&gt;, &lt;/body&gt;, &lt;/html&gt;)
require __DIR__ . '/partials/footer.php';
?&gt;</code></pre>

        <h2>Jak funguje produktová karta?</h2>
        <p>
            Partial <code>product-card.php</code> očekává proměnnou <code>$product</code>.
            Ve smyčce se tak karta použije opakovaně pro každý produkt:
        </p>
        <pre><code>&lt;?php foreach ($products as $product): ?&gt;
    &lt;?php require __DIR__ . '/partials/product-card.php'; ?&gt;
&lt;?php endforeach; ?&gt;</code></pre>

        <h2>Soubory partials</h2>
        <p>Všechny znovupoužitelné části stránek jsou ve složce <code>partials/</code>:</p>
        <ul>
            <li><code>partials/header.php</code> – hlavička s navigací a košíkem</li>
            <li><code>partials/footer.php</code> – patička</li>
            <li><code>partials/product-card.php</code> – produktová karta</li>
        </ul>
    </div>
</main>

<?php
// ============================================================
// FOOTER – společná patička pro všechny stránky
// ============================================================
require __DIR__ . '/partials/footer.php';
?>
