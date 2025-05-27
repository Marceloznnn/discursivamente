<?php

namespace Repositories;

use App\Models\Course;
use PDO;

class CourseRepository
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function findById(int $id): ?Course
    {
        $stmt = $this->pdo->prepare('SELECT * FROM courses WHERE id = :id LIMIT 1');
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ? $this->hydrate($row) : null;
    }

    public function findByCreatorId(int $creatorId): array
    {
        $stmt = $this->pdo->prepare(
            'SELECT * FROM courses WHERE creator_id = :id ORDER BY created_at DESC'
        );
        $stmt->execute([':id' => $creatorId]);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return array_map(fn(array $r) => $this->hydrate($r), $rows);
    }

    /**
     * Conta todos os cursos de um criador específico
     *
     * @param int $creatorId
     * @return int
     */
    public function countByCreatorId(int $creatorId): int
    {
        $stmt = $this->pdo->prepare('SELECT COUNT(*) FROM courses WHERE creator_id = :creator_id');
        $stmt->execute([':creator_id' => $creatorId]);
        return (int) $stmt->fetchColumn();
    }

    /**
     * Retorna cursos paginados de um criador específico
     *
     * @param int $creatorId
     * @param int $limit
     * @param int $offset
     * @return Course[]
     */
    public function findByCreatorIdPaginated(int $creatorId, int $limit, int $offset): array
    {
        $stmt = $this->pdo->prepare(
            'SELECT * FROM courses 
             WHERE creator_id = :creator_id 
             ORDER BY created_at DESC 
             LIMIT :limit OFFSET :offset'
        );
        $stmt->bindValue(':creator_id', $creatorId, PDO::PARAM_INT);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return array_map(fn(array $r) => $this->hydrate($r), $rows);
    }

    public function findAll(): array
    {
        $stmt = $this->pdo->query('SELECT * FROM courses ORDER BY created_at DESC');
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return array_map(fn(array $row) => $this->hydrate($row), $rows);
    }

    // ---------- Paginação (backend) ----------

    /**
     * Retorna cursos paginados (todas categorias)
     *
     * @param int $limit
     * @param int $offset
     * @return Course[]
     */
    public function findPaginated(int $limit, int $offset): array
    {
        $stmt = $this->pdo->prepare(
            'SELECT * FROM courses ORDER BY created_at DESC LIMIT :limit OFFSET :offset'
        );
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return array_map(fn(array $r) => $this->hydrate($r), $rows);
    }

    /**
     * Conta todos os cursos (todas categorias)
     *
     * @return int
     */
    public function countAll(): int
    {
        return (int) $this->pdo->query('SELECT COUNT(*) FROM courses')->fetchColumn();
    }

    // ---------- Filtros com paginação ----------

    /**
     * Retorna cursos de uma categoria, paginados
     *
     * @param int $categoryId
     * @param int $limit
     * @param int $offset
     * @return Course[]
     */
    public function findPaginatedByCategory(int $categoryId, int $limit, int $offset): array
    {
        $stmt = $this->pdo->prepare(
            'SELECT *
               FROM courses
              WHERE category_id = :category_id
           ORDER BY created_at DESC
              LIMIT :limit OFFSET :offset'
        );
        $stmt->bindValue(':category_id', $categoryId, PDO::PARAM_INT);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return array_map(fn(array $r) => $this->hydrate($r), $rows);
    }

    /**
     * Conta cursos de uma categoria
     *
     * @param int $categoryId
     * @return int
     */
    public function countByCategory(int $categoryId): int
    {
        $stmt = $this->pdo->prepare(
            'SELECT COUNT(*) FROM courses WHERE category_id = :category_id'
        );
        $stmt->execute([':category_id' => $categoryId]);
        return (int) $stmt->fetchColumn();
    }

    /**
     * Busca cursos por título/descrição, paginados
     *
     * @param string $q
     * @param int    $limit
     * @param int    $offset
     * @return Course[]
     */
    public function searchPaginated(string $q, int $limit, int $offset): array
    {
        $stmt = $this->pdo->prepare(
            'SELECT *
               FROM courses
              WHERE title       LIKE :q
                 OR description LIKE :q
           ORDER BY created_at DESC
              LIMIT :limit OFFSET :offset'
        );
        $like = "%{$q}%";
        $stmt->bindValue(':q', $like, PDO::PARAM_STR);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return array_map(fn(array $r) => $this->hydrate($r), $rows);
    }

    /**
     * Conta resultados de busca
     *
     * @param string $q
     * @return int
     */
    public function countBySearch(string $q): int
    {
        $stmt = $this->pdo->prepare(
            'SELECT COUNT(*)
               FROM courses
              WHERE title       LIKE :q
                 OR description LIKE :q'
        );
        $like = "%{$q}%";
        $stmt->execute([':q' => $like]);
        return (int) $stmt->fetchColumn();
    }

    // ---------- Métodos existentes (sem paginação) ----------

    /**
     * Retorna todos os cursos de uma determinada categoria (sem paginação)
     *
     * @param int $categoryId
     * @return Course[]
     */
    public function findByCategoryId(int $categoryId): array
    {
        $stmt = $this->pdo->prepare(
            'SELECT *
               FROM courses
              WHERE category_id = :category_id
           ORDER BY created_at DESC'
        );
        $stmt->execute([':category_id' => $categoryId]);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return array_map(fn(array $r) => $this->hydrate($r), $rows);
    }

    public function search(string $q): array
    {
        $stmt = $this->pdo->prepare(
            'SELECT *
               FROM courses
              WHERE title       LIKE :q
                 OR description LIKE :q
           ORDER BY created_at DESC'
        );
        $like = "%{$q}%";
        $stmt->execute([':q' => $like]);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return array_map(fn(array $r) => $this->hydrate($r), $rows);
    }

    public function save(Course $course): void
    {
        if ($course->getId()) {
            $stmt = $this->pdo->prepare(
                'UPDATE courses
                   SET title       = :title,
                       description = :description,
                       creator_id  = :creator_id,
                       price       = :price,
                       category_id = :category_id,
                       updated_at  = NOW()
                 WHERE id = :id'
            );
            $stmt->execute([
                ':title'       => $course->getTitle(),
                ':description' => $course->getDescription(),
                ':creator_id'  => $course->getCreatorId(),
                ':price'       => $course->getPrice(),
                ':category_id' => $course->getCategoryId(),
                ':id'          => $course->getId(),
            ]);
        } else {
            $stmt = $this->pdo->prepare(
                'INSERT INTO courses
                 (title, description, creator_id, price, category_id, created_at, updated_at)
                 VALUES (:title, :description, :creator_id, :price, :category_id, NOW(), NOW())'
            );
            $stmt->execute([
                ':title'       => $course->getTitle(),
                ':description' => $course->getDescription(),
                ':creator_id'  => $course->getCreatorId(),
                ':price'       => $course->getPrice(),
                ':category_id' => $course->getCategoryId(),
            ]);
        }
    }

    public function delete(int $id): void
    {
        $stmt = $this->pdo->prepare('DELETE FROM courses WHERE id = :id');
        $stmt->execute([':id' => $id]);
    }

    /**
     * Mapeia array de dados para instância de Course
     *
     * @param array $row
     * @return Course
     */
    private function hydrate(array $row): Course
    {
        return new Course(
            $row['title'],
            $row['description'],
            (int) $row['creator_id'],             
            isset($row['category_id']) ? (int) $row['category_id'] : null,
            isset($row['id']) ? (int) $row['id'] : null,
            (float) $row['price'],
            $row['created_at'],
            $row['updated_at']
        );
    }
}