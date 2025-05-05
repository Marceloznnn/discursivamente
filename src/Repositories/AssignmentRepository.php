<?php

namespace Repositories;

use PDO;
use App\Models\Assignment;

class AssignmentRepository
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Busca todas as atividades de uma turma
     *
     * @param int $classroomId
     * @return array<int, array<string, mixed>>
     */
    public function findByClassroom(int $classroomId): array
    {
        $stmt = $this->pdo->prepare(
            "SELECT
                a.id,
                a.titulo,
                a.descricao,
                a.tipo,
                DATE_FORMAT(a.dataInicio, '%d/%m/%Y') AS data_inicio,
                DATE_FORMAT(a.dataFim, '%d/%m/%Y') AS data_fim,
                a.peso
             FROM assignments a
             WHERE a.classroom_id = :classroomId
             ORDER BY a.dataInicio DESC"
        );
        $stmt->execute(['classroomId' => $classroomId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Busca uma atividade por ID
     */
    public function findById(int $id): ?array
    {
        $stmt = $this->pdo->prepare("SELECT * FROM assignments WHERE id = :id");
        $stmt->execute(['id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    /**
     * Persiste uma nova atividade
     *
     * @param Assignment $assignment
     * @return int ID da atividade criada
     */
    public function create(Assignment $assignment): int
    {
        $stmt = $this->pdo->prepare(
            "INSERT INTO assignments
             (professor_id, classroom_id, titulo, descricao, tipo, dataInicio, dataFim, peso, configuracoes, created_at, updated_at)
             VALUES
             (:professorId, :classroomId, :titulo, :descricao, :tipo, :dataInicio, :dataFim, :peso, :configuracoes, NOW(), NOW())"
        );
        $stmt->execute([
            'professorId'     => $assignment->professorId,
            'classroomId'     => $assignment->classroomId,
            'titulo'          => $assignment->titulo,
            'descricao'       => $assignment->descricao,
            'tipo'            => $assignment->tipo,
            'dataInicio'      => $assignment->dataInicio,
            'dataFim'         => $assignment->dataFim,
            'peso'            => $assignment->peso,
            'configuracoes'   => $assignment->configuracoes
        ]);

        return (int) $this->pdo->lastInsertId();
    }

    /**
     * Atualiza uma atividade existente
     *
     * @param Assignment $assignment
     * @return void
     */
    public function update(Assignment $assignment): void
    {
        $stmt = $this->pdo->prepare(
            "UPDATE assignments
             SET titulo = :titulo,
                 descricao = :descricao,
                 tipo = :tipo,
                 dataInicio = :dataInicio,
                 dataFim = :dataFim,
                 peso = :peso,
                 configuracoes = :configuracoes,
                 updated_at = NOW()
             WHERE id = :id"
        );

        $stmt->execute([
            'titulo'        => $assignment->titulo,
            'descricao'     => $assignment->descricao,
            'tipo'          => $assignment->tipo,
            'dataInicio'    => $assignment->dataInicio,
            'dataFim'       => $assignment->dataFim,
            'peso'          => $assignment->peso,
            'configuracoes' => $assignment->configuracoes ?? '{}',
            'id'            => $assignment->id
        ]);
    }

    // TODO: adicionar método delete() conforme necessidade
}
