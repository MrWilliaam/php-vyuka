<?php
declare(strict_types=1);

/**
 * Lekce 11a: Registrační formulář s validací - Příklad
 * Spuštění: php -S 0.0.0.0:8080 -t public  →  otevři /11a-registrace-priklad.php
 *
 * Tento příklad ukazuje:
 * - Komplexní validaci (povinná pole, formát emailu, délka hesla, shoda hesel)
 * - "Sticky form" – po chybě se formulář zobrazí s vyplněnými hodnotami
 * - Zobrazení chybových hlášek u konkrétních polí
 * - Bezpečné zacházení s hesly (nikdy nevypisujeme zpět do formuláře)
 */

session_start();

// Inicializace registrovaných uživatelů
if (!isset($_SESSION['uzivatele'])) {
    $_SESSION['uzivatele'] = [];
}

// Výchozí hodnoty formuláře (zachovají se po chybě validace)
$formData = [
    'jmeno' => '',
    'prijmeni' => '',
    'email' => '',
    'vek' => '',
    'pohlavi' => '',
    'telefon' => '',
    'bio' => '',
    'souhlas' => false,
];

// Chyby u jednotlivých polí
$chyby = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['akce']) && $_POST['akce'] === 'smazat') {
        $_SESSION['uzivatele'] = [];
        header('Location: ' . $_SERVER['REQUEST_URI']);
        exit;
    }

    // Čtení dat z formuláře
    $formData['jmeno'] = trim($_POST['jmeno'] ?? '');
    $formData['prijmeni'] = trim($_POST['prijmeni'] ?? '');
    $formData['email'] = trim($_POST['email'] ?? '');
    $formData['vek'] = trim($_POST['vek'] ?? '');
    $formData['pohlavi'] = $_POST['pohlavi'] ?? '';
    $formData['telefon'] = trim($_POST['telefon'] ?? '');
    $formData['bio'] = trim($_POST['bio'] ?? '');
    $formData['souhlas'] = isset($_POST['souhlas']);
    $heslo = $_POST['heslo'] ?? '';
    $hesloZnovu = $_POST['heslo_znovu'] ?? '';

    // === VALIDACE ===

    // Jméno
    if ($formData['jmeno'] === '') {
        $chyby['jmeno'] = 'Jméno je povinné.';
    } elseif (mb_strlen($formData['jmeno']) < 2) {
        $chyby['jmeno'] = 'Jméno musí mít alespoň 2 znaky.';
    }

    // Příjmení
    if ($formData['prijmeni'] === '') {
        $chyby['prijmeni'] = 'Příjmení je povinné.';
    } elseif (mb_strlen($formData['prijmeni']) < 2) {
        $chyby['prijmeni'] = 'Příjmení musí mít alespoň 2 znaky.';
    }

    // Email
    if ($formData['email'] === '') {
        $chyby['email'] = 'Email je povinný.';
    } elseif (!filter_var($formData['email'], FILTER_VALIDATE_EMAIL)) {
        $chyby['email'] = 'Zadej platný email (např. jan@email.cz).';
    } else {
        // Kontrola duplicity emailu
        foreach ($_SESSION['uzivatele'] as $uzivatel) {
            if ($uzivatel['email'] === $formData['email']) {
                $chyby['email'] = 'Tento email je již registrovaný.';
                break;
            }
        }
    }

    // Heslo
    if ($heslo === '') {
        $chyby['heslo'] = 'Heslo je povinné.';
    } elseif (mb_strlen($heslo) < 6) {
        $chyby['heslo'] = 'Heslo musí mít alespoň 6 znaků.';
    } elseif (!preg_match('/[0-9]/', $heslo)) {
        $chyby['heslo'] = 'Heslo musí obsahovat alespoň jedno číslo.';
    }

    // Potvrzení hesla
    if ($hesloZnovu === '') {
        $chyby['heslo_znovu'] = 'Potvrď heslo.';
    } elseif ($heslo !== $hesloZnovu) {
        $chyby['heslo_znovu'] = 'Hesla se neshodují.';
    }

    // Věk (nepovinný, ale pokud vyplněn, musí být validní)
    if ($formData['vek'] !== '') {
        $vekInt = filter_var($formData['vek'], FILTER_VALIDATE_INT);
        if ($vekInt === false || $vekInt < 10 || $vekInt > 120) {
            $chyby['vek'] = 'Věk musí být číslo mezi 10 a 120.';
        }
    }

    // Pohlaví (whitelist validace)
    $povolenaPohlavi = ['', 'muz', 'zena', 'jine'];
    if (!in_array($formData['pohlavi'], $povolenaPohlavi, true)) {
        $chyby['pohlavi'] = 'Neplatná hodnota.';
    }

    // Telefon (nepovinný, ale pokud vyplněn, musí mít správný formát)
    if ($formData['telefon'] !== '' && !preg_match('/^(\+420)?\s?\d{3}\s?\d{3}\s?\d{3}$/', $formData['telefon'])) {
        $chyby['telefon'] = 'Zadej platné české číslo (např. 777 123 456).';
    }

    // Souhlas
    if (!$formData['souhlas']) {
        $chyby['souhlas'] = 'Musíš souhlasit s podmínkami.';
    }

    // === ULOŽENÍ ===
    if (empty($chyby)) {
        $_SESSION['uzivatele'][] = [
            'jmeno' => $formData['jmeno'],
            'prijmeni' => $formData['prijmeni'],
            'email' => $formData['email'],
            'vek' => $formData['vek'] !== '' ? (int) $formData['vek'] : null,
            'pohlavi' => $formData['pohlavi'],
            'telefon' => $formData['telefon'],
            'bio' => $formData['bio'],
            'registrovan' => date('d.m.Y H:i'),
        ];

        header('Location: ' . $_SERVER['REQUEST_URI']);
        exit;
    }
}

$uzivatele = $_SESSION['uzivatele'];

/**
 * Pomocná funkce – zobrazí CSS třídu pro pole s chybou
 */
function fieldClass(array $chyby, string $pole): string
{
    return isset($chyby[$pole]) ? ' class="field-error"' : '';
}
?>
<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lekce 11a: Registrační formulář s validací</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .field-error { border-color: #c0392b !important; background: #fdecea !important; }
        .error-msg { color: #c0392b; font-size: 12px; margin: 2px 0 8px; }
        .required::after { content: " *"; color: #c0392b; }
    </style>
</head>
<body>
    <h1>Lekce 11a: Registrační formulář s validací</h1>
    <p><a href="index.php">&larr; Zpět</a> | <a href="11a-registrace-zadani.php">Zadání &rarr;</a></p>

    <div class="info">
        Tento formulář ukazuje <strong>kompletní validaci</strong> na straně serveru.
        Po chybě se formulář znovu zobrazí s vyplněnými hodnotami (<em>sticky form</em>)
        a chybové hlášky se zobrazují u konkrétních polí.
        Povinná pole jsou označena <span style="color: #c0392b;">*</span>.
    </div>

    <!-- FORMULÁŘ -->
    <div class="card">
        <h2>Registrace</h2>

        <?php if (!empty($chyby)): ?>
            <div class="errors">
                <strong>Formulář obsahuje <?= count($chyby) ?> <?= count($chyby) === 1 ? 'chybu' : (count($chyby) < 5 ? 'chyby' : 'chyb') ?>. Oprav je a odešli znovu.</strong>
            </div>
        <?php endif; ?>

        <form method="POST" action="">
            <!-- JMÉNO -->
            <label for="jmeno" class="required">Jméno</label>
            <input type="text" id="jmeno" name="jmeno" placeholder="Jan"
                   value="<?= htmlspecialchars($formData['jmeno']) ?>"<?= fieldClass($chyby, 'jmeno') ?>>
            <?php if (isset($chyby['jmeno'])): ?>
                <div class="error-msg"><?= htmlspecialchars($chyby['jmeno']) ?></div>
            <?php endif; ?>

            <!-- PŘÍJMENÍ -->
            <label for="prijmeni" class="required">Příjmení</label>
            <input type="text" id="prijmeni" name="prijmeni" placeholder="Novák"
                   value="<?= htmlspecialchars($formData['prijmeni']) ?>"<?= fieldClass($chyby, 'prijmeni') ?>>
            <?php if (isset($chyby['prijmeni'])): ?>
                <div class="error-msg"><?= htmlspecialchars($chyby['prijmeni']) ?></div>
            <?php endif; ?>

            <!-- EMAIL -->
            <label for="email" class="required">Email</label>
            <input type="email" id="email" name="email" placeholder="jan@email.cz"
                   value="<?= htmlspecialchars($formData['email']) ?>"<?= fieldClass($chyby, 'email') ?>>
            <?php if (isset($chyby['email'])): ?>
                <div class="error-msg"><?= htmlspecialchars($chyby['email']) ?></div>
            <?php endif; ?>

            <!-- HESLO (nikdy nevracíme hodnotu zpět!) -->
            <label for="heslo" class="required">Heslo</label>
            <input type="password" id="heslo" name="heslo" placeholder="Alespoň 6 znaků, musí obsahovat číslo"
                   <?= fieldClass($chyby, 'heslo') ?>>
            <?php if (isset($chyby['heslo'])): ?>
                <div class="error-msg"><?= htmlspecialchars($chyby['heslo']) ?></div>
            <?php endif; ?>
            <div class="hint">Minimálně 6 znaků, musí obsahovat alespoň jedno číslo</div>

            <!-- HESLO ZNOVU -->
            <label for="heslo_znovu" class="required">Heslo znovu</label>
            <input type="password" id="heslo_znovu" name="heslo_znovu" placeholder="Zopakuj heslo"
                   <?= fieldClass($chyby, 'heslo_znovu') ?>>
            <?php if (isset($chyby['heslo_znovu'])): ?>
                <div class="error-msg"><?= htmlspecialchars($chyby['heslo_znovu']) ?></div>
            <?php endif; ?>

            <!-- VĚK -->
            <label for="vek">Věk</label>
            <input type="number" id="vek" name="vek" min="10" max="120"
                   value="<?= htmlspecialchars($formData['vek']) ?>"<?= fieldClass($chyby, 'vek') ?>>
            <?php if (isset($chyby['vek'])): ?>
                <div class="error-msg"><?= htmlspecialchars($chyby['vek']) ?></div>
            <?php endif; ?>

            <!-- POHLAVÍ -->
            <label>Pohlaví</label>
            <div class="radio-group">
                <label><input type="radio" name="pohlavi" value="muz" <?= $formData['pohlavi'] === 'muz' ? 'checked' : '' ?>> Muž</label>
                <label><input type="radio" name="pohlavi" value="zena" <?= $formData['pohlavi'] === 'zena' ? 'checked' : '' ?>> Žena</label>
                <label><input type="radio" name="pohlavi" value="jine" <?= $formData['pohlavi'] === 'jine' ? 'checked' : '' ?>> Jiné</label>
            </div>

            <!-- TELEFON -->
            <label for="telefon">Telefon</label>
            <input type="text" id="telefon" name="telefon" placeholder="777 123 456"
                   value="<?= htmlspecialchars($formData['telefon']) ?>"<?= fieldClass($chyby, 'telefon') ?>>
            <?php if (isset($chyby['telefon'])): ?>
                <div class="error-msg"><?= htmlspecialchars($chyby['telefon']) ?></div>
            <?php endif; ?>

            <!-- BIO -->
            <label for="bio">O sobě</label>
            <textarea id="bio" name="bio" placeholder="Napiš něco o sobě..."><?= htmlspecialchars($formData['bio']) ?></textarea>

            <!-- SOUHLAS -->
            <div style="margin: 16px 0;">
                <label style="display: inline; font-weight: normal;">
                    <input type="checkbox" name="souhlas" value="1" <?= $formData['souhlas'] ? 'checked' : '' ?>>
                    Souhlasím s podmínkami použití <span style="color: #c0392b;">*</span>
                </label>
                <?php if (isset($chyby['souhlas'])): ?>
                    <div class="error-msg"><?= htmlspecialchars($chyby['souhlas']) ?></div>
                <?php endif; ?>
            </div>

            <button type="submit">Zaregistrovat se</button>
        </form>
    </div>

    <!-- REGISTROVANÍ UŽIVATELÉ -->
    <div class="card">
        <h2>Registrovaní uživatelé (<?= count($uzivatele) ?>)</h2>

        <?php if (empty($uzivatele)): ?>
            <p class="empty">Zatím žádní uživatelé.</p>
        <?php else: ?>
            <table>
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Jméno</th>
                        <th>Email</th>
                        <th>Věk</th>
                        <th>Telefon</th>
                        <th>Registrován</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($uzivatele as $i => $u): ?>
                        <tr>
                            <td><?= $i + 1 ?></td>
                            <td><?= htmlspecialchars($u['jmeno'] . ' ' . $u['prijmeni']) ?></td>
                            <td><?= htmlspecialchars($u['email']) ?></td>
                            <td><?= $u['vek'] !== null ? $u['vek'] : '–' ?></td>
                            <td><?= htmlspecialchars($u['telefon'] !== '' ? $u['telefon'] : '–') ?></td>
                            <td><?= htmlspecialchars($u['registrovan']) ?></td>
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

    <!-- VYSVĚTLENÍ -->
    <div class="card">
        <h2>Jak to funguje?</h2>
        <ol>
            <li><strong>Sticky form</strong> – po chybě validace se formulář znovu zobrazí s hodnotami, které uživatel vyplnil. Klíč je atribut <code>value="&lt;?= htmlspecialchars($formData['jmeno']) ?&gt;"</code></li>
            <li><strong>Heslo se NIKDY nevrací</strong> – pole <code>type="password"</code> po chybě zůstane prázdné. Je to bezpečnostní pravidlo.</li>
            <li><strong>Chyby u polí</strong> – místo jednoho seznamu chyb nahoře zobrazujeme chybu přímo pod polem, kde vznikla. Pole <code>$chyby</code> je asociativní – klíč = název pole.</li>
            <li><strong>Víceúrovňová validace</strong> – nejdřív kontrolujeme, jestli pole není prázdné, pak formát (email, délka hesla), pak logiku (shoda hesel, duplicita emailu).</li>
            <li><strong>Vizuální zpětná vazba</strong> – pole s chybou dostane červený rámeček (CSS třída <code>.field-error</code>).</li>
            <li><strong>PRG pattern</strong> – po úspěchu přesměrujeme, aby refresh stránky neodeslal formulář znovu.</li>
        </ol>
    </div>
</body>
</html>
