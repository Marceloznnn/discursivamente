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

    /** Retorna todos os eventos, ordenados pela data mais próxima */
    public function findAll(): array
    {
        $stmt = $this->pdo->query(
            'SELECT * FROM events ORDER BY event_date ASC'
        );
        $rows = $stmt->fetchAll();
        return array_map(fn($row) => $this->hydrate($row), $rows);
    }

    /** Busca um evento por ID */
    public function findById(int $id): ?Event
    {
        $stmt = $this->pdo->prepare(
            'SELECT * FROM events WHERE id = :id LIMIT 1'
        );
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch();
        return $row ? $this->hydrate($row) : null;
    }

    /** Insere ou atualiza um evento */
    public function save(Event $event): void
    {
        if ($event->getId()) {
            $stmt = $this->pdo->prepare(
                'UPDATE events
                 SET title       = :title,
                     description = :description,
                     event_date  = :dateTime,
                     visibility  = :visibility,
                     image       = :image,
                     updated_at  = NOW()
                 WHERE id = :id'
            );
            $stmt->execute([
                ':title'       => $event->getTitle(),
                ':description' => $event->getDescription(),
                ':dateTime'    => $event->getDateTime(),
                ':visibility'  => $event->getVisibility(),
                ':image'       => $event->getImage(),
                ':id'          => $event->getId(),
            ]);
        } else {
            $stmt = $this->pdo->prepare(
                'INSERT INTO events
                 (title, description, event_date, visibility, image, created_at, updated_at)
                 VALUES
                 (:title, :description, :dateTime, :visibility, :image, NOW(), NOW())'
            );
            $stmt->execute([
                ':title'       => $event->getTitle(),
                ':description' => $event->getDescription(),
                ':dateTime'    => $event->getDateTime(),
                ':visibility'  => $event->getVisibility(),
                ':image'       => $event->getImage(),
            ]);
            // se quiser recuperar o ID gerado:
            // $eventId = (int)$this->pdo->lastInsertId();
        }
    }

    /** Remove um evento */
    public function delete(int $id): void
    {
        $stmt = $this->pdo->prepare(
            'DELETE FROM events WHERE id = :id'
        );
        $stmt->execute([':id' => $id]);
    }

    /** Hidrata o array de dados num objeto Event */
    private function hydrate(array $row): Event
    {
        return new Event(
            $row['title'],
            $row['description'],
            $row['event_date'],
            $row['visibility'],
            $row['image'] ?? null,
            (int)$row['id'],
            $row['created_at'],
            $row['updated_at']
        );
    }
}
