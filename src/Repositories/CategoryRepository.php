<?php
// src/Repositories/CategoryRepository.php

namespace Repositories;

use App\Models\Category;
use PDO;

class CategoryRepository
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Retorna todas as categorias
     *
     * @return Category[]
     */
    public function findAll(): array
    {
        $stmt = $this->pdo->query(
            'SELECT id, name, created_at, updated_at
               FROM categories
           ORDER BY name ASC'
        );
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return array_map(fn(array $r) => $this->hydrate($r), $rows);
    }

    /**
     * Busca uma categoria pelo ID
     */
    public function findById(int $id): ?Category
    {
        $stmt = $this->pdo->prepare(
            'SELECT id, name, created_at, updated_at
               FROM categories
              WHERE id = :id
              LIMIT 1'
        );
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row ? $this->hydrate($row) : null;
    }

    /**
     * Converte array de banco em Category
     */
    private function hydrate(array $row): Category
    {
        return new Category(
            $row['name'],
            (int) $row['id'],
            $row['created_at'],
            $row['updated_at']
        );
    }
}
