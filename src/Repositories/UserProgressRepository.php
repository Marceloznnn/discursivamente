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

    /**
     * Retorna todos os registros de progresso de um usuário
     *
     * @param int $userId
     * @return UserProgress[]
     */
    public function findByUserId(int $userId): array
    {
        $stmt = $this->pdo->prepare(
            'SELECT * FROM user_progress WHERE user_id = :userId ORDER BY completed_at'
        );
        $stmt->execute([':userId' => $userId]);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return array_map(fn(array $row) => $this->hydrate($row), $rows);
    }

    /**
     * Retorna todos os registros de progresso de um material
     *
     * @param int $materialId
     * @return UserProgress[]
     */
    public function findByMaterialId(int $materialId): array
    {
        $stmt = $this->pdo->prepare(
            'SELECT * FROM user_progress WHERE material_id = :materialId ORDER BY completed_at'
        );
        $stmt->execute([':materialId' => $materialId]);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return array_map(fn(array $row) => $this->hydrate($row), $rows);
    }

    /**
     * Verifica se já existe um registro de progresso para usuário+material
     *
     * @param int $userId
     * @param int $materialId
     * @return UserProgress|null
     */
    public function find(int $userId, int $materialId): ?UserProgress
    {
        $stmt = $this->pdo->prepare(
            'SELECT * FROM user_progress WHERE user_id = :userId AND material_id = :materialId LIMIT 1'
        );
        $stmt->execute([':userId' => $userId, ':materialId' => $materialId]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row ? $this->hydrate($row) : null;
    }

    /**
     * Marca um material como concluído (ou atualiza timestamp se já existir)
     */
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

    /**
     * Remove o registro de conclusão de um material para um usuário
     */
    public function delete(int $userId, int $materialId): void
    {
        $stmt = $this->pdo->prepare(
            'DELETE FROM user_progress WHERE user_id = :userId AND material_id = :materialId'
        );
        $stmt->execute([':userId' => $userId, ':materialId' => $materialId]);
    }

    /**
     * Obtém o ID do módulo associado a um material
     *
     * @param int $materialId
     * @return int|null
     */
    public function findModuleIdByMaterial(int $materialId): ?int
    {
        $stmt = $this->pdo->prepare(
            'SELECT module_id
               FROM material_entries
              WHERE id = :materialId
              LIMIT 1'
        );
        $stmt->execute([':materialId' => $materialId]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row['module_id'] ?? null;
    }

    /**
     * Conta quantos materiais existem em um módulo
     *
     * @param int $moduleId
     * @return int
     */
    public function countMaterialsInModule(int $moduleId): int
    {
        $stmt = $this->pdo->prepare(
            'SELECT COUNT(*) FROM material_entries WHERE module_id = :moduleId'
        );
        $stmt->execute([':moduleId' => $moduleId]);
        return (int)$stmt->fetchColumn();
    }

    /**
     * Conta quantos materiais de um módulo já foram concluídos por um usuário
     *
     * @param int $userId
     * @param int $moduleId
     * @return int
     */
    public function countCompletedInModule(int $userId, int $moduleId): int
    {
        $stmt = $this->pdo->prepare(
            'SELECT COUNT(*) 
               FROM user_progress p
         INNER JOIN material_entries e ON e.id = p.material_id
              WHERE p.user_id   = :userId
                AND e.module_id = :moduleId'
        );
        $stmt->execute([
            ':userId'   => $userId,
            ':moduleId' => $moduleId,
        ]);
        return (int)$stmt->fetchColumn();
    }

    /**
     * Busca um progresso específico por usuário e material
     */
    public function findByUserAndMaterial(int $userId, int $materialId): ?UserProgress
    {
        $stmt = $this->pdo->prepare("
            SELECT * FROM user_progress 
            WHERE user_id = ? AND material_id = ?
        ");
        $stmt->execute([$userId, $materialId]);
        
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$row) {
            return null;
        }

        return new UserProgress(
            $row['user_id'],
            $row['material_id'],
            $row['completed_at']
        );
    }

    /**
     * Mapeia array de dados para instância de UserProgress
     */
    private function hydrate(array $row): UserProgress
    {
        return new UserProgress(
            (int) $row['user_id'], 
            (int) $row['material_id'], 
            $row['completed_at']
        );
    }
}