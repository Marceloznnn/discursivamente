<?php
namespace Repositories;

use App\Models\Event;
use PDO;

class EventRepository
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function findAll(): array
    {
        $sql = "SELECT * FROM events
                ORDER BY is_featured DESC,
                         feature_priority DESC,
                         event_date DESC";
        $stmt = $this->pdo->query($sql);
        return array_map([$this, 'hydrate'], $stmt->fetchAll());
    }

    public function findById(int $id): ?Event
    {
        $stmt = $this->pdo->prepare('SELECT * FROM events WHERE id = :id LIMIT 1');
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch();
        return $row ? $this->hydrate($row) : null;
    }

    public function save(Event $event): void
    {
        if ($event->getId()) {
            $sql = "UPDATE events SET
                      title = :title,
                      description = :description,
                      event_date = :dateTime,
                      visibility = :visibility,
                      image = :image,
                      is_featured = :isFeatured,
                      feature_priority = :featurePriority,
                      updated_at = NOW()
                    WHERE id = :id";
            $params = [
                ':title'           => $event->getTitle(),
                ':description'     => $event->getDescription(),
                ':dateTime'        => $event->getDateTime(),
                ':visibility'      => $event->getVisibility(),
                ':image'           => $event->getImage(),
                ':isFeatured'      => $event->getIsFeatured() ? 1 : 0,
                ':featurePriority' => $event->getFeaturePriority(),
                ':id'              => $event->getId(),
            ];
        } else {
            $sql = "INSERT INTO events
                      (title, description, event_date, visibility, image,
                       is_featured, feature_priority, created_at, updated_at)
                    VALUES
                      (:title, :description, :dateTime, :visibility, :image,
                       :isFeatured, :featurePriority, NOW(), NOW())";
            $params = [
                ':title'           => $event->getTitle(),
                ':description'     => $event->getDescription(),
                ':dateTime'        => $event->getDateTime(),
                ':visibility'      => $event->getVisibility(),
                ':image'           => $event->getImage(),
                ':isFeatured'      => $event->getIsFeatured() ? 1 : 0,
                ':featurePriority' => $event->getFeaturePriority(),
            ];
        }
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
    }

    public function delete(int $id): void
    {
        $stmt = $this->pdo->prepare('DELETE FROM events WHERE id = :id');
        $stmt->execute([':id' => $id]);
    }

    /**
     * Retorna o total de eventos cadastrados
     */
    public function countAll(): int
    {
        $stmt = $this->pdo->query('SELECT COUNT(*) FROM events');
        return (int) $stmt->fetchColumn();
    }

    private function hydrate(array $row): Event
    {
        return new Event(
            $row['title'],
            $row['description'],
            $row['event_date'],
            $row['visibility'],
            $row['image'],
            (bool)$row['is_featured'],
            (int)$row['feature_priority'],
            (int)$row['id'],            
            $row['created_at'],
            $row['updated_at']
        );
    }
}
