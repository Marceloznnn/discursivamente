<?php

namespace Models;

class Submission
{
    private int $id;
    private int $assignment_id;
    private int $student_id;
    private string $resposta;
    private string $arquivo_path;
    private ?float $nota;
    private ?string $feedback;
    private string $status;
    private string $submitted_at;
    private ?string $graded_at;
    private string $created_at;
    private string $updated_at;

    // Getters e setters
    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function getAssignmentId(): int
    {
        return $this->assignment_id;
    }

    public function setAssignmentId(int $assignment_id): void
    {
        $this->assignment_id = $assignment_id;
    }

    public function getStudentId(): int
    {
        return $this->student_id;
    }

    public function setStudentId(int $student_id): void
    {
        $this->student_id = $student_id;
    }

    public function getContent(): string
    {
        return $this->resposta;
    }

    public function setContent(string $resposta): void
    {
        $this->resposta = $resposta;
    }

    public function getArquivoPath(): string
    {
        return $this->arquivo_path;
    }

    public function setArquivoPath(string $arquivo_path): void
    {
        $this->arquivo_path = $arquivo_path;
    }

    public function getNota(): ?float
    {
        return $this->nota;
    }

    public function setNota(?float $nota): void
    {
        $this->nota = $nota;
    }

    public function getFeedback(): ?string
    {
        return $this->feedback;
    }

    public function setFeedback(?string $feedback): void
    {
        $this->feedback = $feedback;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function setStatus(string $status): void
    {
        $this->status = $status;
    }

    public function getSubmittedAt(): string
    {
        return $this->submitted_at;
    }

    public function setSubmittedAt(string $submitted_at): void
    {
        $this->submitted_at = $submitted_at;
    }

    public function getGradedAt(): ?string
    {
        return $this->graded_at;
    }

    public function setGradedAt(?string $graded_at): void
    {
        $this->graded_at = $graded_at;
    }

    public function getCreatedAt(): string
    {
        return $this->created_at;
    }

    public function setCreatedAt(string $created_at): void
    {
        $this->created_at = $created_at;
    }

    public function getUpdatedAt(): string
    {
        return $this->updated_at;
    }

    public function setUpdatedAt(string $updated_at): void
    {
        $this->updated_at = $updated_at;
    }
}
