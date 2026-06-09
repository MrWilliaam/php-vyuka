<?php

declare(strict_types=1);

final class CommentRepository {

	private PDO $db;

	public function __construct() {
		$this->db = Database::getConnection();
	}

	/**
	 * Vrátí schválené komentáře k článku, seřazené od nejstaršího.
	 *
	 * @return list<CommentDTO>
	 */
	public function getApprovedForArticle(int $articleId): array {
		$stmt = $this->db->prepare("
			SELECT * FROM comments
			WHERE article_id = :articleId AND status = 'approved'
			ORDER BY created_at ASC
		");
		$stmt->execute(['articleId' => $articleId]);

		return array_map(CommentDTO::fromRow(...), $stmt->fetchAll());
	}

	/**
	 * Vrátí všechny komentáře (pro admin moderaci).
	 *
	 * @return list<CommentDTO>
	 */
	public function getAll(): array {
		$stmt = $this->db->query('SELECT * FROM comments ORDER BY created_at DESC');

		return array_map(CommentDTO::fromRow(...), $stmt->fetchAll());
	}

	/**
	 * Vrátí komentáře čekající na schválení.
	 *
	 * @return list<CommentDTO>
	 */
	public function getPending(): array {
		$stmt = $this->db->query("
			SELECT * FROM comments
			WHERE status = 'pending'
			ORDER BY created_at DESC
		");

		return array_map(CommentDTO::fromRow(...), $stmt->fetchAll());
	}

	/**
	 * Najde komentář podle ID.
	 */
	public function getById(int $id): ?CommentDTO {
		$stmt = $this->db->prepare('SELECT * FROM comments WHERE id = :id');
		$stmt->execute(['id' => $id]);

		$row = $stmt->fetch();

		return $row ? CommentDTO::fromRow($row) : NULL;
	}

	/**
	 * Vytvoří nový komentář (od nepřihlášeného uživatele).
	 * Komentář se uloží jako "pending" – musí ho schválit admin.
	 */
	public function create(
		int $articleId,
		string $authorName,
		string $authorEmail,
		string $content,
	): CommentDTO {
		$stmt = $this->db->prepare("
			INSERT INTO comments (article_id, author_name, author_email, content, status)
			VALUES (:articleId, :name, :email, :content, 'pending')
		");
		$stmt->execute([
			'articleId' => $articleId,
			'name'      => $authorName,
			'email'     => $authorEmail,
			'content'   => $content,
		]);

		return $this->getById((int) $this->db->lastInsertId())
			?? throw new \RuntimeException('Nepodařilo se vytvořit komentář.');
	}

	/**
	 * Schválí čekající komentář.
	 */
	public function approve(int $id): void {
		$stmt = $this->db->prepare("UPDATE comments SET status = 'approved' WHERE id = :id");
		$stmt->execute(['id' => $id]);
	}

	/**
	 * Smaže komentář.
	 */
	public function delete(int $id): void {
		$stmt = $this->db->prepare('DELETE FROM comments WHERE id = :id');
		$stmt->execute(['id' => $id]);
	}

}
