<?php

namespace Repositories;

use PDO;

class ForumReplyRepository
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function getByTopicId(int $topicId): array
    {
        $stmt = $this->pdo->prepare(
            "SELECT r.*, u.name as author_name 
             FROM forum_replies r
             JOIN users u ON u.id = r.user_id
             WHERE r.topic_id = ?
             ORDER BY r.created_at ASC"
        );
        $stmt->execute([$topicId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function create(int $topicId, int $userId, string $content): bool
    {
        $stmt = $this->pdo->prepare(
            "INSERT INTO forum_replies (topic_id, user_id, content)
             VALUES (?, ?, ?)"
        );
        return $stmt->execute([$topicId, $userId, $content]);
    }

    public function update(int $replyId, int $userId, string $content): bool
    {
        $stmt = $this->pdo->prepare(
            "UPDATE forum_replies 
             SET content = ?
             WHERE id = ? AND user_id = ?"
        );
        return $stmt->execute([$content, $replyId, $userId]);
    }

    public function delete(int $replyId, int $userId): bool
    {
        $stmt = $this->pdo->prepare(
            "DELETE FROM forum_replies 
             WHERE id = ? AND user_id = ?"
        );
        return $stmt->execute([$replyId, $userId]);
    }
}
