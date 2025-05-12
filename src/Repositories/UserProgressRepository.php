<?php

namespace Repositories;

use App\Models\UserProgress;
use PDO;

class UserProgressRepository
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function findByUserId(int $userId): array
    {
        $stmt = $this->pdo->prepare(
            'SELECT * FROM user_progress WHERE user_id = :userId ORDER BY completed_at'
        );
        $stmt->execute([':userId' => $userId]);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return array_map(fn(array $row) => $this->hydrate($row), $rows);
    }

    public function findByMaterialId(int $materialId): array
    {
        $stmt = $this->pdo->prepare(
            'SELECT * FROM user_progress WHERE material_id = :materialId ORDER BY completed_at'
        );
        $stmt->execute([':materialId' => $materialId]);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return array_map(fn(array $row) => $this->hydrate($row), $rows);
    }

    public function find(int $userId, int $materialId): ?UserProgress
    {
        $stmt = $this->pdo->prepare(
            'SELECT * FROM user_progress WHERE user_id = :userId AND material_id = :materialId LIMIT 1'
        );
        $stmt->execute([':userId' => $userId, ':materialId' => $materialId]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row ? $this->hydrate($row) : null;
    }

    public function save(UserProgress $progress): void
    {
        $stmt = $this->pdo->prepare(
            'INSERT INTO user_progress (user_id, material_id, completed_at)
             VALUES (:userId, :materialId, :completedAt)
             ON DUPLICATE KEY UPDATE completed_at = :completedAt'
        );
        $stmt->execute([
            ':userId'      => $progress->getUserId(),
            ':materialId'  => $progress->getMaterialId(),
            ':completedAt' => $progress->getCompletedAt(),
        ]);
    }

    public function delete(int $userId, int $materialId): void
    {
        $stmt = $this->pdo->prepare(
            'DELETE FROM user_progress WHERE user_id = :userId AND material_id = :materialId'
        );
        $stmt->execute([':userId' => $userId, ':materialId' => $materialId]);
    }

    private function hydrate(array $row): UserProgress
    {
        return new UserProgress(
            (int) $row['user_id'],        // userId
            (int) $row['material_id'],    // materialId
            $row['completed_at'] ?? null  // completedAt
        );
    }
}