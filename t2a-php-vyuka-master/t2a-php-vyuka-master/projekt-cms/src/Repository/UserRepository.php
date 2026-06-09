<?php

declare(strict_types=1);

final class UserRepository {

	private PDO $db;

	public function __construct() {
		$this->db = Database::getConnection();
	}

	/**
	 * Najde uživatele podle ID.
	 */
	public function getById(int $id): ?UserDTO {
		$stmt = $this->db->prepare('SELECT * FROM users WHERE id = :id');
		$stmt->execute(['id' => $id]);

		$row = $stmt->fetch();

		return $row ? UserDTO::fromRow($row) : NULL;
	}

	/**
	 * Najde uživatele podle e-mailu.
	 */
	public function getByEmail(string $email): ?UserDTO {
		$stmt = $this->db->prepare('SELECT * FROM users WHERE email = :email');
		$stmt->execute(['email' => $email]);

		$row = $stmt->fetch();

		return $row ? UserDTO::fromRow($row) : NULL;
	}

	/**
	 * Vytvoří nového uživatele. Heslo musí být v plaintextu – metoda ho zahashuje.
	 */
	public function create(string $email, string $password, string $name): UserDTO {
		$stmt = $this->db->prepare('
			INSERT INTO users (email, password_hash, name)
			VALUES (:email, :hash, :name)
		');

		$stmt->execute([
			'email' => $email,
			'hash'  => password_hash($password, PASSWORD_DEFAULT),
			'name'  => $name,
		]);

		return $this->getById((int) $this->db->lastInsertId())
			?? throw new \RuntimeException('Nepodařilo se vytvořit uživatele.');
	}

}
