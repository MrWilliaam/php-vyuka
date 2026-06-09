<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PHP Výuka</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <h1>PHP Výuka</h1>

    <div class="card">
        <h2>Aktuální datum a čas</h2>
        <p><?= date('d.m.Y H:i:s') ?></p>
    </div>

    <div class="card">
        <h2>Informace o PHP</h2>
        <p>Verze PHP: <code><?= PHP_VERSION ?></code></p>
        <p>Operační systém: <code><?= PHP_OS ?></code></p>
    </div>

    <div class="card">
        <h2>Příklady</h2>
        <ul>
            <li><a href="formular.php">Formulář</a></li>
            <li><a href="09-formulare-priklad.php">Lekce 9: Formuláře - Příklad</a></li>
            <li><a href="09-formulare-zadani.php">Lekce 9: Formuláře - Zadání</a></li>
            <li><a href="10-formular-akce-priklad.php">Lekce 10: Formulář s akcí na jinou stránku - Příklad</a></li>
            <li><a href="10-formular-akce-zadani.php">Lekce 10: Formulář s akcí na jinou stránku - Zadání</a></li>
            <li><a href="11a-registrace-priklad.php">Lekce 11a: Registrační formulář s validací - Příklad</a></li>
            <li><a href="11a-registrace-zadani.php">Lekce 11a: Editace profilu - Zadání</a></li>
            <li><a href="11b-objednavka-priklad.php">Lekce 11b: Objednávkový formulář - Příklad</a></li>
            <li><a href="11b-objednavka-zadani.php">Lekce 11b: Kalkulačka školního výletu - Zadání</a></li>
            <li><a href="11c-kviz-zadani.php">Lekce 11c: Kvíz s vyhodnocením - Zadání</a></li>
            <li><a href="12-soubory-priklad.php">Lekce 12: Práce se soubory (návštěvní kniha) - Příklad</a></li>
            <li><a href="12-soubory-zadani.php">Lekce 12: Kontaktní formulář s CSV - Zadání</a></li>
        </ul>
    </div>

    <?php
    // PHP kód můžeš psát přímo do HTML
    $pozdrav = "Vítej v PHP!";
    ?>

    <div class="card">
        <h2><?= $pozdrav ?></h2>
        <p>Tento text je generovaný PHP.</p>
    </div>
</body>
</html>
