<?php

namespace Repositories;

use PDO;

class ForumTopicRepository
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function findByIdAndCourse(int $topicId, int $courseId)
    {
        $stmt = $this->pdo->prepare(
            "SELECT * FROM forum_topics WHERE id = ? AND course_id = ?"
        );
        $stmt->execute([$topicId, $courseId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
