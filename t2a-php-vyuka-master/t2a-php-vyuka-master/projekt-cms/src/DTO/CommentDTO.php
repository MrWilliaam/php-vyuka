<?php

declare(strict_types=1);

readonly class CommentDTO {

	public function __construct(
		public int $id,
		public int $articleId,
		public string $authorName,
		public string $authorEmail,
		public string $content,
		public string $status,
		public string $createdAt,
	) {
	}

	/**
	 * @param array<string, mixed> $row
	 */
	public static function fromRow(array $row): self {
		return new self(
			id: (int) $row['id'],
			articleId: (int) $row['article_id'],
			authorName: $row['author_name'],
			authorEmail: $row['author_email'],
			content: $row['content'],
			status: $row['status'],
			createdAt: $row['created_at'],
		);
	}

	public function isApproved(): bool {
		return $this->status === 'approved';
	}

	public function isPending(): bool {
		return $this->status === 'pending';
	}

	/**
	 * Vrátí datum ve formátu "12. 5. 2026 14:30".
	 */
	public function getFormattedCreatedAt(): string {
		return (new DateTimeImmutable($this->createdAt))->format('j. n. Y H:i');
	}

}
