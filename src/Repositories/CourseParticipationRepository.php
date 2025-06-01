<?php

namespace Repositories;

use App\Models\CourseParticipation;
use PDO;
use PDOException;

class CourseParticipationRepository
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Registra a participação de um usuário em um curso.
     * Se já existir um registro com left_at não nulo, ele será "reaberto" (left_at = NULL).
     */
    public function joinCourse(int $userId, int $courseId): void
    {
        $sql = "
            INSERT INTO course_participations (user_id, course_id, joined_at, left_at)
            VALUES (:user_id, :course_id, NOW(), NULL)
            ON DUPLICATE KEY UPDATE
                joined_at = VALUES(joined_at),
                left_at    = NULL
        ";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':user_id'   => $userId,
            ':course_id' => $courseId,
        ]);
    }

    /**
     * Marca que o usuário saiu do curso, definindo left_at.
     */
    public function leaveCourse(int $userId, int $courseId): void
    {
        $sql = "
            UPDATE course_participations
               SET left_at = NOW()
             WHERE user_id   = :user_id
               AND course_id = :course_id
               AND left_at IS NULL
        ";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':user_id'   => $userId,
            ':course_id' => $courseId,
        ]);
    }

    /**
     * Verifica se o usuário está atualmente participando do curso
     */
    public function isParticipating(int $userId, int $courseId): bool
    {
        $sql = "
            SELECT COUNT(*) 
              FROM course_participations
             WHERE user_id   = :user_id
               AND course_id = :course_id
               AND left_at IS NULL
        ";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':user_id'   => $userId,
            ':course_id' => $courseId,
        ]);
        return (bool) $stmt->fetchColumn();
    }

    /**
     * Retorna todas as participações (ativas ou não) de um usuário.
     *
     * @return CourseParticipation[]
     */
    public function findByUser(int $userId): array
    {
        $stmt = $this->pdo->prepare('
            SELECT * 
              FROM course_participations
             WHERE user_id = :user_id
          ORDER BY joined_at DESC
        ');
        $stmt->execute([':user_id' => $userId]);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return array_map(fn(array $r) => $this->hydrate($r), $rows);
    }

    /**
     * Retorna todas as participações (ativas ou não) de um curso.
     *
     * @return CourseParticipation[]
     */
    public function findByCourse(int $courseId): array
    {
        $stmt = $this->pdo->prepare('
            SELECT * 
              FROM course_participations
             WHERE course_id = :course_id
          ORDER BY joined_at DESC
        ');
        $stmt->execute([':course_id' => $courseId]);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return array_map(fn(array $r) => $this->hydrate($r), $rows);
    }

    
    /**
     * Retorna quantos usuários estão participando (left_at IS NULL).
     */
    public function countActiveByCourse(int $courseId): int
    {
        $sql = "
            SELECT COUNT(*) 
              FROM course_participations
             WHERE course_id = :course_id
               AND left_at IS NULL
        ";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':course_id' => $courseId]);
        return (int) $stmt->fetchColumn();
    }    /**
     * Mapeia a linha do banco para o model CourseParticipation.
     */
    private function hydrate(array $row): CourseParticipation
    {
        return new CourseParticipation(
            (int)    $row['user_id'],
            (int)    $row['course_id'],
            $row['joined_at'],
            $row['left_at'],
            (int)    $row['id']
        );
    }
      /**
     * Retorna os IDs dos materiais completados por um usuário em um curso específico
     * 
     * @return array IDs dos materiais completados
     */
    public function getCompletedIdsByUser(int $userId, int $courseId): array
    {
        $sql = "SELECT up.material_id
                FROM user_progress up
                INNER JOIN material_entries me ON me.id = up.material_id
                INNER JOIN modules m ON m.id = me.material_id
                WHERE up.user_id = :userId AND m.course_id = :courseId";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':userId' => $userId,
            ':courseId' => $courseId
        ]);
        return array_column($stmt->fetchAll(PDO::FETCH_ASSOC), 'material_id');
    }

    /**
     * Retorna todos os cursos que o usuário está participando ativamente.
     *
     * @param int $userId
     * @return array
     */
    public function findCoursesByUser(int $userId): array
    {
        $stmt = $this->pdo->prepare('
            SELECT c.*
              FROM courses c
              JOIN course_participations cp ON c.id = cp.course_id
             WHERE cp.user_id = :user_id
               AND cp.left_at IS NULL
          ORDER BY cp.joined_at DESC
        ');
        $stmt->execute([':user_id' => $userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
