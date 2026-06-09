# E-shop – 2. fáze (PHP + SQLite)

Připravený základ pro druhou fázi projektu e-shopu, kde propojíte svůj HTML/CSS frontend s PHP a SQLite databází.

## Spuštění v Codespaces

Databáze se vytvoří **automaticky při startu Codespace**. Stačí spustit webový server:

```bash
php -S 0.0.0.0:8080 -t projekt
```

Po spuštění klikněte na odkaz v terminálu nebo otevřete záložku **Ports** a klikněte na port **8080**.

Vzorové stránky:
- `/ukazka.php` – výpis produktů, košík, partials
- `/produkt.php?slug=nike-air-zoom-pegasus-41` – detail produktu s galerií a parametry

### Reset databáze

Pokud potřebujete databázi vytvořit znovu (nebo jste si změnili data v `init.php`):

```bash
php projekt/database/init.php
```

## Spuštění lokálně

```bash
php projekt/database/init.php
php -S localhost:8080 -t projekt
```

Otevřete v prohlížeči: `http://localhost:8080/ukazka.php`

## Struktura projektu

```
projekt/
├── database/
│   ├── init.php              ← skript pro vytvoření/reset databáze
│   └── eshop.db              ← SQLite databáze (generuje se automaticky)
├── src/
│   ├── bootstrap.php         ← načte všechny třídy (stačí jeden require)
│   ├── Database.php          ← připojení k databázi
│   ├── Cart.php              ← košík (ukládá do session)
│   ├── Validator.php         ← validátor formulářů (fluent interface)
│   ├── DTO/                  ← datové objekty (readonly třídy)
│   │   ├── CategoryDTO.php
│   │   ├── ProductDTO.php
│   │   ├── ProductImageDTO.php
│   │   ├── ProductParameterDTO.php
│   │   ├── ShippingMethodDTO.php
│   │   ├── PaymentMethodDTO.php
│   │   ├── CustomerDTO.php
│   │   ├── OrderDTO.php
│   │   ├── OrderItemDTO.php
│   │   └── CartItemDTO.php
│   └── Repository/           ← třídy pro práci s databází
│       ├── CategoryRepository.php
│       ├── ProductRepository.php
│       ├── ShippingMethodRepository.php
│       ├── PaymentMethodRepository.php
│       ├── CustomerRepository.php
│       └── OrderRepository.php
├── partials/                     ← znovupoužitelné části stránek
│   ├── header.php            ← hlavička s navigací a košíkem
│   ├── footer.php            ← patička
│   └── product-card.php      ← produktová karta
├── assets/
│   └── css/                  ← ukázkové CSS (nahradíte vlastním z 1. fáze)
│       ├── main.css
│       ├── variables.css
│       ├── base.css
│       ├── layout.css
│       ├── components.css
│       └── responsive.css
├── ukazka.php                ← vzorová stránka (partials + košík + produkty)
├── produkt.php               ← vzorový detail produktu (galerie + parametry)
└── README.md                 ← tento soubor
```

## Partials – znovupoužitelné části stránek

V 1. fázi jste hlavičku a patičku kopírovali ručně. S PHP stačí použít `require` a části se vloží automaticky:

```php
<?php
require_once __DIR__ . '/src/bootstrap.php';

$cart = new Cart();
$pageTitle = 'Hlavní stránka';
$cartItemCount = $cart->getTotalQuantity();

// Hlavička (otevírá <html>, <head>, <header> s navigací a košíkem)
require __DIR__ . '/partials/header.php';
?>

<!-- Zde je obsah konkrétní stránky -->

<?php
// Patička (uzavírá <footer>, </body>, </html>)
require __DIR__ . '/partials/footer.php';
?>
```

### Produktová karta

Partial `product-card.php` očekává proměnnou `$product` (ProductDTO). Ve smyčce se karta opakuje pro každý produkt:

```php
<div class="products-grid">
    <?php foreach ($products as $product): ?>
        <?php require __DIR__ . '/partials/product-card.php'; ?>
    <?php endforeach; ?>
</div>
```

### Vlastní partials

Stejným způsobem si můžete vytvořit další partials – například breadcrumb, boční panel, vyhledávací formulář apod. Stačí vytvořit nový soubor v `partials/` a vložit ho přes `require`.

## Jak používat na svých stránkách

Na začátku každého PHP souboru stačí načíst bootstrap a vytvořit instance tříd, které potřebujete:

```php
<?php
declare(strict_types=1);

require_once __DIR__ . '/src/bootstrap.php';

// Vytvoření repozitářů
$productRepo = new ProductRepository();
$categoryRepo = new CategoryRepository();
$cart = new Cart();
```

### Načítání dat z databáze

```php
// Všechny kategorie
$categories = $categoryRepo->getAll();

// Doporučené produkty (pro hlavní stránku)
$featured = $productRepo->getFeatured(limit: 6);

// Produkty v kategorii (podle ID nebo slugu)
$products = $productRepo->getByCategory(1);
$products = $productRepo->getByCategorySlug('bezecke-vybaveni');

// Konkrétní produkt podle slugu (pro detail produktu)
$product = $productRepo->getBySlug('nike-air-zoom-pegasus-41');

// Obrázky a parametry produktu (pro detail)
$images = $productRepo->getImages($product->id);
$params = $productRepo->getParameters($product->id);

// Rozdělení parametrů na volitelné (select) a informační (info)
$selectableParams = array_filter($params, fn(ProductParameterDTO $p) => $p->isSelectable());
$infoParams = array_filter($params, fn(ProductParameterDTO $p) => !$p->isSelectable());

// Zjištění, zda produkt vyžaduje výběr varianty
$product->hasVariants; // true pokud má alespoň jeden parametr typu 'select'

// Vyhledávání
$results = $productRepo->search('nike');
```

### Práce s košíkem

```php
$cart = new Cart();

// Přidat produkt do košíku (bez varianty)
$cart->add(
    productId: $product->id,
    productName: $product->name,
    unitPrice: $product->price,
    image: $product->image,
);

// Přidat produkt s variantou (stejný produkt v jiné variantě = jiná položka)
$cart->add(
    productId: $product->id,
    productName: $product->name,
    unitPrice: $product->price,
    image: $product->image,
    variant: 'Barva: Černá, Velikost: 42',
);

// Změnit množství (pro variantu je třeba uvést i variant string)
$cart->updateQuantity(productId: 1, quantity: 3);
$cart->updateQuantity(productId: 1, quantity: 2, variant: 'Barva: Černá, Velikost: 42');

// Odebrat produkt
$cart->remove(productId: 1);
$cart->remove(productId: 1, variant: 'Barva: Černá, Velikost: 42');

// Zobrazení košíku
$items = $cart->getItems();           // pole CartItemDTO[]
$total = $cart->getTotalPrice();      // celková cena
$count = $cart->getTotalQuantity();   // celkový počet kusů
$empty = $cart->isEmpty();            // je košík prázdný?

// Vyprázdnit košík (po odeslání objednávky)
$cart->clear();
```

### Doprava a platba

```php
$shippingRepo = new ShippingMethodRepository();
$paymentRepo = new PaymentMethodRepository();

// Všechny způsoby dopravy (pro formulář s výběrem)
$shippingMethods = $shippingRepo->getAll();

// Všechny způsoby platby
$paymentMethods = $paymentRepo->getAll();

// Konkrétní způsob podle ID
$shipping = $shippingRepo->getById(2); // Zásilkovna
$shipping->name;         // "Zásilkovna"
$shipping->price;        // 69.0
$shipping->deliveryDays; // "2–3 pracovní dny"
$shipping->isFree();     // false
```

### Vytvoření objednávky

```php
$customerRepo = new CustomerRepository();
$orderRepo = new OrderRepository();
$cart = new Cart();

// 1) Vytvořit zákazníka
$customer = $customerRepo->create(
    firstName: 'Jan',
    lastName: 'Novák',
    email: 'jan@email.cz',
    phone: '+420 777 123 456',
    street: 'Sportovní 42',
    city: 'Praha',
    zip: '11000',
);

// 2) Vytvořit objednávku z položek košíku
//    Celková cena se vypočítá automaticky (zboží + doprava + platba)
$order = $orderRepo->create(
    customerId: $customer->id,
    shippingMethodId: 2,   // Zásilkovna
    paymentMethodId: 1,    // Kartou online
    note: 'Prosím zabalit jako dárek.',
    cartItems: $cart->getItems(),
);

// 3) Vyprázdnit košík
$cart->clear();

// $order->id obsahuje číslo nové objednávky
// $order->totalPrice obsahuje celkovou cenu včetně dopravy a platby
```

### Výpis dat v HTML šabloně

```php
<?php foreach ($products as $product): ?>
    <div class="product-card">
        <img src="<?= htmlspecialchars($product->image) ?>"
             alt="<?= htmlspecialchars($product->name) ?>">
        <h2><?= htmlspecialchars($product->name) ?></h2>
        <p><?= number_format($product->price, 0, ',', ' ') ?> Kč</p>
        <p><?= htmlspecialchars($product->categoryName) ?></p>

        <?php if ($product->hasDiscount()): ?>
            <span>Sleva <?= $product->getDiscountPercent() ?> %</span>
        <?php endif; ?>
    </div>
<?php endforeach; ?>
```

## Obrázky

Projekt obsahuje **placeholder obrázky** (SVG) ve složce `assets/images/`:

```
assets/images/
├── kategorie/    ← obrázky kategorií (6 ks)
└── produkty/     ← obrázky produktů + galerie (41 ks)
```

Placeholdery jsou barevné SVG soubory s názvem produktu/kategorie. **Nahraďte je vlastními obrázky** (JPG, PNG, WebP) a upravte cesty v `database/init.php`. Cesty v databázi jsou relativní k webovému rootu (složce `projekt/`), např. `assets/images/produkty/pegasus-41.svg`.

## Vzorová data

Databáze obsahuje **6 kategorií** a **30 produktů** se sportovní tématikou. Data si můžete upravit v souboru `database/init.php` a poté znovu spustit:

```bash
php projekt/database/init.php
```

## Bezpečnost – CSRF ochrana formulářů

Formuláře, které mění data (přidání do košíku, odeslání objednávky), by měly být chráněny proti CSRF útokům. CSRF (Cross-Site Request Forgery) je útok, kdy škodlivá stránka přiměje prohlížeč uživatele odeslat formulář na váš web bez jeho vědomí.

Ochrana spočívá v tom, že při zobrazení formuláře vygenerujete náhodný token, uložíte ho do session a vložíte do formuláře jako skryté pole. Při zpracování pak ověříte, že token souhlasí:

```php
// Generování tokenu (na začátku stránky s formulářem)
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Vložení do formuláře
<input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">

// Ověření při zpracování
if (!hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'] ?? '')) {
    http_response_code(403);
    exit('Neplatný bezpečnostní token.');
}
```

Vzorové soubory `ukazka.php` a `produkt.php` CSRF ochranu neobsahují, aby zůstaly co nejjednodušší. **Ve svých stránkách ji ale implementujte** – zejména u košíku a objednávkového formuláře.

## Databázové tabulky

| Tabulka | Popis |
|---------|-------|
| `categories` | Kategorie produktů (název, slug, obrázek, popis) |
| `products` | Produkty (název, cena, popis, obrázek, příznak doporučený) |
| `product_images` | Galerie obrázků produktu |
| `product_parameters` | Parametry produktu – `type`: `'select'` = volitelný (dropdown), `'info'` = pouze informační |
| `shipping_methods` | Číselník způsobů dopravy (název, cena, doba doručení) |
| `payment_methods` | Číselník způsobů platby (název, cena/poplatek) |
| `customers` | Zákazníci (jméno, email, telefon, adresa) |
| `orders` | Objednávky (zákazník, doprava, platba, cena dopravy/platby, celková cena, stav) |
| `order_items` | Položky objednávky (produkt, varianta, množství, jednotková cena) |
