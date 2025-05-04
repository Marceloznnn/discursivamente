<?php

namespace Repositories;

use PDO;
use App\Models\Classroom;

class ClassroomRepository
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function create(Classroom $classroom): int
    {
        $stmt = $this->pdo->prepare('INSERT INTO classrooms (professor_id, nome, descricao, status, privacidade, token_acesso, capacity, regras_aprovacao, calendario, biblioteca_materiais, estatisticas, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())');
        $stmt->execute([
            $classroom->professorId,
            $classroom->nome,
            $classroom->descricao,
            $classroom->status,
            $classroom->privacidade,
            $classroom->tokenAcesso,
            $classroom->capacity,
            $classroom->regrasAprovacao,
            $classroom->calendario,
            $classroom->bibliotecaMateriais,
            $classroom->estatisticas
        ]);
        return (int)$this->pdo->lastInsertId();
    }

    public function update(Classroom $classroom): void
    {
        $stmt = $this->pdo->prepare('UPDATE classrooms SET nome=?, descricao=?, status=?, privacidade=?, token_acesso=?, capacity=?, regras_aprovacao=?, calendario=?, biblioteca_materiais=?, estatisticas=?, updated_at=NOW() WHERE id=?');
        $stmt->execute([
            $classroom->nome,
            $classroom->descricao,
            $classroom->status,
            $classroom->privacidade,
            $classroom->tokenAcesso,
            $classroom->capacity,
            $classroom->regrasAprovacao,
            $classroom->calendario,
            $classroom->bibliotecaMateriais,
            $classroom->estatisticas,
            $classroom->id
        ]);
    }

    public function delete(int $id): void
    {
        $stmt = $this->pdo->prepare('DELETE FROM classrooms WHERE id = ?');
        $stmt->execute([$id]);
    }

    public function findById(int $id): ?Classroom
    {
        $stmt = $this->pdo->prepare('SELECT * FROM classrooms WHERE id = ?');
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        if (!$row) return null;
        return $this->hydrate($row);
    }

    public function findByProfessor(int $professorId): array
    {
        $stmt = $this->pdo->prepare('SELECT * FROM classrooms WHERE professor_id = ?');
        $stmt->execute([$professorId]);
        $result = [];
        while ($row = $stmt->fetch()) {
            $result[] = $this->hydrate($row);
        }
        return $result;
    }

    public function findOpenOrInvitedForStudent(int $studentId): array
    {
        // Exemplo: turmas abertas ou que o aluno foi convidado
        $stmt = $this->pdo->prepare('SELECT * FROM classrooms WHERE privacidade = "aberta" OR id IN (SELECT classroom_id FROM enrollments WHERE user_id = ?)');
        $stmt->execute([$studentId]);
        $result = [];
        while ($row = $stmt->fetch()) {
            $result[] = $this->hydrate($row);
        }
        return $result;
    }

    private function hydrate(array $row): Classroom
    {
        return new Classroom(
            $row['id'],
            $row['professor_id'],
            $row['nome'],
            $row['descricao'],
            $row['status'],
            $row['privacidade'],
            $row['token_acesso'],
            $row['capacity'],
            $row['regras_aprovacao'],
            $row['calendario'],
            $row['biblioteca_materiais'],
            $row['estatisticas'],
            $row['created_at'],
            $row['updated_at']
        );
    }
}