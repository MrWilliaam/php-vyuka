<?php
declare(strict_types=1);

// 1) Načteme všechny třídy
require_once __DIR__ . '/src/bootstrap.php';

$productRepo = new ProductRepository();
$cart = new Cart();

// 2) Zpracování přidání do košíku (s výběrem varianty, např. Velikosti)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_to_cart'])) {
    $productId = (int) $_POST['product_id'];
    $product = $productRepo->getById($productId);

    if ($product !== null) {
        $variant = '';
        if (isset($_POST['variants']) && is_array($_POST['variants'])) {
            $parts = $_POST['variants'];
            ksort($parts);
            $variantParts = [];
            foreach ($parts as $paramName => $paramValue) {
                $variantParts[] = $paramName . ': ' . $paramValue;
            }
            $variant = implode(', ', $variantParts);
        }

        $cart->add(
            productId: $product->id,
            productName: $product->name,
            unitPrice: $product->price,
            image: $product->image,
            variant: $variant,
        );
    }
    header('Location: ' . $_SERVER['REQUEST_URI']);
    exit;
}

// 3) Načtení produktu podle názvu v URL (tzv. slugu)
$slug = trim($_GET['slug'] ?? '');
$product = $slug !== '' ? $productRepo->getBySlug($slug) : null;

if ($product === null) {
    http_response_code(404);
    $pageTitle = 'Produkt nenalezen';
    $cartItemCount = $cart->getTotalQuantity();
    require __DIR__ . '/partials/header.php';
    echo '<main class="container" style="padding: 50px 0; text-align: center;"><h1>Produkt nenalezen</h1><p>Zkuste se vrátit na <a href="index.php">hlavní stránku</a>.</p></main>';
    require __DIR__ . '/partials/footer.php';
    exit;
}

// 4) Načtení parametrů a fotek
$images = $productRepo->getImages($product->id);
$params = $productRepo->getParameters($product->id);

$selectableParams = array_filter($params, fn($p) => $p->isSelectable());
$infoParams = array_filter($params, fn($p) => !$p->isSelectable());

$pageTitle = $product->name . ' – HCV Fanshop';
$cartItemCount = $cart->getTotalQuantity();

require __DIR__ . '/partials/header.php'; 
?>

<div class="container" style="padding-top: 18px;">
    <nav aria-label="Breadcrumb" class="breadcrumb">
        <a href="index.php">Home</a>
        <span aria-hidden="true">›</span>
        <a href="produkty.php">Produkty</a>
        <span aria-hidden="true">›</span>
        <span>Detail</span>
    </nav>
</div>

<main>
    <section class="section">
        <div class="container stack">
            <h1>Detail produktu</h1>
            <div class="grid grid-2">
                <div class="card">
                    <?php if (!empty($product->image)): ?>
                        <img class="product-media" src="<?= htmlspecialchars($product->image) ?>" alt="<?= htmlspecialchars($product->name) ?>" style="aspect-ratio: 1/1; object-fit: cover; width: 100%;">
                    <?php else: ?>
                        <div aria-hidden="true" class="product-media" style="aspect-ratio: 1 / 1; width: 100%;"></div>
                    <?php endif; ?>
                    
                    <?php if ($images !== []): ?>
                    <div class="card-pad">
                        <div class="grid grid-3" style="gap:12px;">
                            <?php foreach ($images as $img): ?>
                                <img src="<?= htmlspecialchars($img->image) ?>" alt="Galerie" style="aspect-ratio: 1 / 1; object-fit: cover; width: 100%;">
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>

                <div class="card card-pad stack">
                    <div>
                        <div class="meta"><?= htmlspecialchars($product->categoryName ?? '') ?></div>
                        <h2><?= htmlspecialchars($product->name) ?></h2>
                        <p><?= htmlspecialchars($product->description) ?></p>
                    </div>
                    
                    <form method="post">
                        <input type="hidden" name="product_id" value="<?= $product->id ?>">

                        <?php foreach ($selectableParams as $param): ?>
                            <div style="margin-bottom: 15px;">
                                <label for="variant-<?= htmlspecialchars($param->name) ?>">
                                    <strong><?= htmlspecialchars($param->name) ?>:</strong>
                                </label>
                                <select name="variants[<?= htmlspecialchars($param->name) ?>]" id="variant-<?= htmlspecialchars($param->name) ?>" required style="padding: 5px; margin-left: 10px; border-radius: 4px; border: 1px solid #ccc;">
                                    <option value="">-- Vyberte --</option>
                                    <?php foreach ($param->getOptions() as $option): ?>
                                        <option value="<?= htmlspecialchars($option) ?>"><?= htmlspecialchars($option) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        <?php endforeach; ?>

                        <div class="between" style="margin-top: 20px;">
                            <div class="price" style="font-size:22px;">
                                <?= number_format($product->price, 0, ',', ' ') ?> Kč
                            </div>
                            <button type="submit" name="add_to_cart" class="btn btn-primary">
                                Přidat do košíku
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>
</main>

<?php require __DIR__ . '/partials/footer.php'; ?>