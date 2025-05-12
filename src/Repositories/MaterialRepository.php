<?php
// src/Repositories/MaterialRepository.php

namespace Repositories;

use App\Models\Material;
use PDO;

class MaterialRepository
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Retorna todos os materiais de um curso
     *
     * @param int $courseId
     * @return Material[]
     */
    public function findByCourseId(int $courseId): array
    {
        $stmt = $this->pdo->prepare(
            'SELECT * 
               FROM materials 
              WHERE course_id = :courseId 
           ORDER BY created_at'
        );
        $stmt->execute([':courseId' => $courseId]);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return array_map(fn(array $row) => $this->hydrate($row), $rows);
    }

    /**
     * Retorna um material pelo seu ID
     *
     * @param int $id
     * @return Material|null
     */
    public function findById(int $id): ?Material
    {
        $stmt = $this->pdo->prepare(
            'SELECT * 
               FROM materials 
              WHERE id = :id 
              LIMIT 1'
        );
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row ? $this->hydrate($row) : null;
    }

    /**
     * Insere ou atualiza um material
     */
    public function save(Material $material): void
    {
        if ($material->getId()) {
            $stmt = $this->pdo->prepare(
                'UPDATE materials
                    SET title        = :title,
                        content_url  = :content,
                        content_type = :contentType,
                        updated_at   = NOW()
                  WHERE id = :id'
            );
            $stmt->execute([
                ':title'       => $material->getTitle(),
                ':content'     => $material->getContent(),
                ':contentType' => $material->getContentType(),
                ':id'          => $material->getId(),
            ]);
        } else {
            $stmt = $this->pdo->prepare(
                'INSERT INTO materials
                    (course_id, title, content_url, content_type, created_at, updated_at)
                 VALUES
                    (:courseId, :title, :content, :contentType, NOW(), NOW())'
            );
            $stmt->execute([
                ':courseId'    => $material->getCourseId(),
                ':title'       => $material->getTitle(),
                ':content'     => $material->getContent(),
                ':contentType' => $material->getContentType(),
            ]);
        }
    }

    /**
     * Deleta um material pelo ID
     */
    public function delete(int $id): void
    {
        $stmt = $this->pdo->prepare('DELETE FROM materials WHERE id = :id');
        $stmt->execute([':id' => $id]);
    }

    /**
     * Mapeia um array de banco para a entidade Material
     */
    private function hydrate(array $row): Material
    {
        return new Material(
            (int)$row['course_id'],
            $row['title'],
            $row['content_url'],
            $row['content_type'],
            (int)$row['id'],
            $row['created_at'],
            $row['updated_at']
        );
    }
}
