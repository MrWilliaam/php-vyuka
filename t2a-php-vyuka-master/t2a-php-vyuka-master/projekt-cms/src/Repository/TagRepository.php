<?php

declare(strict_types=1);

final class TagRepository {

	private PDO $db;

	public function __construct() {
		$this->db = Database::getConnection();
	}

	/**
	 * Vrátí všechny tagy.
	 *
	 * @return list<TagDTO>
	 */
	public function getAll(): array {
		$stmt = $this->db->query('SELECT * FROM tags ORDER BY name');

		return array_map(TagDTO::fromRow(...), $stmt->fetchAll());
	}

	/**
	 * Najde tag podle ID.
	 */
	public function getById(int $id): ?TagDTO {
		$stmt = $this->db->prepare('SELECT * FROM tags WHERE id = :id');
		$stmt->execute(['id' => $id]);

		$row = $stmt->fetch();

		return $row ? TagDTO::fromRow($row) : NULL;
	}

	/**
	 * Najde tag podle slugu.
	 */
	public function getBySlug(string $slug): ?TagDTO {
		$stmt = $this->db->prepare('SELECT * FROM tags WHERE slug = :slug');
		$stmt->execute(['slug' => $slug]);

		$row = $stmt->fetch();

		return $row ? TagDTO::fromRow($row) : NULL;
	}

	/**
	 * Vrátí všechny tagy pro daný článek.
	 *
	 * @return list<TagDTO>
	 */
	public function getForArticle(int $articleId): array {
		$stmt = $this->db->prepare('
			SELECT t.*
			FROM tags t
			JOIN article_tags at ON t.id = at.tag_id
			WHERE at.article_id = :articleId
			ORDER BY t.name
		');
		$stmt->execute(['articleId' => $articleId]);

		return array_map(TagDTO::fromRow(...), $stmt->fetchAll());
	}

	/**
	 * Tag cloud – vrátí všechny tagy spolu s počtem (publikovaných) článků.
	 *
	 * @return list<array{tag: TagDTO, count: int}>
	 */
	public function getTagCloud(): array {
		$stmt = $this->db->query("
			SELECT t.*, COUNT(at.article_id) AS count
			FROM tags t
			LEFT JOIN article_tags at ON t.id = at.tag_id
			LEFT JOIN articles a ON at.article_id = a.id AND a.status = 'published'
			GROUP BY t.id
			ORDER BY t.name
		");

		return array_map(
			fn(array $row): array => [
				'tag'   => TagDTO::fromRow($row),
				'count' => (int) $row['count'],
			],
			$stmt->fetchAll(),
		);
	}

	/**
	 * Vytvoří nový tag.
	 */
	public function create(string $name, string $slug): TagDTO {
		$stmt = $this->db->prepare('INSERT INTO tags (name, slug) VALUES (:name, :slug)');
		$stmt->execute(['name' => $name, 'slug' => $slug]);

		return $this->getById((int) $this->db->lastInsertId())
			?? throw new \RuntimeException('Nepodařilo se vytvořit tag.');
	}

}
