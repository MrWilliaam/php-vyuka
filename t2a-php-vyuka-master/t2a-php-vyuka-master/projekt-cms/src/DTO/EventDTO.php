<?php

declare(strict_types=1);

readonly class EventDTO {

	public function __construct(
		public int $id,
		public string $title,
		public string $slug,
		public string $eventDate,
		public string $location,
		public string $description,
		public string $image,
		public string $createdAt,
	) {
	}

	/**
	 * @param array<string, mixed> $row
	 */
	public static function fromRow(array $row): self {
		return new self(
			id: (int) $row['id'],
			title: $row['title'],
			slug: $row['slug'],
			eventDate: $row['event_date'],
			location: $row['location'],
			description: $row['description'],
			image: $row['image'],
			createdAt: $row['created_at'],
		);
	}

	/**
	 * Datum události ve formátu "12. 5. 2026".
	 */
	public function getFormattedDate(): string {
		return (new DateTimeImmutable($this->eventDate))->format('j. n. Y');
	}

	/**
	 * Je akce v budoucnosti (včetně dneška)?
	 */
	public function isUpcoming(): bool {
		$today = (new DateTimeImmutable('today'))->format('Y-m-d');
		return $this->eventDate >= $today;
	}

}
