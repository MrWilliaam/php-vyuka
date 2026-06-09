<?php

declare(strict_types=1);

/**
 * Auth – přihlášení administrátorů přes session.
 *
 * Hesla v databázi jsou uložená jako hash (password_hash, výchozí bcrypt).
 * Ověřování se dělá přes password_verify – nikdy nesrovnávejte hesla rovností!
 *
 * Příklad použití:
 *   $auth = new Auth();
 *
 *   // přihlášení
 *   if ($auth->login($email, $password)) {
 *       header('Location: admin.php');
 *       exit;
 *   }
 *
 *   // chráněná stránka
 *   if (!$auth->isLoggedIn()) {
 *       header('Location: admin-login.php');
 *       exit;
 *   }
 *
 *   $user = $auth->getCurrentUser(); // UserDTO
 *
 *   // odhlášení
 *   $auth->logout();
 */
final class Auth {

	private const string SESSION_KEY = 'user_id';

	public function __construct() {
		if (session_status() === PHP_SESSION_NONE) {
			session_start();
		}
	}

	/**
	 * Pokusí se přihlásit uživatele e-mailem a heslem.
	 * Vrátí true pokud byl login úspěšný (a uloží user_id do session).
	 */
	public function login(string $email, string $password): bool {
		$user = (new UserRepository())->getByEmail($email);

		if ($user === NULL) {
			return false;
		}

		if (!password_verify($password, $user->passwordHash)) {
			return false;
		}

		// Při změně oprávnění je dobrý zvyk regenerovat ID session (session fixation)
		session_regenerate_id(delete_old_session: true);

		$_SESSION[self::SESSION_KEY] = $user->id;

		return true;
	}

	/**
	 * Odhlásí aktuálně přihlášeného uživatele.
	 */
	public function logout(): void {
		unset($_SESSION[self::SESSION_KEY]);
		session_regenerate_id(delete_old_session: true);
	}

	/**
	 * Je někdo přihlášen?
	 */
	public function isLoggedIn(): bool {
		return isset($_SESSION[self::SESSION_KEY]);
	}

	/**
	 * Vrátí ID přihlášeného uživatele, nebo null pokud nikdo není přihlášen.
	 */
	public function getUserId(): ?int {
		$id = $_SESSION[self::SESSION_KEY] ?? NULL;

		return $id !== NULL ? (int) $id : NULL;
	}

	/**
	 * Vrátí DTO přihlášeného uživatele, nebo null pokud nikdo není přihlášen.
	 */
	public function getCurrentUser(): ?UserDTO {
		$id = $this->getUserId();

		return $id !== NULL ? (new UserRepository())->getById($id) : NULL;
	}

	/**
	 * Zkratka pro stránky, které vyžadují přihlášení.
	 * Pokud uživatel není přihlášen, přesměruje na login a ukončí skript.
	 */
	public function requireLogin(string $loginUrl = 'admin-login.php'): void {
		if (!$this->isLoggedIn()) {
			header('Location: ' . $loginUrl);
			exit;
		}
	}

}
