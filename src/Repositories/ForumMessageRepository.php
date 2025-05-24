<?php

namespace Repositories;

use PDO;
use App\Models\ForumMessage;

class ForumMessageRepository
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function findById(int $id): ?array
    {
        $stmt = $this->pdo->prepare(
            "SELECT m.*, u.name AS user_name, u.avatar AS user_avatar
             FROM forum_messages m
             JOIN users u ON u.id = m.user_id
             WHERE m.id = ?"
        );
        $stmt->execute([$id]);
        
        $data = $stmt->fetch(PDO::FETCH_ASSOC);
        return $data ?: null;
    }

    public function findByCourse(int $courseId, int $limit = 100): array
    {
        $stmt = $this->pdo->prepare(
            "SELECT m.*, u.name AS user_name, u.avatar AS user_avatar
             FROM forum_messages m
             JOIN users u ON u.id = m.user_id
             WHERE m.course_id = ?
             ORDER BY m.created_at ASC
             LIMIT ?"
        );
        $stmt->execute([$courseId, $limit]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function create(ForumMessage $message): int
    {
        $stmt = $this->pdo->prepare(
            "INSERT INTO forum_messages (user_id, course_id, message) 
             VALUES (?, ?, ?)"
        );
        
        $stmt->execute([
            $message->getUserId(),
            $message->getCourseId(),
            $message->getMessage()
        ]);
        
        return (int)$this->pdo->lastInsertId();
    }

    public function update(ForumMessage $message): bool
    {
        $stmt = $this->pdo->prepare(
            "UPDATE forum_messages 
             SET message = ? 
             WHERE id = ? AND user_id = ?"
        );
        
        return $stmt->execute([
            $message->getMessage(),
            $message->getId(),
            $message->getUserId()
        ]);
    }

    public function delete(int $id, int $userId): bool
    {
        $stmt = $this->pdo->prepare(
            "DELETE FROM forum_messages 
             WHERE id = ? AND user_id = ?"
        );
        
        return $stmt->execute([$id, $userId]);
    }
}
