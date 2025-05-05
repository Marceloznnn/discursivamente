<?php
// src/Repositories/EnrollmentRepository.php

namespace Repositories;

use PDO;

class EnrollmentRepository
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Cria uma solicitação (status = pendente)
     */
    public function create(int $userId, int $classroomId, ?string $info): int
    {
        $stmt = $this->pdo->prepare(
            "INSERT INTO enrollments
             (user_id, classroom_id, status, informacoes_adicionais, data_solicitacao, created_at, updated_at)
             VALUES
             (:userId, :classroomId, 'pendente', :info, NOW(), NOW(), NOW())"
        );
        $stmt->execute([
            'userId'      => $userId,
            'classroomId' => $classroomId,
            'info'        => $info
        ]);
        return (int) $this->pdo->lastInsertId();
    }
    public function countPendingByProfessor(int $professorId): array
    {
        $stmt = $this->pdo->prepare(
            "SELECT c.id AS classroom_id,
                    c.nome AS classroom_name,
                    COUNT(e.id) AS pending_count
             FROM classrooms c
             LEFT JOIN enrollments e
               ON e.classroom_id = c.id AND e.status = 'pendente'
             WHERE c.professor_id = :professorId
             GROUP BY c.id, c.nome
             HAVING pending_count > 0"
        );
        $stmt->execute(['professorId' => $professorId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Busca solicitações pendentes de uma turma
     */
    public function findPendingByClassroom(int $classroomId): array
    {
        $stmt = $this->pdo->prepare(
            "SELECT
                e.id,
                e.informacoes_adicionais,
                DATE_FORMAT(e.data_solicitacao, '%d/%m/%Y %H:%i') AS data_solicitacao,
                u.name   AS user_name,
                c.nome   AS classroom_name,
                e.classroom_id
             FROM enrollments e
             JOIN users u ON e.user_id = u.id
             JOIN classrooms c ON e.classroom_id = c.id
             WHERE e.classroom_id = :classroomId
               AND e.status = 'pendente'
             ORDER BY e.data_solicitacao DESC"
        );
        $stmt->execute(['classroomId' => $classroomId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Aprova uma solicitação
     */
    public function approve(int $id): void
    {
        $stmt = $this->pdo->prepare(
            "UPDATE enrollments
             SET status = 'aprovado',
                 data_aprovacao = NOW(),
                 updated_at    = NOW()
             WHERE id = :id"
        );
        $stmt->execute(['id' => $id]);
    }

    /**
     * Recusa uma solicitação com motivo opcional
     */
    public function reject(int $id, ?string $motivo): void
    {
        $stmt = $this->pdo->prepare(
            "UPDATE enrollments
             SET status       = 'recusado',
                 data_recusa  = NOW(),
                 motivo_recusa = :motivo,
                 updated_at   = NOW()
             WHERE id = :id"
        );
        $stmt->execute([
            'id'     => $id,
            'motivo' => $motivo
        ]);
    }
}
