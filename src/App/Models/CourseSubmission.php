<?php

namespace App\Models;

use PDO;

class CourseSubmission
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function create(array $data): bool
    {
        $sql = "INSERT INTO course_submissions 
                (nome, email, titulo, descricao, arquivo_nome) 
                VALUES (:nome, :email, :titulo, :descricao, :arquivo_nome)";

        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            ':nome'         => $data['nome'],
            ':email'        => $data['email'],
            ':titulo'       => $data['titulo'],
            ':descricao'    => $data['descricao'] ?? null,
            ':arquivo_nome' => $data['arquivo_nome'] ?? null,
        ]);
    }

    public function all(): array
    {
        $stmt = $this->pdo->query("SELECT * FROM course_submissions ORDER BY enviado_em DESC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function find(int $id): ?array
    {
        $stmt = $this->pdo->prepare("SELECT * FROM course_submissions WHERE id = :id");
        $stmt->execute([':id' => $id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ?: null;
    }

    public function updateStatus(int $id, string $status): bool
    {
        $sql = "UPDATE course_submissions SET status = :status WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            ':status' => $status,
            ':id'     => $id,
        ]);
    }

    public function delete(int $id): bool
    {
        $stmt = $this->pdo->prepare("DELETE FROM course_submissions WHERE id = :id");
        return $stmt->execute([':id' => $id]);
    }
}
