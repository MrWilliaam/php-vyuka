<?php

declare(strict_types=1);

final class EventRepository {

	private PDO $db;

	public function __construct() {
		$this->db = Database::getConnection();
	}

	/**
	 * Vrátí všechny události.
	 *
	 * @return list<EventDTO>
	 */
	public function getAll(): array {
		$stmt = $this->db->query('SELECT * FROM events ORDER BY event_date DESC');

		return array_map(EventDTO::fromRow(...), $stmt->fetchAll());
	}

	/**
	 * Najde událost podle ID.
	 */
	public function getById(int $id): ?EventDTO {
		$stmt = $this->db->prepare('SELECT * FROM events WHERE id = :id');
		$stmt->execute(['id' => $id]);

		$row = $stmt->fetch();

		return $row ? EventDTO::fromRow($row) : NULL;
	}

	/**
	 * Najde událost podle slugu.
	 */
	public function getBySlug(string $slug): ?EventDTO {
		$stmt = $this->db->prepare('SELECT * FROM events WHERE slug = :slug');
		$stmt->execute(['slug' => $slug]);

		$row = $stmt->fetch();

		return $row ? EventDTO::fromRow($row) : NULL;
	}

	/**
	 * Vrátí nadcházející události (od dneška).
	 *
	 * @return list<EventDTO>
	 */
	public function getUpcoming(int $limit = 0): array {
		$today = (new DateTimeImmutable('today'))->format('Y-m-d');

		$sql = '
			SELECT * FROM events
			WHERE event_date >= :today
			ORDER BY event_date ASC
		';

		if ($limit > 0) {
			$sql .= ' LIMIT :limit';
			$stmt = $this->db->prepare($sql);
			$stmt->bindValue('today', $today);
			$stmt->bindValue('limit', $limit, PDO::PARAM_INT);
			$stmt->execute();
		} else {
			$stmt = $this->db->prepare($sql);
			$stmt->execute(['today' => $today]);
		}

		return array_map(EventDTO::fromRow(...), $stmt->fetchAll());
	}

	/**
	 * Vrátí proběhlé události.
	 *
	 * @return list<EventDTO>
	 */
	public function getPast(): array {
		$today = (new DateTimeImmutable('today'))->format('Y-m-d');

		$stmt = $this->db->prepare('
			SELECT * FROM events
			WHERE event_date < :today
			ORDER BY event_date DESC
		');
		$stmt->execute(['today' => $today]);

		return array_map(EventDTO::fromRow(...), $stmt->fetchAll());
	}

	/**
	 * Vytvoří novou událost.
	 */
	public function create(
		string $title,
		string $slug,
		string $eventDate,
		string $location,
		string $description,
		string $image,
	): EventDTO {
		$stmt = $this->db->prepare('
			INSERT INTO events (title, slug, event_date, location, description, image)
			VALUES (:title, :slug, :date, :location, :description, :image)
		');
		$stmt->execute([
			'title'       => $title,
			'slug'        => $slug,
			'date'        => $eventDate,
			'location'    => $location,
			'description' => $description,
			'image'       => $image,
		]);

		return $this->getById((int) $this->db->lastInsertId())
			?? throw new \RuntimeException('Nepodařilo se vytvořit událost.');
	}

	/**
	 * Smaže událost.
	 */
	public function delete(int $id): void {
		$stmt = $this->db->prepare('DELETE FROM events WHERE id = :id');
		$stmt->execute(['id' => $id]);
	}

}
