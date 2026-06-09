<?php
declare(strict_types=1);

/**
 * Lekce 12: Práce se soubory - Zadání (Kontaktní formulář s CSV)
 * Spuštění: php -S 0.0.0.0:8080 -t public  →  otevři /12-soubory-zadani.php
 *
 * Úkol: Vytvoř kontaktní formulář, který ukládá zprávy do CSV souboru.
 * CSV (Comma-Separated Values) je formát, kde jsou hodnoty oddělené středníkem
 * a dají se otevřít v Excelu nebo Google Sheets.
 *
 * Podívej se nejdřív na příklad: 12-soubory-priklad.php
 */

// Cesta k datovému souboru
$dataDir = __DIR__ . '/../data';
$soubor = $dataDir . '/kontakty.csv';

// Vytvoříme složku data/, pokud neexistuje
if (!is_dir($dataDir)) {
    mkdir($dataDir, 0777, true);
}

// Pokud soubor neexistuje, vytvoříme ho s hlavičkou CSV
if (!file_exists($soubor)) {
    // Hlavička = první řádek CSV souboru, popisuje sloupce
    file_put_contents($soubor, "Datum;Jmeno;Email;Predmet;Zprava;Priorita\n");
}

$priority = [
    'nizka' => 'Nízká',
    'normalni' => 'Normální',
    'vysoka' => 'Vysoká',
];

$predmety = [
    'dotaz' => 'Obecný dotaz',
    'chyba' => 'Nahlášení chyby',
    'napad' => 'Nápad na zlepšení',
    'spoluprace' => 'Nabídka spolupráce',
];

$formData = [
    'jmeno' => '',
    'email' => '',
    'predmet' => '',
    'priorita' => 'normalni',
    'zprava' => '',
];

$chyby = [];
$ulozeno = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Akce: smazat vše (ponechat hlavičku)
    if (isset($_POST['akce']) && $_POST['akce'] === 'smazat') {
        file_put_contents($soubor, "Datum;Jmeno;Email;Predmet;Zprava;Priorita\n");
        header('Location: ' . $_SERVER['REQUEST_URI']);
        exit;
    }

    // TODO: Přečti data z formuláře do $formData
    // $formData['jmeno'] = trim($_POST['jmeno'] ?? '');
    // $formData['email'] = ...
    // $formData['predmet'] = ...
    // $formData['priorita'] = ...
    // $formData['zprava'] = ...


    // TODO: Validace
    // - Jméno: povinné, max 50 znaků
    // - Email: povinný, platný formát (filter_var + FILTER_VALIDATE_EMAIL)
    // - Předmět: povinný, whitelist ($predmety) – použij array_key_exists()
    // - Priorita: whitelist ($priority) – použij array_key_exists()
    // - Zpráva: povinná, min 10 znaků, max 1000 znaků


    // TODO: Uložení do CSV souboru (pokud nejsou chyby)
    //
    // CSV formát: hodnoty oddělené středníkem, každý záznam na novém řádku
    //
    // Kroky:
    // 1. Připrav datum: $datum = date('d.m.Y H:i');
    //
    // 2. Ošetři středníky v textu – pokud by jméno nebo zpráva obsahovaly
    //    středník, rozbilo by to CSV. Nahraď středníky čárkou:
    //    $bezpecneJmeno = str_replace(';', ',', $formData['jmeno']);
    //    $bezpecnaZprava = str_replace(';', ',', $formData['zprava']);
    //
    // 3. Sestav řádek CSV:
    //    $radek = $datum . ';' . $bezpecneJmeno . ';' . $formData['email'] . ';'
    //             . $predmety[$formData['predmet']] . ';' . $bezpecnaZprava . ';'
    //             . $priority[$formData['priorita']] . "\n";
    //
    // 4. Zapiš na konec souboru:
    //    file_put_contents($soubor, $radek, FILE_APPEND);
    //
    // 5. Nastav $ulozeno = true a resetuj $formData

    if (empty($chyby)) {
        // TODO: Ulož do souboru (viz kroky výše)
    }
}

// TODO: Načtení zpráv ze souboru
//
// Kroky:
// 1. Načti soubor jako pole řádků:
//    $radky = file($soubor, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
//
// 2. Odstraň první řádek (hlavičku):
//    $hlavicka = array_shift($radky);
//
// 3. Projdi řádky a rozparsuj je:
//    $zpravy = [];
//    foreach ($radky as $radek) {
//        $casti = explode(';', $radek, 6);
//        if (count($casti) === 6) {
//            $zpravy[] = [
//                'datum' => $casti[0],
//                'jmeno' => $casti[1],
//                'email' => $casti[2],
//                'predmet' => $casti[3],
//                'zprava' => $casti[4],
//                'priorita' => $casti[5],
//            ];
//        }
//    }
//
// 4. Otoč pořadí (nejnovější nahoře):
//    $zpravy = array_reverse($zpravy);

$zpravy = []; // TODO: Nahraď načtením ze souboru (viz výše)

?>
<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lekce 12: Kontaktní formulář - Zadání</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .error-msg { color: #c0392b; font-size: 12px; margin: 2px 0 8px; }
        .required::after { content: " *"; color: #c0392b; }
        .zprava-card { background: white; padding: 16px; border-radius: 8px; margin: 12px 0; box-shadow: 0 2px 4px rgba(0,0,0,0.1); border-left: 4px solid #4F5B93; }
        .zprava-hlavicka { display: flex; justify-content: space-between; flex-wrap: wrap; gap: 8px; margin-bottom: 8px; font-size: 13px; color: #666; }
        .zprava-jmeno { font-weight: bold; color: #4F5B93; }
        .zprava-text { line-height: 1.5; margin-top: 8px; }
        .priorita-nizka { border-left-color: #27ae60; }
        .priorita-normalni { border-left-color: #f39c12; }
        .priorita-vysoka { border-left-color: #c0392b; }
        .badge { display: inline-block; padding: 2px 8px; border-radius: 10px; font-size: 11px; font-weight: bold; color: white; }
        .badge-nizka { background: #27ae60; }
        .badge-normalni { background: #f39c12; }
        .badge-vysoka { background: #c0392b; }
    </style>
</head>
<body>
    <h1>Lekce 12: Kontaktní formulář - Zadání</h1>
    <p><a href="index.php">&larr; Zpět</a> | <a href="12-soubory-priklad.php">&larr; Příklad (návštěvní kniha)</a></p>

    <div class="task">
        <h3>Úkol</h3>
        <p>Dokonči kontaktní formulář, který ukládá zprávy do <strong>CSV souboru</strong>. Doplň:</p>
        <ol>
            <li>Čtení dat z formuláře</li>
            <li>Validaci všech polí (povinnost, formát emailu, whitelist, délka)</li>
            <li>Uložení do CSV souboru (<code>file_put_contents</code> s <code>FILE_APPEND</code>)</li>
            <li>Načtení zpráv ze souboru a jejich zobrazení</li>
        </ol>
        <p>Klíčový rozdíl oproti příkladu: používáme <strong>CSV formát</strong> (hodnoty oddělené středníkem)
            a soubor má <strong>hlavičku</strong> (první řádek popisuje sloupce).</p>
    </div>

    <div class="info">
        <strong>Co je CSV?</strong> Textový soubor, kde každý řádek je záznam a hodnoty jsou oddělené
        oddělovačem (středník nebo čárka). Dá se otevřít v Excelu, Google Sheets nebo LibreOffice Calc.
        Hlavička (první řádek) popisuje, co je v jakém sloupci.
    </div>

    <?php if ($ulozeno): ?>
        <div class="success">
            Zpráva byla uložena do CSV souboru!
        </div>
    <?php endif; ?>

    <!-- FORMULÁŘ -->
    <form method="POST" action="">
        <div class="card">
            <h2>Kontaktní formulář</h2>

            <label for="jmeno" class="required">Jméno</label>
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

            <label for="predmet" class="required">Předmět</label>
            <select id="predmet" name="predmet">
                <option value="">-- Vyber předmět --</option>
                <?php foreach ($predmety as $kod => $nazev): ?>
                    <option value="<?= $kod ?>" <?= $formData['predmet'] === $kod ? 'selected' : '' ?>>
                        <?= htmlspecialchars($nazev) ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <?php if (isset($chyby['predmet'])): ?>
                <div class="error-msg"><?= htmlspecialchars($chyby['predmet']) ?></div>
            <?php endif; ?>

            <label>Priorita</label>
            <div class="radio-group">
                <?php foreach ($priority as $kod => $nazev): ?>
                    <label>
                        <input type="radio" name="priorita" value="<?= $kod ?>"
                               <?= $formData['priorita'] === $kod ? 'checked' : '' ?>>
                        <?= htmlspecialchars($nazev) ?>
                    </label>
                <?php endforeach; ?>
            </div>
            <?php if (isset($chyby['priorita'])): ?>
                <div class="error-msg"><?= htmlspecialchars($chyby['priorita']) ?></div>
            <?php endif; ?>

            <label for="zprava" class="required">Zpráva</label>
            <textarea id="zprava" name="zprava" rows="5" placeholder="Napiš svou zprávu (min. 10 znaků)..."><?= htmlspecialchars($formData['zprava']) ?></textarea>
            <?php if (isset($chyby['zprava'])): ?>
                <div class="error-msg"><?= htmlspecialchars($chyby['zprava']) ?></div>
            <?php endif; ?>

            <button type="submit">Odeslat zprávu</button>
        </div>
    </form>

    <!-- PŘIJATÉ ZPRÁVY -->
    <div class="card">
        <h2>Přijaté zprávy (<?= count($zpravy) ?>)</h2>

        <?php if (empty($zpravy)): ?>
            <p class="empty">Zatím žádné zprávy.</p>
        <?php else: ?>
            <?php foreach ($zpravy as $z): ?>
                <?php
                // Určení CSS třídy podle priority
                $prioritaClass = 'normalni';
                if (mb_strtolower($z['priorita']) === 'nízká') {
                    $prioritaClass = 'nizka';
                } elseif (mb_strtolower($z['priorita']) === 'vysoká') {
                    $prioritaClass = 'vysoka';
                }
                ?>
                <div class="zprava-card priorita-<?= $prioritaClass ?>">
                    <div class="zprava-hlavicka">
                        <span>
                            <span class="zprava-jmeno"><?= htmlspecialchars($z['jmeno']) ?></span>
                            &middot; <?= htmlspecialchars($z['email']) ?>
                        </span>
                        <span>
                            <span class="badge badge-<?= $prioritaClass ?>"><?= htmlspecialchars($z['priorita']) ?></span>
                            &middot; <?= htmlspecialchars($z['datum']) ?>
                        </span>
                    </div>
                    <div><strong><?= htmlspecialchars($z['predmet']) ?></strong></div>
                    <div class="zprava-text"><?= nl2br(htmlspecialchars($z['zprava'])) ?></div>
                </div>
            <?php endforeach; ?>

            <form method="POST" action="" style="margin-top: 16px;">
                <input type="hidden" name="akce" value="smazat">
                <button type="submit" class="btn-danger">Smazat všechny zprávy</button>
            </form>
        <?php endif; ?>
    </div>

    <!-- NÁPOVĚDA: FUNKCE PRO PRÁCI SE SOUBORY -->
    <div class="card">
        <h2>Přehled funkcí</h2>
        <table>
            <thead>
                <tr>
                    <th>Funkce</th>
                    <th>Co dělá</th>
                    <th>Kdy použít</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><code>file_put_contents()</code></td>
                    <td>Zapíše text do souboru</td>
                    <td>Zápis celého obsahu nebo přidání na konec (<code>FILE_APPEND</code>)</td>
                </tr>
                <tr>
                    <td><code>file_get_contents()</code></td>
                    <td>Načte celý soubor jako řetězec</td>
                    <td>Když potřebuješ celý obsah najednou</td>
                </tr>
                <tr>
                    <td><code>file()</code></td>
                    <td>Načte soubor jako pole řádků</td>
                    <td>Když chceš zpracovat soubor řádek po řádku</td>
                </tr>
                <tr>
                    <td><code>file_exists()</code></td>
                    <td>Zjistí, jestli soubor existuje</td>
                    <td>Kontrola před čtením</td>
                </tr>
                <tr>
                    <td><code>is_dir()</code></td>
                    <td>Zjistí, jestli existuje složka</td>
                    <td>Kontrola před zápisem</td>
                </tr>
                <tr>
                    <td><code>mkdir()</code></td>
                    <td>Vytvoří složku</td>
                    <td>Příprava adresáře pro data</td>
                </tr>
                <tr>
                    <td><code>explode()</code></td>
                    <td>Rozdělí řetězec podle oddělovače</td>
                    <td>Parsování CSV řádku</td>
                </tr>
                <tr>
                    <td><code>str_replace()</code></td>
                    <td>Nahradí text v řetězci</td>
                    <td>Ošetření speciálních znaků</td>
                </tr>
            </tbody>
        </table>
    </div>

    <div class="bonus">
        <h3>Bonus</h3>
        <ul>
            <li>Přidej filtrování zpráv podle priority (zobraz jen "vysoké")</li>
            <li>Přidej počítadlo zpráv podle předmětu (kolik dotazů, kolik chyb...)</li>
            <li>Přidej tlačítko "Stáhnout CSV" – nabídne soubor ke stažení do počítače</li>
            <li>Přidej mazání jednotlivých zpráv (přepiš soubor bez smazaného řádku)</li>
        </ul>
    </div>
</body>
</html>
