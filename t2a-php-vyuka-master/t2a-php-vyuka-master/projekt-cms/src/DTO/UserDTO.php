<?php

declare(strict_types=1);

readonly class UserDTO {

	public function __construct(
		public int $id,
		public string $email,
		public string $passwordHash,
		public string $name,
		public string $createdAt,
	) {
	}

	/**
	 * @param array<string, mixed> $row
	 */
	public static function fromRow(array $row): self {
		return new self(
			id: (int) $row['id'],
			email: $row['email'],
			passwordHash: $row['password_hash'],
			name: $row['name'],
			createdAt: $row['created_at'],
		);
	}

}
