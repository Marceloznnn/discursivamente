<?php

namespace Repositories;

use App\Models\CourseComment;
use PDO;

class CourseCommentRepository
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function findById(int $id): ?CourseComment
    {
        $stmt = $this->pdo->prepare('SELECT * FROM course_comments WHERE id = :id LIMIT 1');
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ? $this->hydrate($row) : null;
    }

    public function findByCourseId(int $courseId): array
    {
        $stmt = $this->pdo->prepare('SELECT * FROM course_comments WHERE course_id = :courseId ORDER BY created_at DESC');
        $stmt->execute([':courseId' => $courseId]);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return array_map(fn($r) => $this->hydrate($r), $rows);
    }

    public function findByUserId(int $userId): array
    {
        $stmt = $this->pdo->prepare('SELECT * FROM course_comments WHERE user_id = :userId ORDER BY created_at DESC');
        $stmt->execute([':userId' => $userId]);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return array_map(fn($r) => $this->hydrate($r), $rows);
    }

    public function findAll(): array
    {
        $stmt = $this->pdo->query('SELECT * FROM course_comments ORDER BY created_at DESC');
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return array_map(fn($r) => $this->hydrate($r), $rows);
    }

    public function save(CourseComment $comment): void
    {
        if ($comment->getId()) {
            $stmt = $this->pdo->prepare(
                'UPDATE course_comments
                 SET comment    = :comment,
                     rating     = :rating
                 WHERE id = :id'
            );
            $stmt->execute([
                ':comment' => $comment->getComment(),
                ':rating'  => $comment->getRating(),
                ':id'      => $comment->getId(),
            ]);
        } else {
            $stmt = $this->pdo->prepare(
                'INSERT INTO course_comments 
                 (course_id, user_id, comment, rating, created_at)
                 VALUES (:courseId, :userId, :comment, :rating, NOW())'
            );
            $stmt->execute([
                ':courseId' => $comment->getCourseId(),
                ':userId'   => $comment->getUserId(),
                ':comment'  => $comment->getComment(),
                ':rating'   => $comment->getRating(),
            ]);
        }
    }

    public function delete(int $id): void
    {
        $stmt = $this->pdo->prepare('DELETE FROM course_comments WHERE id = :id');
        $stmt->execute([':id' => $id]);
    }

    private function hydrate(array $row): CourseComment
    {
        return new CourseComment(
            (int)$row['course_id'],
            (int)$row['user_id'],
            $row['comment'],
            (int)$row['rating'],
            (int)$row['id'],
            $row['created_at']
        );
    }
}
