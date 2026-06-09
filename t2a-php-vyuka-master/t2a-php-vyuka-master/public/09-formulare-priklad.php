<?php
declare(strict_types=1);

/**
 * Lekce 9: Formuláře - Funkční příklad
 * Spuštění: php -S 0.0.0.0:8080 -t public  →  otevři /09-formulare-priklad.php
 *
 * Tento příklad ukazuje:
 * - Různé typy inputů (text, email, number, date, select, radio, checkbox, textarea)
 * - Odeslání formuláře metodou POST
 * - Zpracování dat v PHP ($_POST)
 * - Escapování výstupu (htmlspecialchars) - ochrana proti XSS
 * - Ukládání dat do pole (session/v rámci jednoho požadavku)
 * - Zobrazení dat v HTML tabulce
 */

// Session pro uchování dat mezi požadavky
session_start();

// Inicializace pole pro záznamy
if (!isset($_SESSION['osoby'])) {
    $_SESSION['osoby'] = [];
}

// Výchozí hodnoty (zachovají se ve formuláři po chybě validace)
$jmeno = '';
$email = '';
$vek = 16;
$narozeniny = '';
$pohlavi = '';
$trida = '';
$jazyky = [];
$poznamka = '';

// Zpracování odeslaného formuláře
$chyby = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['akce']) && $_POST['akce'] === 'smazat') {
        // Smazání všech záznamů + přesměrování (PRG pattern)
        $_SESSION['osoby'] = [];
        header('Location: ' . $_SERVER['REQUEST_URI']);
        exit;
    } else {
        // Čtení a čištění dat z formuláře
        $jmeno = trim($_POST['jmeno'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $vek = filter_var($_POST['vek'] ?? '', FILTER_VALIDATE_INT);
        $narozeniny = $_POST['narozeniny'] ?? '';
        $pohlavi = $_POST['pohlavi'] ?? '';
        $trida = $_POST['trida'] ?? '';
        $jazyky = $_POST['jazyky'] ?? [];
        $poznamka = trim($_POST['poznamka'] ?? '');

        // Checkbox může přijít jako cokoliv - ověříme, že je to pole
        if (!is_array($jazyky)) {
            $jazyky = [];
        }

        // Povolené hodnoty pro select, radio i checkbox (whitelist validace)
        $povoleneTridy = ['', '1.A', '2.A', '3.A', '4.A'];
        $povolenaPohlavi = ['', 'muz', 'zena', 'jine'];
        $povoleneJazyky = ['PHP', 'JavaScript', 'Python', 'C#'];

        // Odfiltrujeme nepovolené hodnoty checkboxů
        $jazyky = array_intersect($jazyky, $povoleneJazyky);

        // Validace
        if ($jmeno === '') {
            $chyby[] = 'Jméno je povinné.';
        }
        if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $chyby[] = 'Zadej platný email.';
        }
        if ($vek === false || $vek < 1 || $vek > 120) {
            $chyby[] = 'Věk musí být číslo mezi 1 a 120.';
        }
        if ($narozeniny !== '' && !preg_match('/^\d{4}-\d{2}-\d{2}$/', $narozeniny)) {
            $chyby[] = 'Neplatný formát data narození.';
        }
        if (!in_array($trida, $povoleneTridy, true)) {
            $chyby[] = 'Neplatná třída.';
        }
        if (!in_array($pohlavi, $povolenaPohlavi, true)) {
            $chyby[] = 'Neplatné pohlaví.';
        }

        // Pokud nejsou chyby, uložíme záznam
        if (empty($chyby)) {
            $_SESSION['osoby'][] = [
                'jmeno' => $jmeno,
                'email' => $email,
                'vek' => $vek,
                'narozeniny' => $narozeniny,
                'pohlavi' => $pohlavi,
                'trida' => $trida,
                'jazyky' => implode(', ', $jazyky),
                'poznamka' => $poznamka,
            ];

            // PRG pattern: po úspěšném uložení přesměrujeme,
            // aby refresh stránky neodeslal formulář znovu
            header('Location: ' . $_SERVER['REQUEST_URI']);
            exit;
        }
    }
}

$osoby = $_SESSION['osoby'];
?>
<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lekce 9: Formuláře - Příklad</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <h1>Lekce 9: Formuláře - Příklad</h1>
    <p><a href="index.php">&larr; Zpět</a> | <a href="09-formulare-zadani.php">Zadání &rarr;</a></p>

    <div class="info">
        Tento formulář ukazuje různé typy inputů. Po odeslání se data zobrazí v tabulce.
        Data se ukládají v <code>$_SESSION</code>, takže přežijí refresh stránky.
    </div>

    <!-- FORMULÁŘ -->
    <div class="card">
        <h2>Registrace studenta</h2>

        <?php if (!empty($chyby)): ?>
            <div class="errors">
                <strong>Oprav chyby:</strong>
                <ul>
                    <?php foreach ($chyby as $chyba): ?>
                        <li><?= htmlspecialchars($chyba) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <form method="POST" action="">
            <!-- TEXT -->
            <label for="jmeno">Jméno a příjmení:</label>
            <input type="text" id="jmeno" name="jmeno" placeholder="Jan Novák" required
                   value="<?= htmlspecialchars($jmeno) ?>">
            <div class="hint">type="text" - základní textový vstup</div>

            <!-- EMAIL -->
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" placeholder="jan@skola.cz" required
                   value="<?= htmlspecialchars($email) ?>">
            <div class="hint">type="email" - prohlížeč kontroluje formát emailu</div>

            <!-- NUMBER -->
            <label for="vek">Věk:</label>
            <input type="number" id="vek" name="vek" min="10" max="25"
                   value="<?= htmlspecialchars((string) $vek) ?>">
            <div class="hint">type="number" - jen čísla, atributy min/max</div>

            <!-- DATE -->
            <label for="narozeniny">Datum narození:</label>
            <input type="date" id="narozeniny" name="narozeniny"
                   value="<?= htmlspecialchars($narozeniny) ?>">
            <div class="hint">type="date" - výběr data z kalendáře</div>

            <!-- SELECT -->
            <label for="trida">Třída:</label>
            <select id="trida" name="trida">
                <option value="">-- vyber --</option>
                <option value="1.A" <?= $trida === '1.A' ? 'selected' : '' ?>>1.A</option>
                <option value="2.A" <?= $trida === '2.A' ? 'selected' : '' ?>>2.A</option>
                <option value="3.A" <?= $trida === '3.A' ? 'selected' : '' ?>>3.A</option>
                <option value="4.A" <?= $trida === '4.A' ? 'selected' : '' ?>>4.A</option>
            </select>
            <div class="hint">&lt;select&gt; - rozbalovací seznam</div>

            <!-- RADIO -->
            <label>Pohlaví:</label>
            <div class="radio-group">
                <label><input type="radio" name="pohlavi" value="muz" <?= $pohlavi === 'muz' ? 'checked' : '' ?>> Muž</label>
                <label><input type="radio" name="pohlavi" value="zena" <?= $pohlavi === 'zena' ? 'checked' : '' ?>> Žena</label>
                <label><input type="radio" name="pohlavi" value="jine" <?= $pohlavi === 'jine' ? 'checked' : '' ?>> Jiné</label>
            </div>
            <div class="hint">type="radio" - výběr jedné možnosti (stejný name)</div>

            <!-- CHECKBOX -->
            <label>Programovací jazyky:</label>
            <div class="checkbox-group">
                <label><input type="checkbox" name="jazyky[]" value="PHP" <?= in_array('PHP', $jazyky, true) ? 'checked' : '' ?>> PHP</label>
                <label><input type="checkbox" name="jazyky[]" value="JavaScript" <?= in_array('JavaScript', $jazyky, true) ? 'checked' : '' ?>> JS</label>
                <label><input type="checkbox" name="jazyky[]" value="Python" <?= in_array('Python', $jazyky, true) ? 'checked' : '' ?>> Python</label>
                <label><input type="checkbox" name="jazyky[]" value="C#" <?= in_array('C#', $jazyky, true) ? 'checked' : '' ?>> C#</label>
            </div>
            <div class="hint">type="checkbox" - více možností, name="jazyky[]" vytvoří pole</div>

            <!-- TEXTAREA -->
            <label for="poznamka">Poznámka:</label>
            <textarea id="poznamka" name="poznamka" placeholder="Cokoliv chceš dodat..."><?= htmlspecialchars($poznamka) ?></textarea>
            <div class="hint">&lt;textarea&gt; - víceřádkový text</div>

            <button type="submit">Odeslat</button>
        </form>
    </div>

    <!-- TABULKA S VÝSLEDKY -->
    <div class="card">
        <h2>Registrovaní studenti (<?= count($osoby) ?>)</h2>

        <?php if (empty($osoby)): ?>
            <p class="empty">Zatím žádní studenti. Vyplň formulář výše.</p>
        <?php else: ?>
            <table>
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Jméno</th>
                        <th>Email</th>
                        <th>Věk</th>
                        <th>Narozeniny</th>
                        <th>Pohlaví</th>
                        <th>Třída</th>
                        <th>Jazyky</th>
                        <th>Poznámka</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($osoby as $i => $osoba): ?>
                        <tr>
                            <td><?= $i + 1 ?></td>
                            <td><?= htmlspecialchars($osoba['jmeno']) ?></td>
                            <td><?= htmlspecialchars($osoba['email']) ?></td>
                            <td><?= $osoba['vek'] ?></td>
                            <td><?= htmlspecialchars($osoba['narozeniny']) ?></td>
                            <td><?= htmlspecialchars($osoba['pohlavi']) ?></td>
                            <td><?= htmlspecialchars($osoba['trida']) ?></td>
                            <td><?= htmlspecialchars($osoba['jazyky']) ?></td>
                            <td><?= htmlspecialchars($osoba['poznamka']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <form method="POST" action="" style="margin-top: 10px;">
                <input type="hidden" name="akce" value="smazat">
                <button type="submit" class="btn-danger">Smazat vše</button>
            </form>
        <?php endif; ?>
    </div>

    <!-- VYSVĚTLENÍ PRO STUDENTY -->
    <div class="card">
        <h2>Jak to funguje?</h2>
        <ol>
            <li><code>&lt;form method="POST"&gt;</code> - formulář odesílá data metodou POST</li>
            <li>PHP přečte data z <code>$_POST['jmeno']</code>, <code>$_POST['email']</code> atd.</li>
            <li><code>htmlspecialchars()</code> - escapuje HTML znaky při <strong>výpisu</strong> (ochrana proti XSS útoku). Používá se až při zobrazení, ne při čtení vstupu!</li>
            <li><strong>Whitelist validace</strong> - u select/radio ověřujeme, že hodnota je z povolených možností (<code>in_array</code>)</li>
            <li><strong>PRG pattern</strong> (Post/Redirect/Get) - po úspěšném uložení přesměrujeme pomocí <code>header('Location: ...')</code>, aby refresh stránky neodeslal formulář znovu</li>
            <li><code>$_SESSION</code> - data přežijí refresh stránky (ukládají se na serveru)</li>
            <li>Checkbox s <code>name="jazyky[]"</code> - hranaté závorky vytvoří pole hodnot</li>
            <li>Radio s <code>name="pohlavi"</code> - stejný name = lze vybrat jen jednu hodnotu</li>
            <li><strong>Zachování hodnot</strong> - při chybě validace formulář znovu zobrazí, co uživatel vyplnil (pomocí atributu <code>value="&lt;?= htmlspecialchars(...) ?&gt;"</code>)</li>
            <li><strong>Co tu chybí?</strong> V reálné aplikaci by formulář měl obsahovat <strong>CSRF token</strong> — náhodný řetězec uložený v session, který brání tomu, aby cizí stránka mohla odeslat formulář za uživatele. Frameworky (např. Nette) to řeší automaticky.</li>
        </ol>
    </div>
</body>
</html>
