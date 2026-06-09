<?php
declare(strict_types=1);

/**
 * Lekce 10: Formulář s akcí na jinou stránku - Příklad
 * Spuštění: php -S 0.0.0.0:8080 -t public  →  otevři /10-formular-akce-priklad.php
 *
 * Tento příklad ukazuje:
 * - Odeslání formuláře na JINOU stránku (atribut action)
 * - Zpracování dat na cílové stránce
 * - Předání dat mezi stránkami pomocí POST
 * - Rozdíl oproti action="" (odeslání na sebe)
 */
?>
<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lekce 10: Formulář s akcí - Příklad</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <h1>Lekce 10: Formulář s akcí na jinou stránku</h1>
    <p><a href="index.php">&larr; Zpět</a> | <a href="10-formular-akce-zadani.php">Zadání &rarr;</a></p>

    <div class="info">
        <strong>Klíčový rozdíl oproti lekci 9:</strong>
        V lekci 9 formulář odesílal data sám na sebe (<code>action=""</code>).
        Tady formulář odesílá data na <strong>jinou stránku</strong> (<code>action="10-formular-akce-priklad-zpracovani.php"</code>).
        Formulář jen sbírá data, zpracování je na cílové stránce.
    </div>

    <!-- KONTAKTNÍ FORMULÁŘ -->
    <div class="card">
        <h2>Kontaktní formulář</h2>
        <p>Vyplň formulář a odešli. Data se zpracují na jiné stránce.</p>

        <form method="POST" action="10-formular-akce-priklad-zpracovani.php">
            <label for="jmeno">Jméno:</label>
            <input type="text" id="jmeno" name="jmeno" placeholder="Jan Novák" required>

            <label for="email">Email:</label>
            <input type="email" id="email" name="email" placeholder="jan@email.cz" required>

            <label for="predmet">Předmět:</label>
            <input type="text" id="predmet" name="predmet" placeholder="Dotaz na kurz">

            <label for="zprava">Zpráva:</label>
            <textarea id="zprava" name="zprava" placeholder="Napiš svou zprávu..." required></textarea>

            <button type="submit">Odeslat zprávu</button>
        </form>
    </div>

    <!-- VYSVĚTLENÍ -->
    <div class="card">
        <h2>Jak to funguje?</h2>

        <h3>1. Formulář odesílá na jinou stránku</h3>
<pre>&lt;!-- Tato stránka (formulář) --&gt;
&lt;form method="POST" action="zpracovani.php"&gt;
    &lt;input name="jmeno"&gt;
    &lt;button type="submit"&gt;Odeslat&lt;/button&gt;
&lt;/form&gt;</pre>

        <h3>2. Cílová stránka přečte data z $_POST</h3>
<pre>
<?php
// zpracovani.php - sem přijdou data po odeslání
$jmeno = trim($_POST['jmeno'] ?? '');
echo htmlspecialchars($jmeno);
?>;
</pre>

        <h3>3. Kdy použít action na jinou stránku?</h3>
        <ul>
            <li><code>action=""</code> — formulář i zpracování na jedné stránce (jednodušší, vhodné pro malé formuláře)</li>
            <li><code>action="zpracovani.php"</code> — formulář a zpracování odděleně (přehlednější, oddělení zodpovědností)</li>
        </ul>
        <p>V praxi se nejčastěji používá oddělené zpracování — buď na jiné stránce,
           nebo přes framework (presenter/controller), který směruje požadavek na správné místo.</p>

        <h3>4. Co se děje "pod kapotou"?</h3>
        <ol>
            <li>Uživatel vyplní formulář na <code>10-formular-akce-priklad.php</code></li>
            <li>Klikne na "Odeslat" → prohlížeč pošle POST požadavek na <code>10-formular-akce-priklad-zpracovani.php</code></li>
            <li>Server spustí PHP soubor <code>10-formular-akce-priklad-zpracovani.php</code></li>
            <li>Ten přečte data z <code>$_POST</code> a zobrazí odpověď</li>
        </ol>

        <h3>5. Pozor na přímý přístup</h3>
        <p>Cílová stránka musí ošetřit situaci, kdy na ni uživatel přijde přímo (bez odeslání formuláře).
           Proto kontrolujeme <code>$_SERVER['REQUEST_METHOD'] === 'POST'</code>.</p>
    </div>
</body>
</html>
