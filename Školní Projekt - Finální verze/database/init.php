<?php

declare(strict_types=1);

/**
 * Inicializace databáze – vytvoří tabulky a naplní vzorovými daty.
 *
 * Spuštění: php database/init.php
 *
 * POZOR: Smaže existující databázi a vytvoří novou!
 */

$dbPath = __DIR__ . '/eshop.db';

// Smazat existující databázi
if (file_exists($dbPath)) {
    unlink($dbPath);
    echo "Stará databáze smazána.\n";
}

$db = new PDO('sqlite:' . $dbPath, options: [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
]);

$db->exec('PRAGMA journal_mode = WAL');
$db->exec('PRAGMA foreign_keys = ON');

// ============================================================
// Vytvoření tabulek
// ============================================================

$db->exec('
    CREATE TABLE categories (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        name TEXT NOT NULL,
        slug TEXT NOT NULL UNIQUE,
        image TEXT NOT NULL DEFAULT "",
        description TEXT NOT NULL DEFAULT ""
    )
');

$db->exec('
    CREATE TABLE products (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        category_id INTEGER NOT NULL,
        name TEXT NOT NULL,
        slug TEXT NOT NULL UNIQUE,
        price REAL NOT NULL,
        original_price REAL,
        description TEXT NOT NULL DEFAULT "",
        image TEXT NOT NULL DEFAULT "",
        featured INTEGER NOT NULL DEFAULT 0,
        created_at TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (category_id) REFERENCES categories(id)
    )
');

$db->exec('
    CREATE TABLE product_images (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        product_id INTEGER NOT NULL,
        image TEXT NOT NULL,
        sort_order INTEGER NOT NULL DEFAULT 0,
        FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
    )
');

$db->exec('
    CREATE TABLE product_parameters (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        product_id INTEGER NOT NULL,
        name TEXT NOT NULL,
        value TEXT NOT NULL,
        type TEXT NOT NULL DEFAULT "info",
        FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
    )
');

$db->exec('
    CREATE TABLE customers (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        first_name TEXT NOT NULL,
        last_name TEXT NOT NULL,
        email TEXT NOT NULL,
        phone TEXT NOT NULL DEFAULT "",
        street TEXT NOT NULL DEFAULT "",
        city TEXT NOT NULL DEFAULT "",
        zip TEXT NOT NULL DEFAULT "",
        created_at TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP
    )
');

$db->exec('
    CREATE TABLE shipping_methods (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        name TEXT NOT NULL,
        price REAL NOT NULL DEFAULT 0,
        delivery_days TEXT NOT NULL DEFAULT ""
    )
');

$db->exec('
    CREATE TABLE payment_methods (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        name TEXT NOT NULL,
        price REAL NOT NULL DEFAULT 0
    )
');

$db->exec('
    CREATE TABLE orders (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        customer_id INTEGER NOT NULL,
        shipping_method_id INTEGER NOT NULL,
        payment_method_id INTEGER NOT NULL,
        shipping_price REAL NOT NULL DEFAULT 0,
        payment_price REAL NOT NULL DEFAULT 0,
        note TEXT NOT NULL DEFAULT "",
        total_price REAL NOT NULL,
        status TEXT NOT NULL DEFAULT "new",
        created_at TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (customer_id) REFERENCES customers(id),
        FOREIGN KEY (shipping_method_id) REFERENCES shipping_methods(id),
        FOREIGN KEY (payment_method_id) REFERENCES payment_methods(id)
    )
');

$db->exec('
    CREATE TABLE order_items (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        order_id INTEGER NOT NULL,
        product_id INTEGER NOT NULL,
        product_name TEXT NOT NULL,
        variant TEXT NOT NULL DEFAULT "",
        quantity INTEGER NOT NULL,
        unit_price REAL NOT NULL,
        FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
        FOREIGN KEY (product_id) REFERENCES products(id)
    )
');

echo "Tabulky vytvořeny.\n";

// ============================================================
// Vzorová data – téma: HCV Fanshop
// ============================================================

// Kategorie (Z tvého HTML)
$categories = [
    ['Mikiny a Bundy', 'mikiny-bundy', '', 'Pohodlné oblečení do chladného počasí.'],
    ['Trička', 'tricka', '', 'Základ šatníku každého správného fanouška.'],
    ['Šály', 'saly', '', 'Klubové šály na stadion i do města.'],
    ['Čepice', 'cepice', '', 'Kšiltovky a zimní čepice s logem.'],
    ['Dresy', 'dresy', '', 'Oficiální domácí i venkovní dresy.'],
    ['Doplňky', 'doplnky', '', 'Drobnosti, co udělají radost.'],
];

$catStmt = $db->prepare('INSERT INTO categories (name, slug, image, description) VALUES (?, ?, ?, ?)');
foreach ($categories as $cat) {
    $catStmt->execute($cat);
}
echo "Kategorie vloženy.\n";

// Produkty (Z tvého HTML)
$products = [
    // Mikiny a Bundy (category_id = 1)
    [1, 'Mikina NEON Classic', 'mikina-classic', 1190, NULL, 'Pohodlná mikina z měkké bavlny, ideální na trénink i do školy.', '', 1],
    [1, 'Bunda NEON Windbreaker', 'bunda-windbreaker', 1790, NULL, 'Lehká větrovka s voděodolnou úpravou – když venku fouká a prší.', '', 0],
    
    // Trička (category_id = 2)
    [2, 'Tričko NEON Logo', 'tricko-logo', 590, NULL, 'Jednoduchý střih, výrazné logo. Základ do šatníku fanouška.', '', 1],
    [2, 'Tričko NEON Oversize', 'tricko-oversize', 690, NULL, 'Volnější fit pro maximální pohodlí. Skvělé k džínům i teplákům.', '', 0],
    
    // Šály (category_id = 3)
    [3, 'Šála NEON City', 'sala-city', 390, NULL, 'Pletená šála s klubovými barvami. Zahřeje a vypadá dobře.', '', 1],
    [3, 'Šála NEON UltraWarm', 'sala-ultrawarm', 450, NULL, 'Extra hřejivý materiál na zimu. Ideální na stadion i do města.', '', 0],
    
    // Čepice (category_id = 4)
    [4, 'Čepice NEON Beanie', 'cepice-beanie', 350, NULL, 'Klasická zimní čepice s nášivkou. Sedí skoro každému.', '', 0],
    [4, 'Kšiltovka NEON Snapback', 'ksiltovka-snapback', 490, NULL, 'Nastavitelný snapback, pevný kšilt. Top na jaro a léto.', '', 0],
    
    // Dresy (category_id = 5)
    [5, 'Dres NEON Home', 'dres-home', 1290, NULL, 'Domácí dres v klubových barvách. Pro fandění i sport.', '', 1],
    [5, 'Dres NEON Away', 'dres-away', 1290, NULL, 'Venkovní varianta s čistým designem. Lehké a prodyšné.', '', 0],
    
    // Doplňky (category_id = 6)
    [6, 'Klíčenka NEON', 'klicenka-neon', 149, NULL, 'Malý detail, co dělá radost. Kovová klíčenka s logem.', '', 0],
    [6, 'Batoh NEON Daypack', 'batoh-daypack', 990, NULL, 'Praktický batoh na školu i trénink. Kapsa na notebook + láhev.', '', 0],
];

$prodStmt = $db->prepare('
    INSERT INTO products (category_id, name, slug, price, original_price, description, image, featured)
    VALUES (?, ?, ?, ?, ?, ?, ?, ?)
');

foreach ($products as $prod) {
    $prodStmt->execute($prod);
}
echo "Produkty vloženy (" . count($products) . ").\n";


// Parametry produktů (např. výběr velikosti pro oblečení)
$parameters = [
    // Výběr velikosti u Triček a Dresů (product_id 3, 4, 9, 10)
    [3, 'Velikost', 'S, M, L, XL, XXL', 'select'],
    [4, 'Velikost', 'S, M, L, XL, XXL', 'select'],
    [9, 'Velikost', 'S, M, L, XL, XXL', 'select'],
    [10, 'Velikost', 'S, M, L, XL, XXL', 'select'],
];

$paramStmt = $db->prepare('INSERT INTO product_parameters (product_id, name, value, type) VALUES (?, ?, ?, ?)');
foreach ($parameters as $param) {
    $paramStmt->execute($param);
}
echo "Parametry vloženy.\n";

// Způsoby dopravy
$shippingMethods = [
    ['Osobní odběr Ostrava', 0, 'Ihned k vyzvednutí'],
    ['Zásilkovna', 69, '2–3 pracovní dny'],
    ['PPL', 99, '1–2 pracovní dny'],
];

$shipStmt = $db->prepare('INSERT INTO shipping_methods (name, price, delivery_days) VALUES (?, ?, ?)');
foreach ($shippingMethods as $method) {
    $shipStmt->execute($method);
}
echo "Způsoby dopravy vloženy.\n";

// Způsoby platby
$paymentMethods = [
    ['Kartou online', 0],
    ['Bankovním převodem', 0],
    ['Dobírkou', 39],
];

$payStmt = $db->prepare('INSERT INTO payment_methods (name, price) VALUES (?, ?)');
foreach ($paymentMethods as $method) {
    $payStmt->execute($method);
}
echo "Způsoby platby vloženy.\n";

// Vzorový zákazník a objednávka
$db->exec('
    INSERT INTO customers (first_name, last_name, email, phone, street, city, zip)
    VALUES ("Jan", "Novák", "jan.novak@email.cz", "+420 777 123 456", "Sportovní 42", "Ostrava", "70200")
');

$db->exec('
    INSERT INTO orders (customer_id, shipping_method_id, payment_method_id, shipping_price, payment_price, note, total_price, status)
    VALUES (1, 2, 1, 69, 0, "Prosím zabalit jako dárek.", 1849, "new")
');

$db->exec('
    INSERT INTO order_items (order_id, product_id, product_name, variant, quantity, unit_price)
    VALUES
        (1, 3, "Tričko NEON Logo", "Velikost: L", 1, 590),
        (1, 9, "Dres NEON Home", "Velikost: L", 1, 1290)
');

// Indexy pro rychlejší vyhledávání
$db->exec('CREATE INDEX idx_products_category ON products(category_id)');
$db->exec('CREATE INDEX idx_products_slug ON products(slug)');
$db->exec('CREATE INDEX idx_products_featured ON products(featured)');
$db->exec('CREATE INDEX idx_categories_slug ON categories(slug)');
$db->exec('CREATE INDEX idx_order_items_order ON order_items(order_id)');
$db->exec('CREATE INDEX idx_product_images_product ON product_images(product_id)');
$db->exec('CREATE INDEX idx_product_params_product ON product_parameters(product_id)');

echo "\nDatabáze úspěšně inicializována!\n";
echo "Soubor: $dbPath\n";