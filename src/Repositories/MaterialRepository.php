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
     * Retorna todos os materiais de um módulo específico de um curso
     *
     * @param int $courseId
     * @param int $moduleId
     * @return Material[]
     */
    public function findByModuleId(int $courseId, int $moduleId): array
    {
        $stmt = $this->pdo->prepare(
            'SELECT * 
               FROM materials 
              WHERE course_id = :courseId 
                AND module_id = :moduleId
           ORDER BY created_at'
        );
        $stmt->execute([
            ':courseId' => $courseId,
            ':moduleId' => $moduleId,
        ]);
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
            // UPDATE existente
            $stmt = $this->pdo->prepare(
                'UPDATE materials
                    SET title         = :title,
                        content_url   = :content,
                        content_type  = :contentType,
                        module_id     = :moduleId,
                        updated_at    = NOW()
                  WHERE id = :id'
            );
            $stmt->execute([
                ':title'       => $material->getTitle(),
                ':content'     => $material->getContent(),
                ':contentType' => $material->getContentType(),
                ':moduleId'    => $material->getModuleId(),
                ':id'          => $material->getId(),
            ]);
        } else {
            // INSERT novo
            $stmt = $this->pdo->prepare(
                'INSERT INTO materials
                    (course_id, title, content_url, content_type, module_id, created_at, updated_at)
                 VALUES
                    (:courseId, :title, :content, :contentType, :moduleId, NOW(), NOW())'
            );
            $stmt->execute([
                ':courseId'    => $material->getCourseId(),
                ':title'       => $material->getTitle(),
                ':content'     => $material->getContent(),
                ':contentType' => $material->getContentType(),
                ':moduleId'    => $material->getModuleId(),
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
            (int) $row['course_id'],
            $row['title'],
            $row['content_url'],
            $row['content_type'],
            isset($row['module_id']) ? (int)$row['module_id'] : null,
            (int) $row['id'],
            new \DateTime($row['created_at']),
            isset($row['updated_at']) ? new \DateTime($row['updated_at']) : null
        );
    }
}
