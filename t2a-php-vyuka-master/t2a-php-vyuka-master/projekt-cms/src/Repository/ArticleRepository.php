<?php

declare(strict_types=1);

final class ArticleRepository {

	private PDO $db;

	/**
	 * Společný SELECT pro všechny dotazy na články.
	 * Obsahuje JOIN na kategorii a autora, takže DTO dostane i jejich názvy.
	 */
	private const string BASE_SELECT = '
		SELECT a.*,
			c.name AS category_name,
			c.slug AS category_slug,
			u.name AS author_name
		FROM articles a
		JOIN categories c ON a.category_id = c.id
		JOIN users u ON a.author_id = u.id
	';

	public function __construct() {
		$this->db = Database::getConnection();
	}

	/**
	 * Vrátí všechny články (včetně nepublikovaných – pro admin).
	 *
	 * @return list<ArticleDTO>
	 */
	public function getAll(): array {
		$stmt = $this->db->query(self::BASE_SELECT . ' ORDER BY a.created_at DESC');

		return array_map(ArticleDTO::fromRow(...), $stmt->fetchAll());
	}

	/**
	 * Vrátí jen publikované články seřazené od nejnovějších.
	 *
	 * @return list<ArticleDTO>
	 */
	public function getPublished(int $limit = 0): array {
		$sql = self::BASE_SELECT . "
			WHERE a.status = 'published'
			ORDER BY a.published_at DESC
		";

		if ($limit > 0) {
			$sql .= ' LIMIT :limit';
			$stmt = $this->db->prepare($sql);
			$stmt->bindValue('limit', $limit, PDO::PARAM_INT);
			$stmt->execute();
		} else {
			$stmt = $this->db->query($sql);
		}

		return array_map(ArticleDTO::fromRow(...), $stmt->fetchAll());
	}

	/**
	 * Najde článek podle ID.
	 */
	public function getById(int $id): ?ArticleDTO {
		$stmt = $this->db->prepare(self::BASE_SELECT . ' WHERE a.id = :id');
		$stmt->execute(['id' => $id]);

		$row = $stmt->fetch();

		return $row ? ArticleDTO::fromRow($row) : NULL;
	}

	/**
	 * Najde článek podle slugu.
	 */
	public function getBySlug(string $slug): ?ArticleDTO {
		$stmt = $this->db->prepare(self::BASE_SELECT . ' WHERE a.slug = :slug');
		$stmt->execute(['slug' => $slug]);

		$row = $stmt->fetch();

		return $row ? ArticleDTO::fromRow($row) : NULL;
	}

	/**
	 * Vrátí publikované články v dané kategorii.
	 *
	 * @return list<ArticleDTO>
	 */
	public function getByCategorySlug(string $slug): array {
		$stmt = $this->db->prepare(self::BASE_SELECT . "
			WHERE c.slug = :slug AND a.status = 'published'
			ORDER BY a.published_at DESC
		");
		$stmt->execute(['slug' => $slug]);

		return array_map(ArticleDTO::fromRow(...), $stmt->fetchAll());
	}

	/**
	 * Vrátí publikované články označené daným tagem.
	 *
	 * @return list<ArticleDTO>
	 */
	public function getByTagSlug(string $slug): array {
		$stmt = $this->db->prepare(self::BASE_SELECT . "
			JOIN article_tags at ON a.id = at.article_id
			JOIN tags t ON at.tag_id = t.id
			WHERE t.slug = :slug AND a.status = 'published'
			ORDER BY a.published_at DESC
		");
		$stmt->execute(['slug' => $slug]);

		return array_map(ArticleDTO::fromRow(...), $stmt->fetchAll());
	}

	/**
	 * Fulltextové vyhledávání v publikovaných článcích (titulek, perex, obsah).
	 *
	 * @return list<ArticleDTO>
	 */
	public function search(string $query): array {
		$escaped = str_replace(['%', '_', '\\'], ['\\%', '\\_', '\\\\'], $query);
		$like = '%' . $escaped . '%';

		$stmt = $this->db->prepare(self::BASE_SELECT . "
			WHERE a.status = 'published'
				AND (a.title LIKE :q ESCAPE '\\'
				  OR a.perex LIKE :q ESCAPE '\\'
				  OR a.content LIKE :q ESCAPE '\\')
			ORDER BY a.published_at DESC
		");
		$stmt->execute(['q' => $like]);

		return array_map(ArticleDTO::fromRow(...), $stmt->fetchAll());
	}

	/**
	 * Vytvoří nový článek se zadanými tagy (M:N).
	 * Vše proběhne v transakci – pokud selže jakákoliv část, nic se neuloží.
	 *
	 * @param list<int> $tagIds  ID tagů, které mají být k článku přiřazeny.
	 */
	public function create(
		int $categoryId,
		int $authorId,
		string $title,
		string $slug,
		string $perex,
		string $content,
		string $coverImage,
		string $status,
		?string $publishedAt,
		array $tagIds = [],
	): ArticleDTO {
		$this->db->beginTransaction();

		try {
			$stmt = $this->db->prepare('
				INSERT INTO articles (category_id, author_id, title, slug, perex, content, cover_image, status, published_at)
				VALUES (:categoryId, :authorId, :title, :slug, :perex, :content, :coverImage, :status, :publishedAt)
			');
			$stmt->execute([
				'categoryId'  => $categoryId,
				'authorId'    => $authorId,
				'title'       => $title,
				'slug'        => $slug,
				'perex'       => $perex,
				'content'     => $content,
				'coverImage'  => $coverImage,
				'status'      => $status,
				'publishedAt' => $publishedAt,
			]);

			$articleId = (int) $this->db->lastInsertId();

			$this->setTags($articleId, $tagIds);

			$this->db->commit();
		} catch (\Throwable $e) {
			$this->db->rollBack();
			throw $e;
		}

		return $this->getById($articleId)
			?? throw new \RuntimeException('Nepodařilo se uložit článek.');
	}

	/**
	 * Aktualizuje existující článek a přepíše jeho tagy.
	 *
	 * @param list<int> $tagIds
	 */
	public function update(
		int $id,
		int $categoryId,
		string $title,
		string $slug,
		string $perex,
		string $content,
		string $coverImage,
		string $status,
		?string $publishedAt,
		array $tagIds = [],
	): ArticleDTO {
		$this->db->beginTransaction();

		try {
			$stmt = $this->db->prepare('
				UPDATE articles
				SET category_id = :categoryId,
				    title = :title,
				    slug = :slug,
				    perex = :perex,
				    content = :content,
				    cover_image = :coverImage,
				    status = :status,
				    published_at = :publishedAt
				WHERE id = :id
			');
			$stmt->execute([
				'id'          => $id,
				'categoryId'  => $categoryId,
				'title'       => $title,
				'slug'        => $slug,
				'perex'       => $perex,
				'content'     => $content,
				'coverImage'  => $coverImage,
				'status'      => $status,
				'publishedAt' => $publishedAt,
			]);

			$this->setTags($id, $tagIds);

			$this->db->commit();
		} catch (\Throwable $e) {
			$this->db->rollBack();
			throw $e;
		}

		return $this->getById($id)
			?? throw new \RuntimeException('Článek po update nelze načíst.');
	}

	/**
	 * Smaže článek (a kaskádově i jeho komentáře a vazby na tagy).
	 */
	public function delete(int $id): void {
		$stmt = $this->db->prepare('DELETE FROM articles WHERE id = :id');
		$stmt->execute(['id' => $id]);
	}

	/**
	 * Přepíše seznam tagů u článku – smaže staré vazby a vloží nové.
	 *
	 * @param list<int> $tagIds
	 */
	private function setTags(int $articleId, array $tagIds): void {
		$del = $this->db->prepare('DELETE FROM article_tags WHERE article_id = :articleId');
		$del->execute(['articleId' => $articleId]);

		if ($tagIds === []) {
			return;
		}

		$ins = $this->db->prepare('INSERT INTO article_tags (article_id, tag_id) VALUES (:articleId, :tagId)');
		foreach (array_unique($tagIds) as $tagId) {
			$ins->execute(['articleId' => $articleId, 'tagId' => $tagId]);
		}
	}

}
