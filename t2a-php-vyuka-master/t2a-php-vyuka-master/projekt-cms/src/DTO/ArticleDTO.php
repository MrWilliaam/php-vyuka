<?php

declare(strict_types=1);

readonly class ArticleDTO {

	/**
	 * @param list<TagDTO> $tags
	 */
	public function __construct(
		public int $id,
		public int $categoryId,
		public int $authorId,
		public string $title,
		public string $slug,
		public string $perex,
		public string $content,
		public string $coverImage,
		public string $status,
		public ?string $publishedAt,
		public string $createdAt,
		public ?string $categoryName = NULL,
		public ?string $categorySlug = NULL,
		public ?string $authorName = NULL,
		public array $tags = [],
	) {
	}

	/**
	 * @param array<string, mixed> $row
	 */
	public static function fromRow(array $row): self {
		return new self(
			id: (int) $row['id'],
			categoryId: (int) $row['category_id'],
			authorId: (int) $row['author_id'],
			title: $row['title'],
			slug: $row['slug'],
			perex: $row['perex'],
			content: $row['content'],
			coverImage: $row['cover_image'],
			status: $row['status'],
			publishedAt: $row['published_at'] ?? NULL,
			createdAt: $row['created_at'],
			categoryName: $row['category_name'] ?? NULL,
			categorySlug: $row['category_slug'] ?? NULL,
			authorName: $row['author_name'] ?? NULL,
		);
	}

	public function isPublished(): bool {
		return $this->status === 'published';
	}

	/**
	 * Vrátí publikovaný datum ve formátu "12. 5. 2026", nebo prázdný řetězec
	 * pokud článek není publikovaný.
	 */
	public function getFormattedPublishedAt(): string {
		if ($this->publishedAt === NULL) {
			return '';
		}

		return (new DateTimeImmutable($this->publishedAt))->format('j. n. Y');
	}

}
