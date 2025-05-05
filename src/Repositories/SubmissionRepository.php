<?php

namespace Repositories;

use Models\Submission;
use PDO;

class SubmissionRepository
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function findByAssignmentAndStudent(int $assignmentId, int $studentId): ?Submission
    {
        $stmt = $this->pdo->prepare('SELECT * FROM submissions WHERE assignment_id = :aid AND student_id = :sid');
        $stmt->execute([
            'aid' => $assignmentId,
            'sid' => $studentId
        ]);

        $data = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$data) {
            return null;
        }

        $submission = new Submission();
        $submission->setId($data['id']);
        $submission->setAssignmentId($data['assignment_id']);
        $submission->setStudentId($data['student_id']);
        $submission->setContent($data['resposta']);
        $submission->setArquivoPath($data['arquivo_path']);
        $submission->setNota($data['nota']);
        $submission->setFeedback($data['feedback']);
        $submission->setStatus($data['status']);
        $submission->setSubmittedAt($data['submitted_at']);
        $submission->setGradedAt($data['graded_at']);
        $submission->setUpdatedAt($data['updated_at']);

        return $submission;
    }

    public function findByStudent(int $studentId): array
    {
        $stmt = $this->pdo->prepare("SELECT * FROM submissions WHERE student_id = :student_id");
        $stmt->execute(['student_id' => $studentId]);

        $submissions = [];
        while ($data = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $submission = new Submission();
            $submission->setId($data['id']);
            $submission->setAssignmentId($data['assignment_id']);
            $submission->setStudentId($data['student_id']);
            $submission->setContent($data['resposta']);
            $submission->setArquivoPath($data['arquivo_path']);
            $submission->setNota($data['nota']);
            $submission->setFeedback($data['feedback']);
            $submission->setStatus($data['status']);
            $submission->setSubmittedAt($data['submitted_at']);
            $submission->setGradedAt($data['graded_at']);
            $submission->setUpdatedAt($data['updated_at']);
            $submissions[] = $submission;
        }

        return $submissions;
    }

    public function create(Submission $submission): bool
    {
        $stmt = $this->pdo->prepare('
            INSERT INTO submissions (assignment_id, student_id, resposta, arquivo_path, nota, feedback, status, submitted_at, graded_at, created_at, updated_at)
            VALUES (:assignment_id, :student_id, :resposta, :arquivo_path, :nota, :feedback, :status, :submitted_at, :graded_at, :created_at, :updated_at)
        ');

        return $stmt->execute([
            'assignment_id'  => $submission->getAssignmentId(),
            'student_id'     => $submission->getStudentId(),
            'resposta'       => $submission->getContent(),
            'arquivo_path'   => $submission->getArquivoPath(),
            'nota'           => $submission->getNota(),
            'feedback'       => $submission->getFeedback(),
            'status'         => $submission->getStatus(),
            'submitted_at'   => $submission->getSubmittedAt(),
            'graded_at'      => $submission->getGradedAt(),
            'created_at'     => $submission->getCreatedAt(),
            'updated_at'     => $submission->getUpdatedAt()
        ]);
    }

    public function update(Submission $submission): bool
    {
        $stmt = $this->pdo->prepare('
            UPDATE submissions 
            SET resposta = :resposta, arquivo_path = :arquivo_path, nota = :nota, feedback = :feedback, status = :status, graded_at = :graded_at, updated_at = :updated_at 
            WHERE id = :id
        ');

        return $stmt->execute([
            'id'             => $submission->getId(),
            'resposta'       => $submission->getContent(),
            'arquivo_path'   => $submission->getArquivoPath(),
            'nota'           => $submission->getNota(),
            'feedback'       => $submission->getFeedback(),
            'status'         => $submission->getStatus(),
            'graded_at'      => $submission->getGradedAt(),
            'updated_at'     => $submission->getUpdatedAt()
        ]);
    }
}
