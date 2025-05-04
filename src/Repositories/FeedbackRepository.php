<?php

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

    /** Retorna todos os feedbacks, mais recentes primeiro */
    public function findAll(): array
    {
        $stmt = $this->pdo->query(
            'SELECT * FROM feedbacks ORDER BY created_at DESC'
        );
        $rows = $stmt->fetchAll();
        return array_map(fn($row) => $this->hydrate($row), $rows);
    }

    /** Busca feedback por ID */
    public function findById(int $id): ?Feedback
    {
        $stmt = $this->pdo->prepare(
            'SELECT * FROM feedbacks WHERE id = :id LIMIT 1'
        );
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch();
        return $row ? $this->hydrate($row) : null;
    }

    /** Busca feedbacks por status ('pending','resolved','rejected') */
    public function findByStatus(string $status): array
    {
        $stmt = $this->pdo->prepare(
            'SELECT * FROM feedbacks WHERE status = :status ORDER BY created_at DESC'
        );
        $stmt->execute([':status' => $status]);
        $rows = $stmt->fetchAll();
        return array_map(fn($row) => $this->hydrate($row), $rows);
    }

    /** Insere ou atualiza um feedback */
    public function save(Feedback $fb): void
    {
        if ($fb->getId()) {
            $stmt = $this->pdo->prepare(
                'UPDATE feedbacks
                 SET message    = :message,
                     rating     = :rating,
                     status     = :status,
                     updated_at = NOW()
                 WHERE id = :id'
            );
            $stmt->execute([
                ':message' => $fb->getMessage(),
                ':rating'  => $fb->getRating(),
                ':status'  => $fb->getStatus(),
                ':id'      => $fb->getId(),
            ]);
        } else {
            $stmt = $this->pdo->prepare(
                'INSERT INTO feedbacks
                 (user_id, message, rating, status, created_at)
                 VALUES
                 (:userId, :message, :rating, :status, NOW())'
            );
            $stmt->execute([
                ':userId'  => $fb->getUserId(),
                ':message' => $fb->getMessage(),
                ':rating'  => $fb->getRating(),
                ':status'  => $fb->getStatus(),
            ]);
        }
    }

    /** Remove um feedback */
    public function delete(int $id): void
    {
        $stmt = $this->pdo->prepare(
            'DELETE FROM feedbacks WHERE id = :id'
        );
        $stmt->execute([':id' => $id]);
    }

    /** Converte linha de BD num objeto Feedback */
    private function hydrate(array $row): Feedback
    {
        return new Feedback(
            (int)$row['user_id'],
            $row['message'],
            $row['rating'] !== null ? (int)$row['rating'] : null,
            $row['status'],
            (int)$row['id'],
            $row['created_at'],
            $row['updated_at'] ?? null
        );
    }
}
