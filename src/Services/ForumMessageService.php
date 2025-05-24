<?php

namespace Services;

use PDO;

class ForumMessageService
{
    private PDO $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    public function saveMessage(int $userId, int $courseId, string $message): bool
    {
        $stmt = $this->db->prepare(
            "INSERT INTO forum_messages (user_id, course_id, message, created_at) VALUES (?, ?, ?, NOW())"
        );
        return $stmt->execute([$userId, $courseId, $message]);
    }    public function getMessages(int $courseId, int $limit = 50): array
    {
        $stmt = $this->db->prepare(
            "SELECT fm.*, u.name as user_name, u.avatar as user_avatar
             FROM forum_messages fm
             JOIN users u ON u.id = fm.user_id
             WHERE fm.course_id = :course_id
             ORDER BY fm.created_at DESC
             LIMIT :limit"
        );
        $stmt->bindParam(':course_id', $courseId, PDO::PARAM_INT);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
