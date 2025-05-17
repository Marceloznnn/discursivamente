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
        $this->log("findByUserId chamado para userId=$userId");
        try {
            $stmt = $this->pdo->prepare(
                'SELECT * FROM user_progress WHERE user_id = :userId ORDER BY completed_at'
            );
            $stmt->execute([':userId' => $userId]);
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $this->log("findByUserId retornou " . count($rows) . " registros para userId=$userId");
            return array_map(fn(array $row) => $this->hydrate($row), $rows);
        } catch (\Throwable $e) {
            $this->log("Erro em findByUserId: " . $e->getMessage());
            return [];
        }
    }

    public function findByMaterialId(int $materialId): array
    {
        $this->log("findByMaterialId chamado para materialId=$materialId");
        try {
            $stmt = $this->pdo->prepare(
                'SELECT * FROM user_progress WHERE material_id = :materialId ORDER BY completed_at'
            );
            $stmt->execute([':materialId' => $materialId]);
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $this->log("findByMaterialId retornou " . count($rows) . " registros para materialId=$materialId");
            return array_map(fn(array $row) => $this->hydrate($row), $rows);
        } catch (\Throwable $e) {
            $this->log("Erro em findByMaterialId: " . $e->getMessage());
            return [];
        }
    }

    public function find($userId, $materialId, $courseId = null): ?UserProgress
    {
        $this->log("find chamado para userId=$userId, materialId=$materialId");
        try {
            $sql = 'SELECT * FROM user_progress WHERE user_id = :userId AND material_id = :materialId LIMIT 1';
            $params = [':userId' => $userId, ':materialId' => $materialId];
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $this->log($row ? "find encontrou registro." : "find não encontrou registro.");
            return $row ? $this->hydrate($row) : null;
        } catch (\Throwable $e) {
            $this->log("Erro em find: " . $e->getMessage());
            return null;
        }
    }

    public function save(UserProgress $progress): void
    {
        $this->log("save chamado para userId=" . $progress->getUserId() . ", materialId=" . $progress->getMaterialId());
        try {
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
            $this->log("save executado com sucesso para userId=" . $progress->getUserId() . ", materialId=" . $progress->getMaterialId());
        } catch (\Throwable $e) {
            $this->log("Erro em save: " . $e->getMessage());
        }
    }

    public function delete($userId, $materialId, $courseId = null): void
    {
        $this->log("delete chamado para userId=$userId, materialId=$materialId");
        try {
            $sql = 'DELETE FROM user_progress WHERE user_id = :userId AND material_id = :materialId';
            $params = [':userId' => $userId, ':materialId' => $materialId];
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
            $this->log("delete executado com sucesso para userId=$userId, materialId=$materialId");
        } catch (\Throwable $e) {
            $this->log("Erro em delete: " . $e->getMessage());
        }
    }

    private function hydrate(array $row): UserProgress
    {
        return new UserProgress(
            (int) $row['user_id'],        // userId
            (int) $row['material_id'],    // materialId
            $row['completed_at'] ?? null  // completedAt
        );
    }

    // Função auxiliar para log
    private function log(string $message): void
    {
        $logDir = __DIR__ . '/../../../logs';
        if (!is_dir($logDir)) {
            mkdir($logDir, 0777, true);
        }
        $logFile = $logDir . '/user_progress.log';
        $date = date('Y-m-d H:i:s');
        file_put_contents($logFile, "[$date] $message\n", FILE_APPEND);
    }
}