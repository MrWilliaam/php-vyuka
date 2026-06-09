<?php
declare(strict_types=1);

/**
 * Lekce 9: Formuláře - Zadání
 * Spuštění: php -S 0.0.0.0:8080 -t public  →  otevři /09-formulare-zadani.php
 *
 * Úkoly na procvičení práce s formuláři.
 * Podívej se nejdřív na příklad: 09-formulare-priklad.php
 */

session_start();

// === ZDE PIŠTE SVŮJ KÓD PRO ZPRACOVÁNÍ FORMULÁŘŮ ===

?>
<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lekce 9: Formuláře - Zadání</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <h1>Lekce 9: Formuláře - Zadání</h1>
    <p><a href="index.php">&larr; Zpět</a> | <a href="09-formulare-priklad.php">&larr; Příklad</a></p>

    <!-- ============================================================ -->
    <!-- ÚKOL 1: Jednoduchý kalkulátor                                -->
    <!-- ============================================================ -->
    <div class="task">
        <h3>Úkol 1: Kalkulátor (15 min)</h3>
        <p>Vytvoř formulář s dvěma čísly a výběrem operace (+, -, *, /).
           Po odeslání zobraz výsledek. Ošetři dělení nulou.</p>
    </div>

    <div class="card">
        <h2>Kalkulátor</h2>

        <div class="hint-box">
            Nápověda: Použij <code>type="number"</code> pro čísla a <code>&lt;select&gt;</code> pro operaci.
            V PHP zpracuj pomocí <code>match</code> nebo <code>switch</code>.
        </div>

        <!-- TODO: Vytvoř formulář s inputy: cislo1, operace (select: +,-,*,/), cislo2 -->
        <!-- TODO: Po odeslání zobraz výsledek, např. "5 + 3 = 8" -->
        <!-- TODO: Ošetři dělení nulou - zobraz chybovou hlášku -->

        <form method="POST" action="">
            <p><em>--- Tady začni psát svůj formulář ---</em></p>
        </form>
    </div>

    <!-- ============================================================ -->
    <!-- ÚKOL 2: Přihlašovací formulář                                -->
    <!-- ============================================================ -->
    <div class="task">
        <h3>Úkol 2: Přihlašovací formulář (20 min)</h3>
        <p>Vytvoř přihlašovací formulář s uživatelským jménem a heslem.
           Zkontroluj, zda se údaje shodují s předem danými hodnotami.
           Zobraz hlášku "Přihlášení úspěšné" nebo "Špatné jméno nebo heslo".</p>
    </div>

    <div class="card">
        <h2>Přihlášení</h2>

        <div class="hint-box">
            Nápověda: Použij <code>type="password"</code> pro heslo.
            Porovnej s pevně danými hodnotami, např. <code>$spravneJmeno = 'admin'</code>,
            <code>$spravneHeslo = 'heslo123'</code>.<br><br>
            <strong>Pozor:</strong> V reálné aplikaci se hesla nikdy neukládají jako text!
            Používá se <code>password_hash()</code> pro uložení a <code>password_verify()</code>
            pro ověření. Tady pro jednoduchost porovnáváme přímo.
        </div>

        <!-- TODO: Formulář s inputy: uzivatel (text), heslo (password) -->
        <!-- TODO: Kontrola proti pevným hodnotám: admin / heslo123 -->
        <!-- TODO: Zobraz zelenou hlášku při úspěchu, červenou při neúspěchu -->

        <form method="POST" action="">
            <p><em>--- Tady začni psát svůj formulář ---</em></p>
        </form>
    </div>

    <!-- ============================================================ -->
    <!-- ÚKOL 3: Objednávka pizzy                                    -->
    <!-- ============================================================ -->
    <div class="task">
        <h3>Úkol 3: Objednávka pizzy (30 min)</h3>
        <p>Vytvoř objednávkový formulář pro pizzerii. Formulář obsahuje:</p>
        <ul>
            <li>Jméno zákazníka (text)</li>
            <li>Telefon (text)</li>
            <li>Velikost pizzy (radio: malá 150 Kč, střední 200 Kč, velká 250 Kč)</li>
            <li>Přísady navíc (checkbox: sýr +20 Kč, šunka +25 Kč, olivy +15 Kč, jalapeňo +15 Kč)</li>
            <li>Poznámka k objednávce (textarea)</li>
        </ul>
        <p>Po odeslání zobraz souhrn objednávky a <strong>vypočítej celkovou cenu</strong>.</p>
    </div>

    <div class="card">
        <h2>Objednávka pizzy</h2>

        <div class="hint-box">
            Nápověda: U radio buttonů dej jako <code>value</code> cenu (např. <code>value="150"</code>).
            U checkboxů použij <code>name="prisady[]"</code> a pro cenu si udělej
            pole cen: <code>$cenyPrisad = ['syr' => 20, 'sunka' => 25, ...]</code>
        </div>

        <!-- TODO: Formulář s inputy podle zadání výše -->
        <!-- TODO: Zpracuj data, vypočítej cenu -->
        <!-- TODO: Zobraz souhrn: jméno, telefon, velikost, přísady, poznámka, CELKOVÁ CENA -->

        <form method="POST" action="">
            <p><em>--- Tady začni psát svůj formulář ---</em></p>
        </form>
    </div>

    <!-- ============================================================ -->
    <!-- ÚKOL 4: Evidence knih                                        -->
    <!-- ============================================================ -->
    <div class="task">
        <h3>Úkol 4: Evidence knih s tabulkou (30 min)</h3>
        <p>Vytvoř formulář pro evidenci knih (název, autor, rok vydání, žánr ze selectu, hodnocení 1-5).
           Každá odeslaná kniha se přidá jako řádek do tabulky. Použij <code>$_SESSION</code>
           pro uchování dat. Přidej tlačítko na smazání všech knih.</p>
    </div>

    <div class="card">
        <h2>Evidence knih</h2>

        <div class="hint-box">
            Nápověda: Podívej se, jak je to uděláno v příkladu (<a href="09-formulare-priklad.php">09-formulare-priklad.php</a>).
            Použij <code>session_start()</code> na začátku souboru a <code>$_SESSION['knihy']</code> pro ukládání.
            Pro hodnocení můžeš použít <code>type="range"</code> s <code>min="1" max="5"</code>.
        </div>

        <!-- TODO: Formulář: nazev (text), autor (text), rok (number), zanr (select), hodnoceni (range 1-5) -->
        <!-- TODO: Ukládej do $_SESSION['knihy'] -->
        <!-- TODO: Zobraz tabulku se všemi knihami -->
        <!-- TODO: Tlačítko "Smazat vše" -->

        <form method="POST" action="">
            <p><em>--- Tady začni psát svůj formulář ---</em></p>
        </form>

        <p class="empty">Zatím žádné knihy.</p>
    </div>

    <!-- ============================================================ -->
    <!-- ÚKOL 5: Validace formuláře                                   -->
    <!-- ============================================================ -->
    <div class="task">
        <h3>Úkol 5: Registrace s validací (25 min)</h3>
        <p>Vytvoř registrační formulář s těmito poli a validací v PHP:</p>
        <ul>
            <li>Přezdívka - povinná, min. 3 znaky, max. 20 znaků</li>
            <li>Email - povinný, musí obsahovat @</li>
            <li>Heslo - povinné, min. 6 znaků</li>
            <li>Heslo znovu - musí se shodovat s heslem</li>
            <li>Věk - povinný, musí být 15-99</li>
            <li>Souhlas s podmínkami - povinný checkbox</li>
        </ul>
        <p>Pokud jsou chyby, zobraz je <strong>červeně nad formulářem</strong> a
           <strong>zachovej vyplněné hodnoty</strong> ve formuláři (kromě hesla).</p>
    </div>

    <div class="card">
        <h2>Registrace</h2>

        <div class="hint-box">
            Nápověda: Pro validaci délky textu použij <code>mb_strlen()</code> (počítá znaky, ne bajty — důležité pro češtinu).
            Pro zachování hodnot ve formuláři: <code>&lt;input value="&lt;?= htmlspecialchars($prezdivka) ?&gt;"&gt;</code>.
            Pro kontrolu emailu: <code>filter_var($email, FILTER_VALIDATE_EMAIL)</code>.
            Checkbox: <code>isset($_POST['souhlas'])</code>.
        </div>

        <!-- TODO: Formulář dle zadání -->
        <!-- TODO: Validace v PHP - sbírej chyby do pole $chyby = [] -->
        <!-- TODO: Zobraz chyby, pokud existují -->
        <!-- TODO: Po úspěšné registraci zobraz zelenou hlášku -->
        <!-- TODO: Zachovej vyplněné hodnoty -->

        <form method="POST" action="">
            <p><em>--- Tady začni psát svůj formulář ---</em></p>
        </form>
    </div>

    <!-- ============================================================ -->
    <!-- BONUSOVÉ ÚKOLY                                               -->
    <!-- ============================================================ -->
    <div class="bonus">
        <h3>Bonus 1: Anketa s výsledky</h3>
        <p>Vytvoř anketu s otázkou (např. "Jaký je tvůj oblíbený předmět?") a radio buttony
           s možnostmi. Ukládej hlasy do <code>$_SESSION</code>. Po hlasování zobraz
           výsledky jako jednoduchý sloupcový graf z HTML (obarvené <code>&lt;div&gt;</code> s šířkou v %).</p>
    </div>

    <div class="bonus">
        <h3>Bonus 2: Převodník jednotek</h3>
        <p>Vytvoř formulář pro převod jednotek. Uživatel zadá číslo, vybere "z" a "na"
           (km/míle, kg/libry, °C/°F). Zobraz výsledek převodu.</p>
    </div>

    <!-- ============================================================ -->
    <!-- TAHÁK                                                        -->
    <!-- ============================================================ -->
    <div class="card">
        <h2>Tahák: Formuláře v PHP</h2>

        <h3>Odeslání formuláře</h3>
<pre>&lt;form method="POST" action=""&gt;
    &lt;input type="text" name="jmeno"&gt;
    &lt;button type="submit"&gt;Odeslat&lt;/button&gt;
&lt;/form&gt;</pre>

        <h3>Zpracování v PHP</h3>
<pre>if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Data čteme "surová" - escapujeme až při výpisu!
    $jmeno = trim($_POST['jmeno'] ?? '');
}
// Při výpisu do HTML vždy escapujeme:
echo htmlspecialchars($jmeno);</pre>

        <h3>Typy inputů</h3>
<pre>type="text"      - běžný text
type="email"     - email (prohlížeč validuje formát)
type="password"  - heslo (skryté znaky)
type="number"    - číslo (min, max, step)
type="date"      - datum
type="range"     - posuvník (min, max)
type="color"     - výběr barvy
type="checkbox"  - zaškrtávátko (více možností)
type="radio"     - přepínač (jedna možnost)
&lt;select&gt;         - rozbalovací seznam
&lt;textarea&gt;       - víceřádkový text</pre>

        <h3>Validace v PHP</h3>
<pre>$chyby = [];

if (trim($jmeno) === '') {
    $chyby[] = 'Jméno je povinné.';
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $chyby[] = 'Neplatný email.';
}

if (mb_strlen($heslo) < 6) {
    $chyby[] = 'Heslo musí mít alespoň 6 znaků.';
}

if (empty($chyby)) {
    // Vše OK - zpracuj data
}</pre>

        <h3>PRG pattern (Post/Redirect/Get)</h3>
<pre>// Po úspěšném zpracování POST přesměruj!
// Bez toho by refresh stránky odeslal data znovu.
if (empty($chyby)) {
    $_SESSION['data'][] = ['jmeno' => $jmeno];
    header('Location: ' . $_SERVER['REQUEST_URI']);
    exit;
}</pre>

        <h3>Session</h3>
<pre>session_start(); // Na začátku souboru!

// Uložení
$_SESSION['data'][] = ['jmeno' => $jmeno];

// Čtení
$zaznamy = $_SESSION['data'] ?? [];

// Smazání
$_SESSION['data'] = [];</pre>
    </div>

</body>
</html>
