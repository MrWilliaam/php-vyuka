<?php
declare(strict_types=1);

/**
 * Lekce 11c: Kvíz s vyhodnocením - Zadání (bez příkladu)
 * Spuštění: php -S 0.0.0.0:8080 -t public  →  otevři /11c-kviz-zadani.php
 *
 * Úkol: Vytvoř kvíz s vyhodnocením. Využij vše, co ses naučil v předchozích lekcích.
 * Tentokrát žádný příklad – zvládneš to sám!
 */

// TODO: Vytvoř pole otázek
// Každá otázka je asociativní pole s klíči:
//   'id'         → unikátní identifikátor (q1, q2, ...)
//   'typ'        → 'radio' (jedna odpověď), 'checkbox' (více odpovědí) nebo 'text' (psaná odpověď)
//   'otazka'     → text otázky
//   'moznosti'   → pole možností ['a' => 'text', 'b' => 'text', ...] (jen pro radio a checkbox)
//   'spravne'    → správná odpověď: string pro radio/text, pole pro checkbox
//   'vysvetleni' → vysvětlení správné odpovědi
//
// Příklad:
// [
//     'id' => 'q1',
//     'typ' => 'radio',
//     'otazka' => 'Kolik nohou má pavouk?',
//     'moznosti' => ['a' => '4', 'b' => '6', 'c' => '8', 'd' => '10'],
//     'spravne' => 'c',
//     'vysvetleni' => 'Pavouci mají 8 nohou (4 páry).',
// ],

$otazky = [
    // TODO: Přidej alespoň 6 otázek (min. 1 radio, 1 checkbox, 1 text)
    // Téma je na tobě: PHP, IT, sport, filmy, škola, historie...
];

$celkem = count($otazky);
$vysledky = null;
$odpovedi = [];
$skore = 0;

// TODO: Zpracování odpovědí
// Krok za krokem:
//
// 1. Zkontroluj, že přišel POST požadavek:
//    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
//
// 2. Inicializuj pole výsledků:
//    $vysledky = [];
//
// 3. Projdi všechny otázky (foreach $otazky as $otazka):
//    Pro každou otázku přečti odpověď z $_POST[$otazka['id']]
//
// 4. Vyhodnoť podle typu:
//
//    RADIO:
//    $odpoved = $_POST[$id] ?? '';
//    $jeSpravne = $odpoved === $otazka['spravne'];
//
//    CHECKBOX (odpověď je pole):
//    $odpoved = $_POST[$id] ?? [];
//    if (!is_array($odpoved)) $odpoved = [];
//    sort($odpoved);
//    $spravne = $otazka['spravne'];
//    sort($spravne);
//    $jeSpravne = $odpoved === $spravne;
//
//    TEXT (porovnej bez ohledu na velikost písmen):
//    $odpoved = trim($_POST[$id] ?? '');
//    $jeSpravne = mb_strtolower($odpoved) === mb_strtolower($otazka['spravne']);
//
// 5. Ulož výsledek:
//    $odpovedi[$id] = $odpoved;
//    $vysledky[$id] = $jeSpravne;
//    if ($jeSpravne) $skore++;
//
// 6. Zavři if
// }

?>
<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lekce 11c: Kvíz - Zadání</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .question {
            background: white; padding: 20px; border-radius: 8px; margin: 16px 0;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1); border-left: 4px solid #4F5B93;
        }
        /* TODO: Přidej styly pro správné a špatné odpovědi */
        /* .question-correct { border-left-color: #27ae60; background: #f0faf0; } */
        /* .question-wrong { border-left-color: #c0392b; background: #fef5f5; } */
        .question h3 { margin-top: 0; }
        .options label { display: block; padding: 8px 12px; margin: 4px 0; border-radius: 4px; cursor: pointer; }
        .options label:hover { background: #f0f0f0; }
        .explanation { background: #e3f2fd; padding: 10px 14px; border-radius: 4px; margin-top: 10px; font-size: 14px; }
        .score-box {
            text-align: center; padding: 30px; background: white;
            border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .score-number { font-size: 48px; font-weight: bold; }
        .progress-bar { height: 20px; background: #eee; border-radius: 10px; overflow: hidden; margin: 10px 0; }
        .progress-fill { height: 100%; border-radius: 10px; }
    </style>
</head>
<body>
    <h1>Lekce 11c: Kvíz - Zadání</h1>
    <p><a href="index.php">&larr; Zpět</a></p>

    <div class="task">
        <h3>Úkol</h3>
        <p>Vytvoř vlastní kvíz s vyhodnocením. Kroky:</p>
        <ol>
            <li>Vymysli alespoň <strong>6 otázek</strong> (mix radio, checkbox a text)</li>
            <li>Doplň <strong>zpracování odpovědí</strong> (viz komentáře v PHP kódu)</li>
            <li>Doplň <strong>zobrazení otázek</strong> podle typu (viz HTML níže)</li>
            <li>Po vyhodnocení zobraz <strong>skóre</strong> a <strong>správné odpovědi</strong></li>
        </ol>
        <p><strong>Nápověda:</strong> Přečti si komentáře v PHP kódu – jsou tam přesné kroky i ukázky kódu pro každý typ otázky.</p>
    </div>

    <?php if ($celkem === 0): ?>
        <div class="errors">
            <p>Pole <code>$otazky</code> je prázdné. Přidej otázky v PHP kódu!</p>
        </div>
    <?php endif; ?>

    <?php if ($vysledky !== null && $celkem > 0): ?>
        <!-- TODO: Zobraz výsledek (skóre, progress bar, hodnocení) -->
        <!--
        Nápověda:
        $procento = (int) round($skore / $celkem * 100);

        Pro progress bar:
        <div class="progress-bar">
            <div class="progress-fill" style="width: __procento__%; background: __barva__;"></div>
        </div>

        Barva podle výsledku: >= 80 % zelená (#27ae60), >= 50 % oranžová (#f39c12), jinak červená (#c0392b)
        -->
    <?php endif; ?>

    <form method="POST" action="">
        <?php foreach ($otazky as $i => $otazka): ?>
            <div class="question">
                <h3><?= $i + 1 ?>. <?= htmlspecialchars($otazka['otazka']) ?></h3>

                <!-- TODO: Zobraz možnosti podle typu otázky -->
                <!--
                TYP RADIO (jedna odpověď):
                <div class="options">
                    foreach moznosti as $klic => $text:
                        <label>
                            <input type="radio" name="<?= $otazka['id'] ?>" value="$klic">
                            $text
                        </label>
                    endforeach
                </div>

                TYP CHECKBOX (více odpovědí):
                <div class="options">
                    foreach moznosti as $klic => $text:
                        <label>
                            <input type="checkbox" name="<?= $otazka['id'] ?>[]" value="$klic">
                            $text
                        </label>
                    endforeach
                </div>

                TYP TEXT (psaná odpověď):
                <input type="text" name="<?= $otazka['id'] ?>" placeholder="Napiš odpověď...">
                -->

                <p><em>TODO: Zobraz možnosti</em></p>

                <!-- TODO: Po vyhodnocení zobraz vysvětlení -->
                <!--
                if ($vysledky !== null):
                    <div class="explanation">
                        <?= htmlspecialchars($otazka['vysvetleni']) ?>
                    </div>
                endif;
                -->
            </div>
        <?php endforeach; ?>

        <?php if ($celkem > 0 && $vysledky === null): ?>
            <button type="submit" style="margin: 20px 0; font-size: 18px; padding: 14px 32px;">Vyhodnotit kvíz</button>
        <?php elseif ($vysledky !== null): ?>
            <a href="<?= htmlspecialchars($_SERVER['REQUEST_URI']) ?>">
                <button type="button" style="margin: 20px 0; font-size: 18px; padding: 14px 32px;">Zkusit znovu</button>
            </a>
        <?php endif; ?>
    </form>

    <div class="bonus">
        <h3>Bonus</h3>
        <ul>
            <li>Označ správné/špatné odpovědi barvou (zelený/červený rámeček u otázky, popisky u možností)</li>
            <li>Po vyhodnocení zamkni formulář (<code>disabled</code> na inputech)</li>
            <li>Ulož nejlepší skóre do <code>$_SESSION</code> a zobraz "osobní rekord"</li>
            <li>Přidej náhodné pořadí otázek (pozor: po odeslání musí být pořadí stejné – použij seed v session)</li>
        </ul>
    </div>
</body>
</html>
