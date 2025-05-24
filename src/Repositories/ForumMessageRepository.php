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

    public function findById(int $id): ?ForumMessage
    {
        $stmt = $this->pdo->prepare("SELECT * FROM forum_messages WHERE id = ?");
        $stmt->execute([$id]);
        
        if ($data = $stmt->fetch(PDO::FETCH_ASSOC)) {
            return new ForumMessage($data);
        }
        
        return null;
    }

    public function findByCourse(int $courseId, int $limit = 100): array
    {
        $stmt = $this->pdo->prepare(
            "SELECT * FROM forum_messages 
             WHERE course_id = ? 
             ORDER BY created_at DESC 
             LIMIT ?"
        );
        $stmt->execute([$courseId, $limit]);
        
        $messages = [];
        while ($data = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $messages[] = new ForumMessage($data);
        }
        
        return $messages;
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
