<?php
// filepath: src/Repositories/FeedbackRepository.php

namespace Repositories;

use App\Models\Feedback;
use PDO;

class FeedbackRepository
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function findAll(): array
    {
        $stmt = $this->pdo->query('SELECT * FROM feedback ORDER BY created_at DESC');
        return array_map([$this, 'hydrate'], $stmt->fetchAll());
    }

    public function findByStatus(string $status): array
    {
        $stmt = $this->pdo->prepare('SELECT * FROM feedback WHERE status = :status ORDER BY created_at DESC');
        $stmt->execute([':status' => $status]);
        return array_map([$this, 'hydrate'], $stmt->fetchAll());
    }

    public function findById(int $id): ?Feedback
    {
        $stmt = $this->pdo->prepare('SELECT * FROM feedback WHERE id = :id LIMIT 1');
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch();
        return $row ? $this->hydrate($row) : null;
    }

    public function save(Feedback $feedback): void
    {
        if ($feedback->getId()) {
            $stmt = $this->pdo->prepare(
                'UPDATE feedback
                    SET user_id    = :user_id,
                        message    = :message,
                        status     = :status,
                        updated_at = NOW()
                  WHERE id = :id'
            );
            $stmt->execute([
                ':user_id' => $feedback->getUserId(),
                ':message' => $feedback->getMessage(),
                ':status'  => $feedback->getStatus(),
                ':id'      => $feedback->getId(),
            ]);
        } else {
            $stmt = $this->pdo->prepare(
                'INSERT INTO feedback
                    (user_id, message, status, created_at, updated_at)
                 VALUES
                    (:user_id, :message, :status, NOW(), NOW())'
            );
            $stmt->execute([
                ':user_id' => $feedback->getUserId(),
                ':message' => $feedback->getMessage(),
                ':status'  => $feedback->getStatus(),
            ]);
            $feedback->setId((int)$this->pdo->lastInsertId());
        }
    }

    public function delete(int $id): void
    {
        $stmt = $this->pdo->prepare('DELETE FROM feedback WHERE id = :id');
        $stmt->execute([':id' => $id]);
    }

    private function hydrate(array $row): Feedback
    {
        return new Feedback(
            (int)$row['id'],
            (int)$row['user_id'],
            $row['message'],
            $row['status'],
            $row['created_at'],
            $row['updated_at']
        );
    }
}
