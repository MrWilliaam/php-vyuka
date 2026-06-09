<?php
declare(strict_types=1);

/**
 * Lekce 11b: Objednávkový formulář s výpočty - Příklad
 * Spuštění: php -S 0.0.0.0:8080 -t public  →  otevři /11b-objednavka-priklad.php
 *
 * Tento příklad ukazuje:
 * - Výběr produktů a množství
 * - Výpočet ceny, slevy a DPH
 * - Zpracování více produktů najednou (pole v formuláři)
 * - Shrnutí objednávky
 */

session_start();

if (!isset($_SESSION['objednavky'])) {
    $_SESSION['objednavky'] = [];
}

// Katalog produktů (v reálu by byl v databázi)
$produkty = [
    'tshirt' => ['nazev' => 'Tričko PHP', 'cena' => 450],
    'hoodie' => ['nazev' => 'Mikina Developer', 'cena' => 890],
    'cap' => ['nazev' => 'Kšiltovka Coder', 'cena' => 320],
    'sticker' => ['nazev' => 'Samolepky (5ks)', 'cena' => 80],
    'mug' => ['nazev' => 'Hrnek "It works on my machine"', 'cena' => 250],
];

// Dostupné slevy
$slevoveKody = [
    'STUDENT10' => 10,  // 10 %
    'PHP20' => 20,       // 20 %
];

$sazbyDph = [
    21 => '21 % (standard)',
    12 => '12 % (snížená)',
];

// Stav formuláře
$formData = [
    'jmeno' => '',
    'email' => '',
    'adresa' => '',
    'mesto' => '',
    'psc' => '',
    'slevovy_kod' => '',
    'dph_sazba' => 21,
    'mnozstvi' => [],
];

$chyby = [];
$objednavka = null; // výsledek po úspěšném odeslání

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['akce']) && $_POST['akce'] === 'smazat') {
        $_SESSION['objednavky'] = [];
        header('Location: ' . $_SERVER['REQUEST_URI']);
        exit;
    }

    // Čtení dat
    $formData['jmeno'] = trim($_POST['jmeno'] ?? '');
    $formData['email'] = trim($_POST['email'] ?? '');
    $formData['adresa'] = trim($_POST['adresa'] ?? '');
    $formData['mesto'] = trim($_POST['mesto'] ?? '');
    $formData['psc'] = trim($_POST['psc'] ?? '');
    $formData['slevovy_kod'] = trim($_POST['slevovy_kod'] ?? '');
    $formData['dph_sazba'] = (int) ($_POST['dph_sazba'] ?? 21);
    $formData['mnozstvi'] = $_POST['mnozstvi'] ?? [];

    // Validace osobních údajů
    if ($formData['jmeno'] === '') {
        $chyby['jmeno'] = 'Jméno je povinné.';
    }
    if ($formData['email'] === '' || !filter_var($formData['email'], FILTER_VALIDATE_EMAIL)) {
        $chyby['email'] = 'Zadej platný email.';
    }
    if ($formData['adresa'] === '') {
        $chyby['adresa'] = 'Adresa je povinná.';
    }
    if ($formData['mesto'] === '') {
        $chyby['mesto'] = 'Město je povinné.';
    }
    if (!preg_match('/^\d{3}\s?\d{2}$/', $formData['psc'])) {
        $chyby['psc'] = 'PSČ musí být ve formátu 123 45.';
    }

    // Validace DPH sazby (whitelist)
    if (!array_key_exists($formData['dph_sazba'], $sazbyDph)) {
        $chyby['dph_sazba'] = 'Neplatná sazba DPH.';
    }

    // Validace množství – alespoň jeden produkt
    $objednanePolozky = [];
    foreach ($produkty as $kod => $produkt) {
        $mnozstvi = (int) ($formData['mnozstvi'][$kod] ?? 0);
        if ($mnozstvi < 0) {
            $chyby['mnozstvi'] = 'Množství nemůže být záporné.';
        }
        if ($mnozstvi > 0) {
            $objednanePolozky[$kod] = $mnozstvi;
        }
    }

    if (empty($objednanePolozky) && !isset($chyby['mnozstvi'])) {
        $chyby['mnozstvi'] = 'Vyber alespoň jeden produkt.';
    }

    // Validace slevového kódu (nepovinný)
    $sleva = 0;
    if ($formData['slevovy_kod'] !== '') {
        $kodVelky = strtoupper($formData['slevovy_kod']);
        if (!isset($slevoveKody[$kodVelky])) {
            $chyby['slevovy_kod'] = 'Neplatný slevový kód.';
        } else {
            $sleva = $slevoveKody[$kodVelky];
        }
    }

    // Výpočet a uložení
    if (empty($chyby)) {
        $meziSoucet = 0;
        $polozkyDetail = [];

        foreach ($objednanePolozky as $kod => $mnozstvi) {
            $cenaCelkem = $produkty[$kod]['cena'] * $mnozstvi;
            $meziSoucet += $cenaCelkem;
            $polozkyDetail[] = [
                'nazev' => $produkty[$kod]['nazev'],
                'cena_ks' => $produkty[$kod]['cena'],
                'mnozstvi' => $mnozstvi,
                'celkem' => $cenaCelkem,
            ];
        }

        $castkaSleva = (int) round($meziSoucet * $sleva / 100);
        $poSleve = $meziSoucet - $castkaSleva;
        $castkaDph = (int) round($poSleve * $formData['dph_sazba'] / 100);
        $celkem = $poSleve + $castkaDph;

        $objednavka = [
            'jmeno' => $formData['jmeno'],
            'email' => $formData['email'],
            'adresa' => $formData['adresa'] . ', ' . $formData['mesto'] . ' ' . $formData['psc'],
            'polozky' => $polozkyDetail,
            'meziSoucet' => $meziSoucet,
            'sleva' => $sleva,
            'castkaSleva' => $castkaSleva,
            'dph_sazba' => $formData['dph_sazba'],
            'castkaDph' => $castkaDph,
            'celkem' => $celkem,
            'datum' => date('d.m.Y H:i'),
        ];

        $_SESSION['objednavky'][] = $objednavka;
    }
}

$objednavky = $_SESSION['objednavky'];

/**
 * Formátuje cenu v Kč
 */
function formatCena(int $cena): string
{
    return number_format($cena, 0, ',', ' ') . ' Kč';
}
?>
<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lekce 11b: Objednávkový formulář</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .field-error { border-color: #c0392b !important; background: #fdecea !important; }
        .error-msg { color: #c0392b; font-size: 12px; margin: 2px 0 8px; }
        .required::after { content: " *"; color: #c0392b; }
        .product-row { display: flex; align-items: center; gap: 12px; padding: 8px 0; border-bottom: 1px solid #eee; }
        .product-row:last-child { border-bottom: none; }
        .product-name { flex: 1; }
        .product-price { width: 100px; text-align: right; color: #666; }
        .product-qty { width: 80px; }
        .product-qty input { width: 100%; margin: 0; }
        .summary-row { display: flex; justify-content: space-between; padding: 6px 0; }
        .summary-row.total { font-weight: bold; font-size: 18px; border-top: 2px solid #333; padding-top: 10px; margin-top: 6px; }
        .summary-row.discount { color: #27ae60; }
    </style>
</head>
<body>
    <h1>Lekce 11b: Objednávkový formulář</h1>
    <p><a href="index.php">&larr; Zpět</a> | <a href="11b-objednavka-zadani.php">Zadání &rarr;</a></p>

    <div class="info">
        Tento formulář ukazuje práci s <strong>více produkty</strong> (pole v formuláři),
        <strong>výpočet cen</strong>, <strong>slevy</strong> a <strong>DPH</strong>.
    </div>

    <?php if ($objednavka !== null): ?>
        <!-- SHRNUTÍ OBJEDNÁVKY (po úspěšném odeslání) -->
        <div class="success">
            <h2>Objednávka přijata!</h2>
            <p>Děkujeme, <?= htmlspecialchars($objednavka['jmeno']) ?>. Potvrzení odešleme na <?= htmlspecialchars($objednavka['email']) ?>.</p>

            <table>
                <thead>
                    <tr>
                        <th>Produkt</th>
                        <th>Cena/ks</th>
                        <th>Množství</th>
                        <th>Celkem</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($objednavka['polozky'] as $polozka): ?>
                        <tr>
                            <td><?= htmlspecialchars($polozka['nazev']) ?></td>
                            <td><?= formatCena($polozka['cena_ks']) ?></td>
                            <td><?= $polozka['mnozstvi'] ?></td>
                            <td><?= formatCena($polozka['celkem']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <div style="max-width: 300px; margin: 16px 0 0 auto;">
                <div class="summary-row">
                    <span>Mezisoučet:</span>
                    <span><?= formatCena($objednavka['meziSoucet']) ?></span>
                </div>
                <?php if ($objednavka['sleva'] > 0): ?>
                    <div class="summary-row discount">
                        <span>Sleva <?= $objednavka['sleva'] ?> %:</span>
                        <span>-<?= formatCena($objednavka['castkaSleva']) ?></span>
                    </div>
                <?php endif; ?>
                <div class="summary-row">
                    <span>DPH <?= $objednavka['dph_sazba'] ?> %:</span>
                    <span><?= formatCena($objednavka['castkaDph']) ?></span>
                </div>
                <div class="summary-row total">
                    <span>Celkem:</span>
                    <span><?= formatCena($objednavka['celkem']) ?></span>
                </div>
            </div>

            <p style="margin-top: 16px;"><a href="<?= htmlspecialchars($_SERVER['REQUEST_URI']) ?>">Nová objednávka</a></p>
        </div>
    <?php else: ?>
        <!-- FORMULÁŘ -->
        <div class="card">
            <h2>Produkty</h2>

            <?php if (isset($chyby['mnozstvi'])): ?>
                <div class="error-msg"><?= htmlspecialchars($chyby['mnozstvi']) ?></div>
            <?php endif; ?>

            <?php foreach ($produkty as $kod => $produkt): ?>
                <div class="product-row">
                    <div class="product-name"><?= htmlspecialchars($produkt['nazev']) ?></div>
                    <div class="product-price"><?= formatCena($produkt['cena']) ?></div>
                    <div class="product-qty">
                        <input type="number" name="mnozstvi[<?= $kod ?>]" min="0" max="99"
                               value="<?= (int) ($formData['mnozstvi'][$kod] ?? 0) ?>"
                               form="objednavka-form">
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <form method="POST" action="" id="objednavka-form">
            <div class="card">
                <h2>Slevový kód a DPH</h2>

                <label for="slevovy_kod">Slevový kód</label>
                <input type="text" id="slevovy_kod" name="slevovy_kod" placeholder="např. STUDENT10"
                       value="<?= htmlspecialchars($formData['slevovy_kod']) ?>">
                <?php if (isset($chyby['slevovy_kod'])): ?>
                    <div class="error-msg"><?= htmlspecialchars($chyby['slevovy_kod']) ?></div>
                <?php endif; ?>
                <div class="hint">Zkus: STUDENT10 (10 %) nebo PHP20 (20 %)</div>

                <label for="dph_sazba">Sazba DPH</label>
                <select id="dph_sazba" name="dph_sazba">
                    <?php foreach ($sazbyDph as $sazba => $popis): ?>
                        <option value="<?= $sazba ?>" <?= $formData['dph_sazba'] === $sazba ? 'selected' : '' ?>><?= htmlspecialchars($popis) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="card">
                <h2>Doručovací údaje</h2>

                <label for="jmeno" class="required">Jméno a příjmení</label>
                <input type="text" id="jmeno" name="jmeno" placeholder="Jan Novák"
                       value="<?= htmlspecialchars($formData['jmeno']) ?>">
                <?php if (isset($chyby['jmeno'])): ?>
                    <div class="error-msg"><?= htmlspecialchars($chyby['jmeno']) ?></div>
                <?php endif; ?>

                <label for="email" class="required">Email</label>
                <input type="email" id="email" name="email" placeholder="jan@email.cz"
                       value="<?= htmlspecialchars($formData['email']) ?>">
                <?php if (isset($chyby['email'])): ?>
                    <div class="error-msg"><?= htmlspecialchars($chyby['email']) ?></div>
                <?php endif; ?>

                <label for="adresa" class="required">Ulice a číslo</label>
                <input type="text" id="adresa" name="adresa" placeholder="Hlavní 123"
                       value="<?= htmlspecialchars($formData['adresa']) ?>">
                <?php if (isset($chyby['adresa'])): ?>
                    <div class="error-msg"><?= htmlspecialchars($chyby['adresa']) ?></div>
                <?php endif; ?>

                <label for="mesto" class="required">Město</label>
                <input type="text" id="mesto" name="mesto" placeholder="Praha"
                       value="<?= htmlspecialchars($formData['mesto']) ?>">
                <?php if (isset($chyby['mesto'])): ?>
                    <div class="error-msg"><?= htmlspecialchars($chyby['mesto']) ?></div>
                <?php endif; ?>

                <label for="psc" class="required">PSČ</label>
                <input type="text" id="psc" name="psc" placeholder="123 45"
                       value="<?= htmlspecialchars($formData['psc']) ?>">
                <?php if (isset($chyby['psc'])): ?>
                    <div class="error-msg"><?= htmlspecialchars($chyby['psc']) ?></div>
                <?php endif; ?>

                <button type="submit">Odeslat objednávku</button>
            </div>
        </form>
    <?php endif; ?>

    <!-- HISTORIE OBJEDNÁVEK -->
    <div class="card">
        <h2>Historie objednávek (<?= count($objednavky) ?>)</h2>

        <?php if (empty($objednavky)): ?>
            <p class="empty">Zatím žádné objednávky.</p>
        <?php else: ?>
            <table>
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Zákazník</th>
                        <th>Položky</th>
                        <th>Sleva</th>
                        <th>Celkem</th>
                        <th>Datum</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($objednavky as $i => $obj): ?>
                        <tr>
                            <td><?= $i + 1 ?></td>
                            <td><?= htmlspecialchars($obj['jmeno']) ?></td>
                            <td><?= count($obj['polozky']) ?> ks</td>
                            <td><?= $obj['sleva'] > 0 ? $obj['sleva'] . ' %' : '–' ?></td>
                            <td><?= formatCena($obj['celkem']) ?></td>
                            <td><?= htmlspecialchars($obj['datum']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <form method="POST" action="" style="margin-top: 10px;">
                <input type="hidden" name="akce" value="smazat">
                <button type="submit" class="btn-danger">Smazat historii</button>
            </form>
        <?php endif; ?>
    </div>

    <!-- VYSVĚTLENÍ -->
    <div class="card">
        <h2>Jak to funguje?</h2>
        <ol>
            <li><strong>Pole v formuláři</strong> – <code>name="mnozstvi[tshirt]"</code> vytvoří asociativní pole <code>$_POST['mnozstvi']</code> s klíči podle produktů.</li>
            <li><strong>Atribut <code>form</code></strong> – inputy pro množství jsou mimo <code>&lt;form&gt;</code>, ale díky <code>form="objednavka-form"</code> se odešlou spolu s formulářem.</li>
            <li><strong>Katalog produktů</strong> – ceny jsou definované v PHP poli (v reálu v databázi). Nikdy nevěříme ceně z formuláře – vždy bereme cenu ze serveru!</li>
            <li><strong>Slevový kód</strong> – porovnáváme pomocí <code>strtoupper()</code>, aby fungovaly i malá písmena.</li>
            <li><strong>Výpočet</strong> – mezisoučet → sleva → DPH → celkem. Zaokrouhlujeme na celé koruny.</li>
            <li><strong>Whitelist validace</strong> – sazba DPH se kontroluje proti povolené sadě (<code>array_key_exists</code>).</li>
        </ol>
    </div>
</body>
</html>
