<?php
declare(strict_types=1);

/**
 * Lekce 10: Zpracování kontaktního formuláře - Příklad
 *
 * Tato stránka přijímá data z formuláře (10-formular-akce-priklad.php).
 * Uživatel sem nepřichází přímo - je sem přesměrován po odeslání formuláře.
 */

// Kontrola, zda přišel POST požadavek (ne přímý přístup přes URL)
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    // Uživatel přišel přímo na URL → přesměrujeme zpět na formulář
    header('Location: 10-formular-akce-priklad.php');
    exit;
}

// Čtení a čištění dat
$jmeno = trim($_POST['jmeno'] ?? '');
$email = trim($_POST['email'] ?? '');
$predmet = trim($_POST['predmet'] ?? '');
$zprava = trim($_POST['zprava'] ?? '');

// Validace
$chyby = [];

if ($jmeno === '') {
    $chyby[] = 'Jméno je povinné.';
}

if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $chyby[] = 'Zadej platný email.';
}

if ($zprava === '') {
    $chyby[] = 'Zpráva je povinná.';
}

// Rozhodnutí: zobrazíme úspěch nebo chyby
$uspech = empty($chyby);

// V reálné aplikaci bychom tu data uložili do databáze nebo odeslali emailem.
// Pro ukázku jen zobrazíme souhrn.
?>
<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Zpracování formuláře</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <h1>Zpracování formuláře</h1>
    <p><a href="10-formular-akce-priklad.php">&larr; Zpět na formulář</a></p>

    <?php if ($uspech): ?>
        <div class="success">
            <h2>Zpráva odeslána!</h2>
            <p>Děkujeme, <?= htmlspecialchars($jmeno) ?>. Tvoje zpráva byla přijata.</p>

            <table>
                <tr>
                    <th>Jméno</th>
                    <td><?= htmlspecialchars($jmeno) ?></td>
                </tr>
                <tr>
                    <th>Email</th>
                    <td><?= htmlspecialchars($email) ?></td>
                </tr>
                <tr>
                    <th>Předmět</th>
                    <td><?= htmlspecialchars($predmet !== '' ? $predmet : '(nezadáno)') ?></td>
                </tr>
                <tr>
                    <th>Zpráva</th>
                    <td><?= nl2br(htmlspecialchars($zprava)) ?></td>
                </tr>
            </table>
        </div>
    <?php else: ?>
        <div class="errors">
            <h2>Chyby ve formuláři</h2>
            <ul>
                <?php foreach ($chyby as $chyba): ?>
                    <li><?= htmlspecialchars($chyba) ?></li>
                <?php endforeach; ?>
            </ul>
            <p><a href="10-formular-akce-priklad.php">Zpět na formulář</a></p>
        </div>
    <?php endif; ?>

    <!-- VYSVĚTLENÍ -->
    <div class="card">
        <h2>Co se stalo?</h2>

        <div class="info">
            Tato stránka (<code>10-formular-akce-priklad-zpracovani.php</code>) přijala data
            z formuláře na stránce <code>10-formular-akce-priklad.php</code>.
        </div>

        <h3>Klíčové body tohoto souboru:</h3>
<pre>// 1. Kontrola přímého přístupu
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: 10-formular-akce-priklad.php');
    exit;
}

// 2. Čtení dat - úplně stejné jako v lekci 9
$jmeno = trim($_POST['jmeno'] ?? '');

// 3. Validace, zobrazení výsledku</pre>

        <h3>Nevýhoda odděleného zpracování:</h3>
        <p>Když validace selže, nemůžeme jednoduše znovu zobrazit formulář s vyplněnými hodnotami
           — formulář je na jiné stránce. Řešení:</p>
        <ul>
            <li>Uložit data a chyby do <code>$_SESSION</code> a přesměrovat zpět na formulář</li>
            <li>Nebo použít framework, který to řeší automaticky (např. Nette)</li>
        </ul>
    </div>
</body>
</html>
