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
        // Alteração: usando a coluna correta 'event_date'
        $stmt = $this->pdo->query('SELECT * FROM events ORDER BY event_date DESC');
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
            $stmt = $this->pdo->prepare(
                'UPDATE events SET title = :title, description = :description, event_date = :dateTime, visibility = :visibility, image = :image, updated_at = NOW() WHERE id = :id'
            );
            $stmt->execute([
                ':title' => $event->getTitle(),
                ':description' => $event->getDescription(),
                ':dateTime' => $event->getDateTime(),
                ':visibility' => $event->getVisibility(),
                ':image' => $event->getImage(),
                ':id' => $event->getId(),
            ]);
        } else {
            $stmt = $this->pdo->prepare(
                'INSERT INTO events (title, description, event_date, visibility, image, created_at, updated_at) VALUES (:title, :description, :dateTime, :visibility, :image, NOW(), NOW())'
            );
            $stmt->execute([
                ':title' => $event->getTitle(),
                ':description' => $event->getDescription(),
                ':dateTime' => $event->getDateTime(),
                ':visibility' => $event->getVisibility(),
                ':image' => $event->getImage(),
            ]);
        }
    }

    public function delete(int $id): void
    {
        $stmt = $this->pdo->prepare('DELETE FROM events WHERE id = :id');
        $stmt->execute([':id' => $id]);
    }

    private function hydrate(array $row): Event
    {
        return new Event(
            $row['title'],
            $row['description'],
            $row['event_date'],  // Correção: usando 'event_date' ao invés de 'date_time'
            $row['visibility'],
            $row['image'],
            (int)$row['id'],            
            $row['created_at'],
            $row['updated_at']
        );
    }
}


use App\Models\SupportMessage;

class SupportMessageRepository
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function findAll(): array
    {
        $stmt = $this->pdo->query('SELECT * FROM support_messages ORDER BY created_at DESC');
        return array_map([$this, 'hydrate'], $stmt->fetchAll());
    }

    public function findById(int $id): ?SupportMessage
    {
        $stmt = $this->pdo->prepare('SELECT * FROM support_messages WHERE id = :id LIMIT 1');
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch();
        return $row ? $this->hydrate($row) : null;
    }

    public function save(SupportMessage $msg): void
    {
        if ($msg->getId()) {
            $stmt = $this->pdo->prepare(
                'UPDATE support_messages SET status = :status, response = :response, updated_at = NOW() WHERE id = :id'
            );
            $stmt->execute([  
                ':status' => $msg->getStatus(),
                ':response' => $msg->getResponse(),
                ':id' => $msg->getId(),
            ]);
        } else {
            $stmt = $this->pdo->prepare(
                'INSERT INTO support_messages (user_id, subject, message, status, created_at, updated_at) VALUES (:userId, :subject, :message, :status, NOW(), NOW())'
            );
            $stmt->execute([
                ':userId' => $msg->getUserId(),
                ':subject' => $msg->getSubject(),
                ':message' => $msg->getMessage(),
                ':status' => $msg->getStatus(),
            ]);
        }
    }

    private function hydrate(array $row): SupportMessage
    {
        return new SupportMessage(
            (int)$row['user_id'],            
            $row['subject'],
            $row['message'],
            $row['status'],
            $row['response'] ?? null,
            (int)$row['id'],
            $row['created_at'],
            $row['updated_at']
        );
    }
}
