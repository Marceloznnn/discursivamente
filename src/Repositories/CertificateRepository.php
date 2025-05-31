<?php
namespace Repositories;

use App\Models\Certificate;
use PDO;

class CertificateRepository
{ 
    private PDO $pdo;
    
    public function __construct(PDO $pdo) 
    { 
        $this->pdo = $pdo; 
    }

    public function findByUserAndCourse(int $userId, int $courseId): ?Certificate 
    {
        $stmt = $this->pdo->prepare('
            SELECT * FROM certificates 
            WHERE user_id = :userId AND course_id = :courseId
        ');
        
        $stmt->execute([
            ':userId' => $userId,
            ':courseId' => $courseId
        ]);
        
        $data = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$data) {
            return null;
        }
        
        return new Certificate(
            $data['user_id'],
            $data['course_id'],
            $data['issued_at'],
            $data['certificate_url'],
            $data['certificate_number'],
            $data['id']
        );
    }
    
    public function findByUser(int $userId): array 
    {
        $stmt = $this->pdo->prepare('
            SELECT * FROM certificates 
            WHERE user_id = :userId
            ORDER BY issued_at DESC
        ');
        
        $stmt->execute([':userId' => $userId]);
        $certificates = [];
        
        while ($data = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $certificates[] = new Certificate(
                $data['user_id'],
                $data['course_id'],
                $data['issued_at'],
                $data['certificate_url'],
                $data['certificate_number'],
                $data['id']
            );
        }
        
        return $certificates;
    }
    
    public function save(Certificate $certificate): void 
    {
        if ($certificate->getId() === null) {
            $this->insert($certificate);
        } else {
            $this->update($certificate);
        }
    }
    
    private function insert(Certificate $certificate): void 
    {
        $stmt = $this->pdo->prepare('
            INSERT INTO certificates (user_id, course_id, issued_at, certificate_url, certificate_number)
            VALUES (:userId, :courseId, :issuedAt, :certificateUrl, :certificateNumber)
        ');
        
        $stmt->execute([
            ':userId' => $certificate->getUserId(),
            ':courseId' => $certificate->getCourseId(),
            ':issuedAt' => $certificate->getIssuedAt(),
            ':certificateUrl' => $certificate->getCertificateUrl(),
            ':certificateNumber' => $certificate->getCertificateNumber()
        ]);
        
        $certificate->setId($this->pdo->lastInsertId());
    }
    
    private function update(Certificate $certificate): void 
    {
        $stmt = $this->pdo->prepare('
            UPDATE certificates 
            SET certificate_url = :certificateUrl
            WHERE id = :id
        ');
        
        $stmt->execute([
            ':certificateUrl' => $certificate->getCertificateUrl(),
            ':id' => $certificate->getId()
        ]);
    }
    
    public function findByCourseId(int $courseId): array 
    {
        $stmt = $this->pdo->prepare('
            SELECT * FROM certificates 
            WHERE course_id = :courseId
            ORDER BY issued_at DESC
        ');
        
        $stmt->execute([':courseId' => $courseId]);
        $certificates = [];
        
        while ($data = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $certificates[] = new Certificate(
                $data['user_id'],
                $data['course_id'],
                $data['issued_at'],
                $data['certificate_url'],
                $data['certificate_number'],
                $data['id']
            );
        }
        
        return $certificates;
    }
}