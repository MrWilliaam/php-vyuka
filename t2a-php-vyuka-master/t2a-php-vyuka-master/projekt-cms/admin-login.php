<?php

declare(strict_types=1);

/**
 * UKÁZKOVÁ STRÁNKA – přihlášení administrátora
 *
 * Co tato stránka ukazuje:
 *   - Přihlašovací formulář
 *   - Použití Auth::login() (password_verify + session)
 *   - Přesměrování na admin.php po úspěšném loginu
 *   - Zobrazení chyby při neplatných údajích
 *
 * Vzorové údaje (vytvořené v init.php):
 *   E-mail: admin@cms.cz
 *   Heslo:  admin123
 */

require_once __DIR__ . '/src/bootstrap.php';

$auth = new Auth();

// Pokud už je někdo přihlášen, přesměrujeme na admin
if ($auth->isLoggedIn()) {
	header('Location: admin.php');
	exit;
}

$loginError = '';
$email = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_login'])) {
	$email = trim($_POST['email'] ?? '');
	$password = $_POST['password'] ?? '';

	if ($email === '' || $password === '') {
		$loginError = 'Vyplňte e-mail i heslo.';
	} elseif ($auth->login($email, $password)) {
		header('Location: admin.php');
		exit;
	} else {
		$loginError = 'Neplatný e-mail nebo heslo.';
	}
}

$pageTitle = 'Přihlášení – CMS';
$currentUser = NULL;

?>
<?php require __DIR__ . '/partials/header.php'; ?>

<main class="container container--narrow">
    <h1>Přihlášení administrátora</h1>

    <?php if ($loginError !== ''): ?>
        <p class="form-error form-error--block">
            <?= htmlspecialchars($loginError) ?>
        </p>
    <?php endif; ?>

    <form method="post" class="login-form">
        <div class="form-row">
            <label for="login-email">E-mail</label>
            <input
                type="email" id="login-email" name="email"
                value="<?= htmlspecialchars($email) ?>"
                required autofocus
            >
        </div>

        <div class="form-row">
            <label for="login-password">Heslo</label>
            <input
                type="password" id="login-password" name="password"
                required
            >
        </div>

        <button type="submit" name="submit_login" class="btn">Přihlásit se</button>

        <p class="form-note">
            Vzorové údaje: <code>admin@cms.cz</code> / <code>admin123</code>
        </p>
    </form>
</main>

<?php require __DIR__ . '/partials/footer.php'; ?>
