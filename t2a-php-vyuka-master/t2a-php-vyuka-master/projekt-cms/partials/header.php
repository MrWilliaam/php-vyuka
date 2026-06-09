<?php

/**
 * PARTIAL: Hlavička stránky
 *
 * Očekává proměnnou:
 *   $pageTitle (string) – titulek stránky
 *
 * Volitelně:
 *   $currentUser (?UserDTO) – přihlášený admin (zobrazí se v hlavičce)
 */

$pageTitle ??= 'CMS';
$currentUser ??= NULL;

?>
<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle) ?></title>
    <link rel="stylesheet" href="assets/css/main.css">
</head>
<body>

<header class="header">
    <a href="index.php" class="header__logo">CMS</a>

    <nav class="header__nav">
        <a href="index.php">Domů</a>
        <a href="clanky.php">Články</a>
        <a href="udalosti.php">Události</a>
        <a href="o-nas.php">O nás</a>
        <a href="kontakt.php">Kontakt</a>
    </nav>

    <div class="header__user">
        <?php if ($currentUser !== NULL): ?>
            <span class="header__user-name">
                &#128100; <?= htmlspecialchars($currentUser->name) ?>
            </span>
            <a href="admin.php" class="header__user-link">Administrace</a>
            <a href="admin-logout.php" class="header__user-link">Odhlásit</a>
        <?php else: ?>
            <a href="admin-login.php" class="header__user-link">Přihlášení</a>
        <?php endif; ?>
    </div>
</header>
