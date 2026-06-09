<?php
declare(strict_types=1);

/**
 * Lekce 11a: Editace profilu - Zadání
 * Spuštění: php -S 0.0.0.0:8080 -t public  →  otevři /11a-registrace-zadani.php
 *
 * Úkol: Vytvoř stránku pro editaci uživatelského profilu.
 * Na rozdíl od registrace (příklad) tady formulář přichází PŘEDVYPLNĚNÝ
 * z uložených dat a student řeší UPDATE místo INSERT.
 *
 * Podívej se nejdřív na příklad: 11a-registrace-priklad.php
 */

session_start();

// Simulovaný uložený profil (v reálu by přišel z databáze)
// Pokud ještě nemáme profil v session, nastavíme výchozí
if (!isset($_SESSION['profil'])) {
    $_SESSION['profil'] = [
        'jmeno' => 'Jan',
        'prijmeni' => 'Novák',
        'email' => 'jan.novak@skola.cz',
        'prezdivka' => 'xNovak42',
        'trida' => '2.A',
        'bio' => 'Rád programuju v PHP.',
        'barva' => '#4F5B93',
    ];
}

$profil = $_SESSION['profil'];

// TODO: Inicializuj $formData hodnotami z $profil (ne prázdnými řetězci!)
// Tím se formulář zobrazí předvyplněný aktuálními daty.
$formData = [
    'jmeno' => '',      // TODO: načti z $profil
    'prijmeni' => '',    // TODO: načti z $profil
    'email' => '',       // TODO: načti z $profil
    'prezdivka' => '',   // TODO: načti z $profil
    'trida' => '',       // TODO: načti z $profil
    'bio' => '',         // TODO: načti z $profil
    'barva' => '#4F5B93', // TODO: načti z $profil
];

$chyby = [];
$ulozeno = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['akce']) && $_POST['akce'] === 'reset') {
        // Reset profilu na výchozí hodnoty
        unset($_SESSION['profil']);
        header('Location: ' . $_SERVER['REQUEST_URI']);
        exit;
    }

    // TODO: Přečti data z formuláře do $formData (nezapomeň na trim())


    // TODO: Validace jména a příjmení (povinné, min. 2 znaky)


    // TODO: Validace emailu (povinný, platný formát)


    // TODO: Validace přezdívky
    // - Povinná, 3–20 znaků
    // - Jen písmena, čísla a podtržítka (nápověda: preg_match('/^[a-zA-Z0-9_]+$/', ...))


    // TODO: Validace třídy – whitelist: '', '1.A', '1.B', '2.A', '2.B', '3.A', '3.B', '4.A', '4.B'


    // TODO: Validace bio – max 200 znaků (nápověda: mb_strlen())


    // TODO: Pokud nejsou chyby:
    // 1. Ulož $formData do $_SESSION['profil']
    // 2. Nastav $ulozeno = true
    // 3. Aktualizuj $profil z nové session
    // POZOR: Tady NEPŘESMĚROVÁVÁME (chceme zobrazit hlášku "Uloženo")
    //        V reálné aplikaci bys přesměroval (PRG pattern).
}

?>
<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lekce 11a: Editace profilu - Zadání</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .field-error { border-color: #c0392b !important; background: #fdecea !important; }
        .error-msg { color: #c0392b; font-size: 12px; margin: 2px 0 8px; }
        .required::after { content: " *"; color: #c0392b; }
        .profile-header { display: flex; align-items: center; gap: 16px; margin-bottom: 16px; }
        .profile-avatar { width: 60px; height: 60px; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-size: 24px; font-weight: bold; }
        .char-count { font-size: 12px; color: #888; text-align: right; }
    </style>
</head>
<body>
    <h1>Lekce 11a: Editace profilu - Zadání</h1>
    <p><a href="index.php">&larr; Zpět</a> | <a href="11a-registrace-priklad.php">&larr; Příklad (registrace)</a></p>

    <div class="task">
        <h3>Úkol</h3>
        <p>Dokonči stránku pro editaci uživatelského profilu. Klíčové rozdíly oproti registraci:</p>
        <ol>
            <li><strong>Předvyplněný formulář</strong> – data se načtou z <code>$_SESSION['profil']</code></li>
            <li><strong>Update místo insert</strong> – ukládáš změny do existujícího záznamu</li>
            <li><strong>Žádné heslo</strong> – změna hesla by byla na samostatné stránce</li>
            <li><strong>Nová pole</strong> – přezdívka (regex validace), bio (max délka), barva</li>
        </ol>
    </div>

    <!-- AKTUÁLNÍ PROFIL -->
    <div class="card">
        <div class="profile-header">
            <div class="profile-avatar" style="background-color: <?= htmlspecialchars($profil['barva']) ?>">
                <?= htmlspecialchars(mb_substr($profil['jmeno'], 0, 1) . mb_substr($profil['prijmeni'], 0, 1)) ?>
            </div>
            <div>
                <strong><?= htmlspecialchars($profil['jmeno'] . ' ' . $profil['prijmeni']) ?></strong>
                <br>
                <span style="color: #666;">@<?= htmlspecialchars($profil['prezdivka']) ?></span>
                <?php if ($profil['trida'] !== ''): ?>
                    · <?= htmlspecialchars($profil['trida']) ?>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <?php if ($ulozeno): ?>
        <div class="success">
            <strong>Profil byl úspěšně aktualizován!</strong>
        </div>
    <?php endif; ?>

    <!-- FORMULÁŘ -->
    <div class="card">
        <h2>Upravit profil</h2>

        <?php if (!empty($chyby)): ?>
            <div class="errors">
                <strong>Oprav chyby a ulož znovu.</strong>
            </div>
        <?php endif; ?>

        <form method="POST" action="">
            <label for="jmeno" class="required">Jméno</label>
            <input type="text" id="jmeno" name="jmeno"
                   value="<?= htmlspecialchars($formData['jmeno']) ?>">
            <?php if (isset($chyby['jmeno'])): ?>
                <div class="error-msg"><?= htmlspecialchars($chyby['jmeno']) ?></div>
            <?php endif; ?>

            <label for="prijmeni" class="required">Příjmení</label>
            <input type="text" id="prijmeni" name="prijmeni"
                   value="<?= htmlspecialchars($formData['prijmeni']) ?>">
            <?php if (isset($chyby['prijmeni'])): ?>
                <div class="error-msg"><?= htmlspecialchars($chyby['prijmeni']) ?></div>
            <?php endif; ?>

            <label for="email" class="required">Email</label>
            <input type="email" id="email" name="email"
                   value="<?= htmlspecialchars($formData['email']) ?>">
            <?php if (isset($chyby['email'])): ?>
                <div class="error-msg"><?= htmlspecialchars($chyby['email']) ?></div>
            <?php endif; ?>

            <label for="prezdivka" class="required">Přezdívka</label>
            <input type="text" id="prezdivka" name="prezdivka"
                   value="<?= htmlspecialchars($formData['prezdivka']) ?>">
            <?php if (isset($chyby['prezdivka'])): ?>
                <div class="error-msg"><?= htmlspecialchars($chyby['prezdivka']) ?></div>
            <?php endif; ?>
            <div class="hint">3–20 znaků, jen písmena, čísla a podtržítka</div>

            <label for="trida">Třída</label>
            <select id="trida" name="trida">
                <option value="">-- vyber --</option>
                <?php foreach (['1.A', '1.B', '2.A', '2.B', '3.A', '3.B', '4.A', '4.B'] as $t): ?>
                    <option value="<?= $t ?>" <?= $formData['trida'] === $t ? 'selected' : '' ?>><?= $t ?></option>
                <?php endforeach; ?>
            </select>
            <?php if (isset($chyby['trida'])): ?>
                <div class="error-msg"><?= htmlspecialchars($chyby['trida']) ?></div>
            <?php endif; ?>

            <label for="bio">O sobě</label>
            <textarea id="bio" name="bio" maxlength="200" placeholder="Napiš něco o sobě..."><?= htmlspecialchars($formData['bio']) ?></textarea>
            <div class="char-count"><?= mb_strlen($formData['bio']) ?> / 200 znaků</div>
            <?php if (isset($chyby['bio'])): ?>
                <div class="error-msg"><?= htmlspecialchars($chyby['bio']) ?></div>
            <?php endif; ?>

            <label for="barva">Barva profilu</label>
            <input type="color" id="barva" name="barva"
                   value="<?= htmlspecialchars($formData['barva']) ?>">
            <div class="hint">type="color" – výběr barvy, hodnota je HEX kód (např. #4F5B93)</div>

            <div style="display: flex; gap: 10px; margin-top: 16px;">
                <button type="submit">Uložit změny</button>
            </div>
        </form>

        <form method="POST" action="" style="margin-top: 10px;">
            <input type="hidden" name="akce" value="reset">
            <button type="submit" class="btn-danger">Resetovat profil</button>
        </form>
    </div>

    <div class="bonus">
        <h3>Bonus</h3>
        <ul>
            <li>Přidej validaci barvy – musí být platný HEX kód (<code>preg_match('/^#[0-9a-fA-F]{6}$/', ...)</code>)</li>
            <li>Zobraz v avataru aktuální zvolenou barvu (ne uloženou, ale tu z formuláře)</li>
            <li>Přidej pole "Změnit heslo" a "Nové heslo znovu" – nepovinné, ale pokud je vyplněné, musí projít validací</li>
        </ul>
    </div>
</body>
</html>
