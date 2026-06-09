<?php

declare(strict_types=1);

/**
 * UKÁZKOVÁ STRÁNKA – detail produktu s parametry, galerií a výběrem variant
 *
 * Co tato stránka ukazuje:
 *   - Načtení produktu podle slugu z URL (?slug=...)
 *   - Výpis galerie obrázků a parametrů produktu
 *   - Volitelné parametry (velikost, barva) jako dropdown pro výběr
 *   - Přidání do košíku s vybranou variantou (Post/Redirect/Get)
 *   - Ošetření stavu, kdy produkt neexistuje
 */

// 1) Načteme všechny třídy
require_once __DIR__ . '/src/bootstrap.php';

// 2) Vytvoříme instance
$productRepo = new ProductRepository();
$cart = new Cart();

// 3) Zpracování akce "přidat do košíku"
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_to_cart'])) {
    $productId = (int) $_POST['product_id'];
    $product = $productRepo->getById($productId);

    if ($product !== null) {
        // Sestavení varianty z odeslaných dropdown hodnot
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

// 4) Načtení produktu podle slugu z URL
$slug = trim($_GET['slug'] ?? '');
$product = $slug !== '' ? $productRepo->getBySlug($slug) : null;

if ($product === null) {
    http_response_code(404);
    $pageTitle = 'Produkt nenalezen';
    $cartItemCount = $cart->getTotalQuantity();
    require __DIR__ . '/partials/header.php';
    echo '<main class="container"><h1>Produkt nenalezen</h1><p>Zkuste se vrátit na <a href="index.php">hlavní stránku</a>.</p></main>';
    require __DIR__ . '/partials/footer.php';
    exit;
}

// 5) Načtení souvisejících dat
$images = $productRepo->getImages($product->id);
$params = $productRepo->getParameters($product->id);

// Rozdělení parametrů na volitelné (select) a informační
$selectableParams = array_filter($params, fn(ProductParameterDTO $p) => $p->isSelectable());
$infoParams = array_filter($params, fn(ProductParameterDTO $p) => !$p->isSelectable());

// 6) Proměnné pro header
$pageTitle = $product->name . ' – SportShop';
$cartItemCount = $cart->getTotalQuantity();

?>
<?php require __DIR__ . '/partials/header.php'; ?>

<main class="container">
    <div class="product-detail">
        <!-- Levá strana – obrázky -->
        <div>
            <img
                class="product-detail__image"
                src="<?= htmlspecialchars($product->image) ?>"
                alt="<?= htmlspecialchars($product->name) ?>"
            >

            <?php if ($images !== []): ?>
                <div class="product-detail__gallery">
                    <?php foreach ($images as $img): ?>
                        <img
                            src="<?= htmlspecialchars($img->image) ?>"
                            alt="<?= htmlspecialchars($product->name) ?>"
                        >
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>

        <!-- Pravá strana – info -->
        <div>
            <span class="product-detail__category">
                <?= htmlspecialchars($product->categoryName ?? '') ?>
            </span>

            <h1 class="product-detail__name">
                <?= htmlspecialchars($product->name) ?>
            </h1>

            <div class="product-detail__price">
                <span class="product-detail__price-current">
                    <?= number_format($product->price, 0, ',', ' ') ?> Kč
                </span>

                <?php if ($product->hasDiscount()): ?>
                    <span class="product-detail__price-original">
                        <?= number_format($product->originalPrice, 0, ',', ' ') ?> Kč
                    </span>
                    <span class="product-card__discount">
                        -<?= $product->getDiscountPercent() ?> %
                    </span>
                <?php endif; ?>
            </div>

            <p class="product-detail__description">
                <?= htmlspecialchars($product->description) ?>
            </p>

            <!-- Formulář s výběrem variant a tlačítkem přidat do košíku -->
            <form method="post">
                <input type="hidden" name="product_id" value="<?= $product->id ?>">

                <?php foreach ($selectableParams as $param): ?>
                    <div class="product-detail__variant">
                        <label for="variant-<?= htmlspecialchars($param->name) ?>">
                            <?= htmlspecialchars($param->name) ?>:
                        </label>
                        <select
                            name="variants[<?= htmlspecialchars($param->name) ?>]"
                            id="variant-<?= htmlspecialchars($param->name) ?>"
                            required
                        >
                            <option value="">-- Vyberte --</option>
                            <?php foreach ($param->getOptions() as $option): ?>
                                <option value="<?= htmlspecialchars($option) ?>">
                                    <?= htmlspecialchars($option) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                <?php endforeach; ?>

                <button type="submit" name="add_to_cart" class="product-detail__btn">
                    Přidat do košíku
                </button>
            </form>

            <?php if ($infoParams !== []): ?>
                <table class="params-table">
                    <caption>Parametry produktu</caption>
                    <?php foreach ($infoParams as $param): ?>
                        <tr>
                            <th><?= htmlspecialchars($param->name) ?></th>
                            <td><?= htmlspecialchars($param->value) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </table>
            <?php endif; ?>
        </div>
    </div>
</main>

<?php require __DIR__ . '/partials/footer.php'; ?>
