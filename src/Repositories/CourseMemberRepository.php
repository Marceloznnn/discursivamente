<?php
// src/Repositories/CourseMemberRepository.php
namespace Repositories;

use App\Models\CourseMember;
use PDO;
use DateTime;

class CourseMemberRepository
{
    private PDO $pdo;
    
    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Retorna todos os membros de um curso.
     *
     * @param int $courseId
     * @return CourseMember[]
     */
    public function findByCourse(int $courseId): array
    {
        $stmt = $this->pdo->prepare("
            SELECT * 
              FROM course_member 
             WHERE course_id = :course_id
             ORDER BY enrolled_at ASC
        ");
        $stmt->execute([':course_id' => $courseId]);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return array_map(fn(array $row) => $this->hydrate($row), $rows);
    }

    /**
     * Retorna um membro pelo seu ID.
     *
     * @param int $id
     * @return CourseMember|null
     */
    public function findById(int $id): ?CourseMember
    {
        $stmt = $this->pdo->prepare("SELECT * FROM course_member WHERE id = :id");
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ? $this->hydrate($row) : null;
    }

    /**
     * Verifica se um usuário já está inscrito em um curso.
     *
     * @param int $courseId
     * @param int $userId
     * @return bool
     */
    public function exists(int $courseId, int $userId): bool
    {
        $stmt = $this->pdo->prepare("
            SELECT 1 
              FROM course_member 
             WHERE course_id = :course_id
               AND user_id   = :user_id
             LIMIT 1
        ");
        $stmt->execute([
            ':course_id' => $courseId,
            ':user_id'   => $userId,
        ]);
        return (bool) $stmt->fetchColumn();
    }

    /**
     * Insere ou atualiza um CourseMember.
     *
     * @param CourseMember $member
     * @return void
     */
    public function save(CourseMember $member): void
    {
        if ($member->getId() === null) {
            $sql = "
                INSERT INTO course_member
                  (course_id, user_id, role, status,
                   enrollment_key, payment_amount,
                   enrolled_at, updated_at,
                   progress, certificate_awarded)
                VALUES
                  (:course_id, :user_id, :role, :status,
                   :enrollment_key, :payment_amount,
                   :enrolled_at, :updated_at,
                   :progress, :certificate_awarded)
            ";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([
                ':course_id'           => $member->getCourseId(),
                ':user_id'             => $member->getUserId(),
                ':role'                => $member->getRole(),
                ':status'              => $member->getStatus(),
                ':enrollment_key'      => $member->getEnrollmentKey(),
                ':payment_amount'      => $member->getPaymentAmount(),
                ':enrolled_at'         => $member->getEnrolledAt()?->format('Y-m-d H:i:s'),
                ':updated_at'          => $member->getUpdatedAt()?->format('Y-m-d H:i:s'),
                ':progress'            => $member->getProgress(),
                ':certificate_awarded' => $member->isCertificateAwarded() ? 1 : 0,
            ]);
            $member->setId((int)$this->pdo->lastInsertId());
        } else {
            $sql = "
                UPDATE course_member SET
                  role                = :role,
                  status              = :status,
                  enrollment_key      = :enrollment_key,
                  payment_amount      = :payment_amount,
                  updated_at          = :updated_at,
                  progress            = :progress,
                  certificate_awarded = :certificate_awarded
                WHERE id = :id
            ";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([
                ':role'                => $member->getRole(),
                ':status'              => $member->getStatus(),
                ':enrollment_key'      => $member->getEnrollmentKey(),
                ':payment_amount'      => $member->getPaymentAmount(),
                ':updated_at'          => (new DateTime())->format('Y-m-d H:i:s'),
                ':progress'            => $member->getProgress(),
                ':certificate_awarded' => $member->isCertificateAwarded() ? 1 : 0,
                ':id'                  => $member->getId(),
            ]);
        }
    }

    /**
     * Remove um membro pelo seu ID.
     *
     * @param int $id
     * @return void
     */
    public function delete(int $id): void
    {
        $stmt = $this->pdo->prepare("DELETE FROM course_member WHERE id = :id");
        $stmt->execute([':id' => $id]);
    }

    /**
     * Converte uma linha do banco em um CourseMember.
     *
     * @param array $row
     * @return CourseMember
     */
    private function hydrate(array $row): CourseMember
    {
        return new CourseMember(
            courseId:           (int)$row['course_id'],
            userId:             (int)$row['user_id'],
            role:               $row['role'],
            status:             $row['status'],
            enrollmentKey:      $row['enrollment_key'],
            paymentAmount:      $row['payment_amount'] !== null ? (float)$row['payment_amount'] : null,
            id:                 (int)$row['id'],
            enrolledAt:         new DateTime($row['enrolled_at']),
            updatedAt:          new DateTime($row['updated_at']),
            progress:           (float)$row['progress'],
            certificateAwarded: (bool)$row['certificate_awarded']
        );
    }
    public function addMember(int $courseId, int $userId, string $role = 'member'): void
{
    $stmt = $this->pdo->prepare("
        INSERT INTO course_members (course_id, user_id, role)
        VALUES (?, ?, ?)
    ");
    $stmt->execute([$courseId, $userId, $role]);
}

        // Novo método
public function countByCourseId(int $courseId): int
{
    $stmt = $this->pdo->prepare('SELECT COUNT(*) FROM course_member WHERE course_id = :course_id');
    $stmt->execute(['course_id' => $courseId]);
    return (int) $stmt->fetchColumn();
}

    
}
