<?php
declare(strict_types=1);

// Načtení databáze a košíku
require_once __DIR__ . '/src/bootstrap.php';

$cart = new Cart();
$cartItems = $cart->getItems(); // Vytáhne všechny produkty v košíku
$cartTotal = $cart->getTotalPrice(); // Spočítá celkovou cenu
$cartItemCount = $cart->getTotalQuantity();

// Funkce pro odstranění položky z košíku
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['remove_item'])) {
    $cart->remove((string)$_POST['cart_item_id']);
    header('Location: ' . $_SERVER['REQUEST_URI']);
    exit;
}

$pageTitle = 'Košík – Adresa - HCV Fanshop';

// Vložení hlavičky
require __DIR__ . '/partials/header.php';
?>

<div class="container" style="padding-top: 18px;">
    <nav aria-label="Breadcrumb" class="breadcrumb">
        <a href="index.php">Home</a>
        <span aria-hidden="true">›</span>
        <span>Košík – krok 1</span>
    </nav>
</div>

<main>
    <section class="section">
        <div class="container stack">
            <h1>Košík – krok 1: Adresa</h1>
            <div class="grid grid-2">
                <div class="card card-pad">
                    <form action="kosik-krok2.php" class="stack" method="post">
                        <div class="form-grid">
                            <div class="field">
                                <label for="jmeno">Jméno a příjmení</label>
                                <input id="jmeno" name="jmeno" placeholder="Jan Novák" required=""/>
                            </div>
                            <div class="field">
                                <label for="email">Email</label>
                                <input id="email" name="email" placeholder="jan@novak.cz" required="" type="email"/>
                            </div>
                            <div class="field">
                                <label for="telefon">Telefon</label>
                                <input id="telefon" name="telefon" placeholder="+420 777 123 456" type="tel"/>
                            </div>
                            <div class="field">
                                <label for="adresa">Adresa</label>
                                <input id="adresa" name="adresa" placeholder="Ulice 12, Praha" required=""/>
                            </div>
                            <div class="field full">
                                <label for="poznamka">Poznámka</label>
                                <textarea id="poznamka" name="poznamka" placeholder="Např. zvonek, patro…" rows="4"></textarea>
                            </div>
                        </div>
                        <div class="between wrap">
                            <a class="btn" href="produkty.php">Zpět na produkty</a>
                            
                            <?php if ($cartItemCount > 0): ?>
                                <button class="btn btn-primary" type="submit">Pokračovat</button>
                            <?php else: ?>
                                <button class="btn" type="button" disabled>Košík je prázdný</button>
                            <?php endif; ?>
                        </div>
                    </form>
                </div>
                <aside class="card">
                    <div class="card-pad">
                        <h3>Shrnutí košíku</h3>
                        <table aria-label="Položky v košíku" class="table">
                            <thead>
                                <tr>
                                    <th>Položka</th>
                                    <th>Cena</th>
                                    <th>Počet</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($cartItems)): ?>
                                    <tr>
                                        <td colspan="4" style="text-align: center; padding: 20px;">Váš košík je prázdný.</td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($cartItems as $itemId => $item): ?>
                                        <tr>
                                            <td>
                                                <strong><?= htmlspecialchars($item['name']) ?></strong>
                                                <?php if (!empty($item['variant'])): ?>
                                                    <br><small><?= htmlspecialchars($item['variant']) ?></small>
                                                <?php endif; ?>
                                            </td>
                                            <td><?= number_format($item['unit_price'], 0, ',', ' ') ?> Kč</td>
                                            <td><?= $item['quantity'] ?> ks</td>
                                            <td>
                                                <form method="post" style="margin: 0;">
                                                    <input type="hidden" name="cart_item_id" value="<?= htmlspecialchars((string)$itemId) ?>">
                                                    <button type="submit" name="remove_item" class="btn" style="padding: 2px 8px; font-size: 12px; color: red;">X</button>
                                                </form>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                    <div class="total-row">
                        <strong>Celkem</strong>
                        <strong><?= number_format($cartTotal, 0, ',', ' ') ?> Kč</strong>
                    </div>
                </aside>
            </div>
        </div>
    </section>
</main>

<?php require __DIR__ . '/partials/footer.php'; ?>