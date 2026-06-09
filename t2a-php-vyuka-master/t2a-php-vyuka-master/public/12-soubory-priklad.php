<?php
declare(strict_types=1);

/**
 * Lekce 12: Práce se soubory - Příklad (Návštěvní kniha)
 * Spuštění: php -S 0.0.0.0:8080 -t public  →  otevři /12-soubory-priklad.php
 *
 * Tento příklad ukazuje:
 * - Zápis dat do textového souboru (file_put_contents s FILE_APPEND)
 * - Čtení dat ze souboru (file_get_contents, file)
 * - Ukládání strukturovaných dat (každý řádek = jeden záznam, oddělený |)
 * - Formulář → validace → uložení do souboru → zobrazení
 * - Rozdíl oproti $_SESSION: data přežijí restart serveru
 */

// Cesta k datovému souboru (ve složce data/ vedle public/)
$dataDir = __DIR__ . '/../data';
$soubor = $dataDir . '/navstevni-kniha.txt';

// Vytvoříme složku data/, pokud neexistuje
if (!is_dir($dataDir)) {
    mkdir($dataDir, 0777, true);
}

// Vytvoříme soubor, pokud neexistuje
if (!file_exists($soubor)) {
    file_put_contents($soubor, '');
}

$formData = [
    'jmeno' => '',
    'zprava' => '',
];

$chyby = [];
$ulozeno = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Akce: smazat vše
    if (isset($_POST['akce']) && $_POST['akce'] === 'smazat') {
        file_put_contents($soubor, '');
        header('Location: ' . $_SERVER['REQUEST_URI']);
        exit;
    }

    // Čtení dat z formuláře
    $formData['jmeno'] = trim($_POST['jmeno'] ?? '');
    $formData['zprava'] = trim($_POST['zprava'] ?? '');

    // Validace
    if ($formData['jmeno'] === '') {
        $chyby['jmeno'] = 'Jméno je povinné.';
    } elseif (mb_strlen($formData['jmeno']) > 50) {
        $chyby['jmeno'] = 'Jméno může mít maximálně 50 znaků.';
    }

    if ($formData['zprava'] === '') {
        $chyby['zprava'] = 'Zpráva je povinná.';
    } elseif (mb_strlen($formData['zprava']) > 500) {
        $chyby['zprava'] = 'Zpráva může mít maximálně 500 znaků.';
    }

    // Uložení do souboru
    if (empty($chyby)) {
        // Formát: datum|jméno|zpráva (každý záznam na novém řádku)
        $datum = date('d.m.Y H:i');
        $radek = $datum . '|' . $formData['jmeno'] . '|' . $formData['zprava'] . "\n";

        // FILE_APPEND = připojí na konec souboru (nepřepíše existující obsah)
        file_put_contents($soubor, $radek, FILE_APPEND);

        $ulozeno = true;
        $formData = ['jmeno' => '', 'zprava' => '']; // reset formuláře
    }
}

// Načtení příspěvků ze souboru
$prispevky = [];
$obsah = file_get_contents($soubor);

if ($obsah !== '' && $obsah !== false) {
    // file() načte soubor jako pole řádků
    $radky = file($soubor, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

    foreach ($radky as $radek) {
        // explode() rozdělí řádek podle oddělovače |
        $casti = explode('|', $radek, 3);

        if (count($casti) === 3) {
            $prispevky[] = [
                'datum' => $casti[0],
                'jmeno' => $casti[1],
                'zprava' => $casti[2],
            ];
        }
    }

    // Nejnovější nahoře
    $prispevky = array_reverse($prispevky);
}
?>
<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lekce 12: Práce se soubory - Příklad</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .error-msg { color: #c0392b; font-size: 12px; margin: 2px 0 8px; }
        .required::after { content: " *"; color: #c0392b; }
        .prispevek { background: white; padding: 16px; border-radius: 8px; margin: 12px 0; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        .prispevek-hlavicka { display: flex; justify-content: space-between; margin-bottom: 8px; font-size: 13px; color: #666; }
        .prispevek-jmeno { font-weight: bold; color: #4F5B93; }
        .prispevek-text { line-height: 1.5; }
        .pocitadlo { text-align: center; color: #888; font-size: 14px; margin: 10px 0; }
    </style>
</head>
<body>
    <h1>Lekce 12: Návštěvní kniha</h1>
    <p><a href="index.php">&larr; Zpět</a> | <a href="12-soubory-zadani.php">Zadání &rarr;</a></p>

    <div class="info">
        Tento příklad ukazuje <strong>ukládání dat do souboru</strong>.
        Na rozdíl od <code>$_SESSION</code> data přežijí restart serveru.
        Každý příspěvek se uloží jako řádek v textovém souboru.
    </div>

    <?php if ($ulozeno): ?>
        <div class="success">
            Příspěvek byl uložen!
        </div>
    <?php endif; ?>

    <!-- FORMULÁŘ -->
    <div class="card">
        <h2>Nový příspěvek</h2>
        <form method="POST" action="">
            <label for="jmeno" class="required">Jméno</label>
            <input type="text" id="jmeno" name="jmeno" placeholder="Tvoje jméno"
                   value="<?= htmlspecialchars($formData['jmeno']) ?>">
            <?php if (isset($chyby['jmeno'])): ?>
                <div class="error-msg"><?= htmlspecialchars($chyby['jmeno']) ?></div>
            <?php endif; ?>

            <label for="zprava" class="required">Zpráva</label>
            <textarea id="zprava" name="zprava" rows="4" placeholder="Napiš vzkaz..."><?= htmlspecialchars($formData['zprava']) ?></textarea>
            <?php if (isset($chyby['zprava'])): ?>
                <div class="error-msg"><?= htmlspecialchars($chyby['zprava']) ?></div>
            <?php endif; ?>

            <button type="submit">Odeslat příspěvek</button>
        </form>
    </div>

    <!-- PŘÍSPĚVKY -->
    <div class="card">
        <h2>Příspěvky (<?= count($prispevky) ?>)</h2>

        <?php if (empty($prispevky)): ?>
            <p class="empty">Zatím žádné příspěvky. Buď první!</p>
        <?php else: ?>
            <?php foreach ($prispevky as $p): ?>
                <div class="prispevek">
                    <div class="prispevek-hlavicka">
                        <span class="prispevek-jmeno"><?= htmlspecialchars($p['jmeno']) ?></span>
                        <span><?= htmlspecialchars($p['datum']) ?></span>
                    </div>
                    <div class="prispevek-text"><?= nl2br(htmlspecialchars($p['zprava'])) ?></div>
                </div>
            <?php endforeach; ?>

            <form method="POST" action="" style="margin-top: 16px;">
                <input type="hidden" name="akce" value="smazat">
                <button type="submit" class="btn-danger">Smazat všechny příspěvky</button>
            </form>
        <?php endif; ?>
    </div>

    <!-- INFO O SOUBORU -->
    <div class="card">
        <h2>Informace o souboru</h2>
        <table>
            <tr>
                <th>Vlastnost</th>
                <th>Hodnota</th>
            </tr>
            <tr>
                <td>Cesta k souboru</td>
                <td><code><?= htmlspecialchars(realpath($soubor) ?: $soubor) ?></code></td>
            </tr>
            <tr>
                <td>Velikost</td>
                <td><?= file_exists($soubor) ? number_format(filesize($soubor), 0, ',', ' ') . ' bajtů' : 'neexistuje' ?></td>
            </tr>
            <tr>
                <td>Počet řádků</td>
                <td><?= count($prispevky) ?></td>
            </tr>
        </table>
    </div>

    <!-- VYSVĚTLENÍ -->
    <div class="card">
        <h2>Jak to funguje?</h2>
        <ol>
            <li>
                <strong><code>file_put_contents($soubor, $text, FILE_APPEND)</code></strong>
                – Zapíše text na konec souboru. Bez <code>FILE_APPEND</code> by přepsal celý obsah.
            </li>
            <li>
                <strong><code>file_get_contents($soubor)</code></strong>
                – Načte celý obsah souboru jako jeden řetězec.
            </li>
            <li>
                <strong><code>file($soubor)</code></strong>
                – Načte soubor jako pole řádků. Každý řádek je jeden prvek pole.
                <code>FILE_IGNORE_NEW_LINES</code> odstraní <code>\n</code> na konci,
                <code>FILE_SKIP_EMPTY_LINES</code> přeskočí prázdné řádky.
            </li>
            <li>
                <strong><code>explode('|', $radek, 3)</code></strong>
                – Rozdělí řádek podle znaku <code>|</code> na maximálně 3 části (datum, jméno, zpráva).
                Třetí parametr zajistí, že <code>|</code> ve zprávě nerozbije parsování.
            </li>
            <li>
                <strong>Formát souboru:</strong> Každý řádek = jeden záznam:
                <code>07.04.2026 14:30|Jan|Ahoj, super stránka!</code>
            </li>
            <li>
                <strong>Složka <code>data/</code></strong> je mimo <code>public/</code>, takže soubor
                není přímo přístupný z prohlížeče (bezpečnost).
            </li>
        </ol>
    </div>

    <div class="card">
        <h2>Porovnání: soubor vs. session</h2>
        <table>
            <thead>
                <tr>
                    <th>Vlastnost</th>
                    <th><code>$_SESSION</code></th>
                    <th>Soubor</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Data přežijí restart serveru</td>
                    <td>Ne</td>
                    <td>Ano</td>
                </tr>
                <tr>
                    <td>Data sdílená mezi uživateli</td>
                    <td>Ne (každý má svou session)</td>
                    <td>Ano (jeden soubor pro všechny)</td>
                </tr>
                <tr>
                    <td>Vhodné pro</td>
                    <td>Dočasná data (košík, přihlášení)</td>
                    <td>Trvalá data (logy, záznamy)</td>
                </tr>
                <tr>
                    <td>Rychlost</td>
                    <td>Rychlejší</td>
                    <td>Pomalejší (čtení z disku)</td>
                </tr>
            </tbody>
        </table>
    </div>
</body>
</html>
