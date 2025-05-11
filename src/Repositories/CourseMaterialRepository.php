<?php
// src/Repositories/CourseMaterialRepository.php
namespace Repositories;

use App\Models\CourseMaterial;
use PDO;
use DateTime;

class CourseMaterialRepository
{
    private PDO $pdo;
    private string $table = 'course_materials';

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function findById(int $id): ?CourseMaterial
    {
        $stmt = $this->pdo->prepare("SELECT * FROM {$this->table} WHERE id = ?");
        $stmt->execute([$id]);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$data) {
            return null;
        }
        return $this->mapRowToModel($data);
    }

    /** @return CourseMaterial[] */
    public function findByCourseId(int $courseId): array
    {
        $stmt = $this->pdo->prepare("SELECT * FROM {$this->table} WHERE course_id = ? ORDER BY created_at");
        $stmt->execute([$courseId]);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return array_map(fn($row) => $this->mapRowToModel($row), $rows);
    }

    public function save(CourseMaterial $m): void
    {
        if ($m->getId() === null) {
            $stmt = $this->pdo->prepare("
                INSERT INTO {$this->table}
                (course_id, title, description, media_type, media_url, cloudinary_public_id)
                VALUES (?, ?, ?, ?, ?, ?)
            ");
            $stmt->execute([
                $m->getCourseId(),
                $m->getTitle(),
                $m->getDescription(),
                $m->getMediaType(),
                $m->getMediaUrl(),
                $m->getCloudinaryPublicId()
            ]);
            $m->setId((int)$this->pdo->lastInsertId());
        } else {
            $stmt = $this->pdo->prepare("
                UPDATE {$this->table} SET
                  course_id               = ?,
                  title                   = ?,
                  description             = ?,
                  media_type              = ?,
                  media_url               = ?,
                  cloudinary_public_id    = ?,
                  updated_at              = NOW()
                WHERE id = ?
            ");
            $stmt->execute([
                $m->getCourseId(),
                $m->getTitle(),
                $m->getDescription(),
                $m->getMediaType(),
                $m->getMediaUrl(),
                $m->getCloudinaryPublicId(),
                $m->getId()
            ]);
        }
    }

    public function delete(int $id): void
    {
        $stmt = $this->pdo->prepare("DELETE FROM {$this->table} WHERE id = ?");
        $stmt->execute([$id]);
    }

    private function mapRowToModel(array $row): CourseMaterial
    {
        return new CourseMaterial(
            (int)$row['course_id'],
            $row['title'],
            $row['media_type'],
            $row['media_url'],
            $row['description'],
            $row['cloudinary_public_id'],
            (int)$row['id'],
            new DateTime($row['created_at']),
            new DateTime($row['updated_at'])
        );
    }
}
