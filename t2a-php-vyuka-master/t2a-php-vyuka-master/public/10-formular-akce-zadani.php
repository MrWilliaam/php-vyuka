<?php
declare(strict_types=1);

/**
 * Lekce 10: Formulář s akcí na jinou stránku - Zadání
 * Spuštění: php -S 0.0.0.0:8080 -t public  →  otevři /10-formular-akce-zadani.php
 *
 * Úkoly na procvičení odeslání formuláře na jinou stránku.
 * Podívej se nejdřív na příklad: 10-formular-akce-priklad.php
 */
?>
<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lekce 10: Formulář s akcí - Zadání</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <h1>Lekce 10: Formulář s akcí na jinou stránku - Zadání</h1>
    <p><a href="index.php">&larr; Zpět</a> | <a href="10-formular-akce-priklad.php">&larr; Příklad</a></p>

    <!-- ============================================================ -->
    <!-- ÚKOL 1: Přihlášení na akci                                   -->
    <!-- ============================================================ -->
    <div class="task">
        <h3>Úkol 1: Přihlášení na akci (20 min)</h3>
        <p>Vytvoř dva soubory:</p>
        <ul>
            <li><strong>Tento soubor</strong> — formulář pro přihlášení na školní akci (jméno, email, ročník ze selectu, poznámka)</li>
            <li><strong><code>10-formular-akce-zadani-zpracovani.php</code></strong> — zpracování: validuj data a zobraz potvrzení přihlášení</li>
        </ul>
        <p>Na zpracovací stránce ošetři přímý přístup (když někdo zadá URL přímo do prohlížeče).</p>
    </div>

    <div class="card">
        <h2>Přihlášení na školní akci</h2>

        <div class="hint-box">
            Nápověda: Formulář má <code>action="10-formular-akce-zadani-zpracovani.php"</code>.
            Na zpracovací stránce nezapomeň na kontrolu <code>$_SERVER['REQUEST_METHOD'] === 'POST'</code>.
            Podívej se, jak je to uděláno v příkladu.
        </div>

        <!-- TODO: Vytvoř formulář s action na zpracovací stránku -->
        <!-- TODO: Inputy: jmeno (text), email (email), rocnik (select: 1.-4.), poznamka (textarea) -->

        <form method="POST" action="10-formular-akce-zadani-zpracovani.php">
            <p><em>--- Tady začni psát svůj formulář ---</em></p>

            <button type="submit">Přihlásit se</button>
        </form>
    </div>

    <!-- ============================================================ -->
    <!-- ÚKOL 2: Objednávka s potvrzením                              -->
    <!-- ============================================================ -->
    <div class="task">
        <h3>Úkol 2: Objednávka s potvrzením (25 min)</h3>
        <p>Vytvoř objednávkový formulář pro školní bufet:</p>
        <ul>
            <li>Jméno (text)</li>
            <li>Třída (select)</li>
            <li>Položka (radio: párek v rohlíku 30 Kč, pizza 50 Kč, salát 45 Kč)</li>
            <li>Počet kusů (number, 1-5)</li>
        </ul>
        <p>Na zpracovací stránce zobraz souhrn objednávky s <strong>celkovou cenou</strong>.</p>
    </div>

    <div class="card">
        <h2>Objednávka z bufetu</h2>

        <div class="hint-box">
            Nápověda: U radio buttonů dej jako <code>value</code> klíč (např. <code>value="parek"</code>)
            a ceny si ulož do pole na zpracovací stránce:
            <code>$ceny = ['parek' => 30, 'pizza' => 50, 'salat' => 45]</code>.
            Celková cena = cena položky &times; počet kusů.
        </div>

        <!-- TODO: Vytvoř formulář s action na svou zpracovací stránku -->
        <!-- TODO: Na zpracovací stránce validuj a zobraz souhrn s cenou -->

        <form method="POST" action="10-formular-akce-zadani-zpracovani.php">
            <p><em>--- Tady začni psát svůj formulář ---</em></p>

            <button type="submit">Objednat</button>
        </form>
    </div>

    <!-- ============================================================ -->
    <!-- BONUS                                                         -->
    <!-- ============================================================ -->
    <div class="bonus">
        <h3>Bonus: Zpět na formulář s chybami</h3>
        <p>Uprav zpracovací stránku tak, aby při chybě validace uložila chyby a vyplněná data
           do <code>$_SESSION</code>, přesměrovala zpět na formulář, a formulář zobrazil chyby
           a zachoval vyplněné hodnoty. To je reálný postup v praxi!</p>
        <div class="hint-box">
            Nápověda:
<pre>// Na zpracovací stránce (při chybě):
$_SESSION['chyby'] = $chyby;
$_SESSION['data'] = $_POST;
header('Location: 10-formular-akce-zadani.php');
exit;

// Na stránce s formulářem (na začátku):
$chyby = $_SESSION['chyby'] ?? [];
$data = $_SESSION['data'] ?? [];
unset($_SESSION['chyby'], $_SESSION['data']);</pre>
        </div>
    </div>

    <!-- ============================================================ -->
    <!-- TAHÁK                                                        -->
    <!-- ============================================================ -->
    <div class="card">
        <h2>Tahák: Formulář na jinou stránku</h2>

        <h3>Odeslání na jinou stránku</h3>
<pre>&lt;!-- formular.php --&gt;
&lt;form method="POST" action="zpracovani.php"&gt;
    &lt;input type="text" name="jmeno"&gt;
    &lt;button type="submit"&gt;Odeslat&lt;/button&gt;
&lt;/form&gt;</pre>

        <h3>Zpracovací stránka</h3>
<pre>&lt;?php
// zpracovani.php

// 1. Ošetření přímého přístupu
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: formular.php');
    exit;
}

// 2. Čtení dat - stejné jako v lekci 9
$jmeno = trim($_POST['jmeno'] ?? '');

// 3. Validace
$chyby = [];
if ($jmeno === '') {
    $chyby[] = 'Jméno je povinné.';
}

// 4. Zobrazení výsledku
if (empty($chyby)) {
    echo 'Děkujeme, ' . htmlspecialchars($jmeno);
} else {
    // Zobraz chyby + odkaz zpět
}
?&gt;</pre>

        <h3>Kdy co použít?</h3>
<pre>action=""                  →  formulář na sebe (vše v jednom souboru)
action="zpracovani.php"    →  formulář a logika odděleně (2 soubory)

V praxi a ve frameworcích se zpracování
vždy odděluje od zobrazení formuláře.</pre>
    </div>

</body>
</html>
