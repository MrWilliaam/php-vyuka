<?php
declare(strict_types=1);

/**
 * Lekce 11b: Kalkulačka školního výletu - Zadání
 * Spuštění: php -S 0.0.0.0:8080 -t public  →  otevři /11b-objednavka-zadani.php
 *
 * Úkol: Vytvoř kalkulačku pro plánování třídního výletu.
 * Na rozdíl od e-shopu (příklad) tady nejde o prosté množství × cena,
 * ale o složitější výpočet s různými variantami a cenou na osobu.
 *
 * Podívej se nejdřív na příklad: 11b-objednavka-priklad.php
 */

// Ceníky
$doprava = [
    'bus' => ['nazev' => 'Autobus', 'cena_km' => 12],   // Kč za km
    'vlak' => ['nazev' => 'Vlak', 'cena_km' => 8],
    'pesky' => ['nazev' => 'Pěšky', 'cena_km' => 0],
];

$ubytovani = [
    'zadne' => ['nazev' => 'Bez ubytování (jednodenní)', 'cena_noc' => 0],
    'hostel' => ['nazev' => 'Hostel', 'cena_noc' => 350],   // Kč za osobu za noc
    'hotel' => ['nazev' => 'Hotel', 'cena_noc' => 800],
    'stan' => ['nazev' => 'Stanování', 'cena_noc' => 100],
];

$aktivity = [
    'muzeum' => ['nazev' => 'Muzeum', 'cena' => 80],       // Kč za osobu
    'zoo' => ['nazev' => 'ZOO', 'cena' => 150],
    'kino' => ['nazev' => 'Kino', 'cena' => 180],
    'sport' => ['nazev' => 'Sportovní centrum', 'cena' => 120],
    'zabavni_park' => ['nazev' => 'Zábavní park', 'cena' => 250],
];

$formData = [
    'cil' => '',
    'pocet_osob' => '25',
    'vzdalenost' => '',
    'doprava' => 'bus',
    'ubytovani' => 'zadne',
    'pocet_noci' => '0',
    'aktivity' => [],
    'rozpocet_na_osobu' => '',
];

$chyby = [];
$kalkulace = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // TODO: Přečti data z formuláře do $formData
    // Pozor: aktivity přicházejí jako pole (checkbox), ošetři is_array()
    // $formData['cil'] = trim($_POST['cil'] ?? '');
    // $formData['pocet_osob'] = ...
    // $formData['vzdalenost'] = ...
    // $formData['doprava'] = ...
    // $formData['ubytovani'] = ...
    // $formData['pocet_noci'] = ...
    // $formData['aktivity'] = ...
    // $formData['rozpocet_na_osobu'] = ...


    // TODO: Validace
    // - Cíl: povinný
    // - Počet osob: povinný, celé číslo, 1–60
    // - Vzdálenost: povinná, číslo > 0
    // - Doprava: whitelist ($doprava)
    // - Ubytování: whitelist ($ubytovani)
    // - Počet nocí: 0–14; pokud ubytování není 'zadne', musí být > 0
    // - Aktivity: whitelist (každá vybraná aktivita musí existovat v $aktivity)


    // TODO: Výpočet (pokud nejsou chyby)
    // 1. Doprava celkem = vzdálenost × 2 (tam a zpět) × cena_km
    //    - Doprava na osobu = doprava celkem / počet osob (zaokrouhli nahoru: ceil())
    // 2. Ubytování na osobu = počet nocí × cena_noc
    // 3. Aktivity na osobu = součet cen vybraných aktivit
    // 4. Celkem na osobu = doprava_na_osobu + ubytování_na_osobu + aktivity_na_osobu
    // 5. Celkem za skupinu = celkem_na_osobu × počet osob
    //
    // Bonus: Pokud je zadaný rozpočet, porovnej s celkovou cenou na osobu
    //
    // Ulož výsledek do $kalkulace = [...]

    if (empty($chyby)) {
        // TODO: Spočítej a ulož do $kalkulace
        // $kalkulace = [
        //     'cil' => ...,
        //     'pocet_osob' => ...,
        //     'doprava_celkem' => ...,
        //     'doprava_na_osobu' => ...,
        //     'ubytovani_na_osobu' => ...,
        //     'aktivity_na_osobu' => ...,
        //     'aktivity_detail' => [...],
        //     'celkem_na_osobu' => ...,
        //     'celkem_skupina' => ...,
        //     'rozpocet_ok' => true/false/null,
        // ];
    }
}

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
    <title>Lekce 11b: Kalkulačka výletu - Zadání</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .error-msg { color: #c0392b; font-size: 12px; margin: 2px 0 8px; }
        .required::after { content: " *"; color: #c0392b; }
        .summary-row { display: flex; justify-content: space-between; padding: 8px 0; border-bottom: 1px solid #eee; }
        .summary-row:last-child { border-bottom: none; }
        .summary-row.total { font-weight: bold; font-size: 18px; border-top: 2px solid #333; border-bottom: none; padding-top: 12px; margin-top: 8px; }
        .budget-ok { background: #e8f5e9; border: 2px solid #27ae60; padding: 12px; border-radius: 8px; text-align: center; color: #2e7d32; }
        .budget-over { background: #fdecea; border: 2px solid #c0392b; padding: 12px; border-radius: 8px; text-align: center; color: #c0392b; }
    </style>
</head>
<body>
    <h1>Lekce 11b: Kalkulačka školního výletu - Zadání</h1>
    <p><a href="index.php">&larr; Zpět</a> | <a href="11b-objednavka-priklad.php">&larr; Příklad (e-shop)</a></p>

    <div class="task">
        <h3>Úkol</h3>
        <p>Dokonči kalkulačku pro plánování třídního výletu. Doplň:</p>
        <ol>
            <li>Čtení dat z formuláře</li>
            <li>Validaci všech polí (povinnost, rozsahy, whitelist)</li>
            <li>Výpočet ceny (doprava, ubytování, aktivity → cena na osobu a za skupinu)</li>
            <li>Zobrazení výsledku</li>
        </ol>
        <p>Klíčový rozdíl oproti e-shopu: doprava se dělí mezi všechny osoby, ubytování a aktivity jsou na osobu.</p>
    </div>

    <?php if ($kalkulace !== null): ?>
        <!-- TODO: Zobraz výsledek kalkulace -->
        <!-- Inspiruj se příkladem (11b-objednavka-priklad.php), ale uprav pro výlet -->
        <div class="success">
            <h2>Kalkulace výletu: <?= htmlspecialchars($kalkulace['cil'] ?? '') ?></h2>

            <!-- TODO: Zobraz rozpad ceny (doprava, ubytování, aktivity) -->
            <!-- TODO: Zobraz celkovou cenu na osobu a za skupinu -->
            <!-- TODO: Pokud je zadaný rozpočet, zobraz jestli se vejdeme -->

            <p><em>Doplň zobrazení výsledků...</em></p>

            <p style="margin-top: 16px;"><a href="<?= htmlspecialchars($_SERVER['REQUEST_URI']) ?>">Nová kalkulace</a></p>
        </div>
    <?php else: ?>
        <form method="POST" action="">
            <!-- ZÁKLADNÍ INFO -->
            <div class="card">
                <h2>Základní informace</h2>

                <label for="cil" class="required">Kam jedeme?</label>
                <input type="text" id="cil" name="cil" placeholder="např. Praha, Český Krumlov..."
                       value="<?= htmlspecialchars($formData['cil']) ?>">
                <?php if (isset($chyby['cil'])): ?>
                    <div class="error-msg"><?= htmlspecialchars($chyby['cil']) ?></div>
                <?php endif; ?>

                <label for="pocet_osob" class="required">Počet osob</label>
                <input type="number" id="pocet_osob" name="pocet_osob" min="1" max="60"
                       value="<?= htmlspecialchars($formData['pocet_osob']) ?>">
                <?php if (isset($chyby['pocet_osob'])): ?>
                    <div class="error-msg"><?= htmlspecialchars($chyby['pocet_osob']) ?></div>
                <?php endif; ?>

                <label for="vzdalenost" class="required">Vzdálenost (km, jedním směrem)</label>
                <input type="number" id="vzdalenost" name="vzdalenost" min="1" max="1000"
                       value="<?= htmlspecialchars($formData['vzdalenost']) ?>">
                <?php if (isset($chyby['vzdalenost'])): ?>
                    <div class="error-msg"><?= htmlspecialchars($chyby['vzdalenost']) ?></div>
                <?php endif; ?>
            </div>

            <!-- DOPRAVA -->
            <div class="card">
                <h2>Doprava</h2>
                <div class="radio-group" style="flex-direction: column; gap: 8px;">
                    <?php foreach ($doprava as $kod => $typ): ?>
                        <label>
                            <input type="radio" name="doprava" value="<?= $kod ?>"
                                   <?= $formData['doprava'] === $kod ? 'checked' : '' ?>>
                            <?= htmlspecialchars($typ['nazev']) ?>
                            <?php if ($typ['cena_km'] > 0): ?>
                                (<?= $typ['cena_km'] ?> Kč/km)
                            <?php else: ?>
                                (zdarma)
                            <?php endif; ?>
                        </label>
                    <?php endforeach; ?>
                </div>
                <div class="hint">Cena dopravy se dělí mezi všechny účastníky (tam i zpět)</div>
            </div>

            <!-- UBYTOVÁNÍ -->
            <div class="card">
                <h2>Ubytování</h2>
                <label for="ubytovani">Typ ubytování</label>
                <select id="ubytovani" name="ubytovani">
                    <?php foreach ($ubytovani as $kod => $typ): ?>
                        <option value="<?= $kod ?>" <?= $formData['ubytovani'] === $kod ? 'selected' : '' ?>>
                            <?= htmlspecialchars($typ['nazev']) ?>
                            <?php if ($typ['cena_noc'] > 0): ?>
                                (<?= $typ['cena_noc'] ?> Kč/noc/osoba)
                            <?php endif; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <?php if (isset($chyby['ubytovani'])): ?>
                    <div class="error-msg"><?= htmlspecialchars($chyby['ubytovani']) ?></div>
                <?php endif; ?>

                <label for="pocet_noci">Počet nocí</label>
                <input type="number" id="pocet_noci" name="pocet_noci" min="0" max="14"
                       value="<?= htmlspecialchars($formData['pocet_noci']) ?>">
                <?php if (isset($chyby['pocet_noci'])): ?>
                    <div class="error-msg"><?= htmlspecialchars($chyby['pocet_noci']) ?></div>
                <?php endif; ?>
            </div>

            <!-- AKTIVITY -->
            <div class="card">
                <h2>Aktivity</h2>
                <div class="checkbox-group" style="flex-direction: column; gap: 8px;">
                    <?php foreach ($aktivity as $kod => $akt): ?>
                        <label>
                            <input type="checkbox" name="aktivity[]" value="<?= $kod ?>"
                                   <?= in_array($kod, $formData['aktivity'], true) ? 'checked' : '' ?>>
                            <?= htmlspecialchars($akt['nazev']) ?> (<?= formatCena($akt['cena']) ?>/osoba)
                        </label>
                    <?php endforeach; ?>
                </div>
                <?php if (isset($chyby['aktivity'])): ?>
                    <div class="error-msg"><?= htmlspecialchars($chyby['aktivity']) ?></div>
                <?php endif; ?>
            </div>

            <!-- ROZPOČET -->
            <div class="card">
                <h2>Rozpočet</h2>
                <label for="rozpocet_na_osobu">Maximální rozpočet na osobu (nepovinné)</label>
                <input type="number" id="rozpocet_na_osobu" name="rozpocet_na_osobu" min="0"
                       placeholder="např. 500"
                       value="<?= htmlspecialchars($formData['rozpocet_na_osobu']) ?>">
                <div class="hint">Pokud vyplníš, ukážeme jestli se výlet vejde do rozpočtu</div>

                <button type="submit" style="margin-top: 16px; font-size: 16px;">Spočítat výlet</button>
            </div>
        </form>
    <?php endif; ?>

    <div class="bonus">
        <h3>Bonus</h3>
        <ul>
            <li>Přidej slevu na dopravu: pokud jede 40+ osob, doprava je o 20 % levnější</li>
            <li>Přidej položku "stravování" (snídaně, oběd, večeře – checkboxy s cenami)</li>
            <li>Zobraz varianty: co kdyby se jelo vlakem místo busem? (porovnání cen)</li>
            <li>Ulož výlety do <code>$_SESSION</code> a zobraz historii kalkulací</li>
        </ul>
    </div>
</body>
</html>
