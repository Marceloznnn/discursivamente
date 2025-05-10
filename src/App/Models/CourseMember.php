<?php
// App/Models/CourseMember.php
namespace App\Models;

class CourseMember
{
    private ?int $id;
    private int $courseId;
    private int $userId;
    private string $role;
    private string $status;
    private ?string $enrollmentKey;
    private ?float $paymentAmount;
    private ?\DateTime $enrolledAt;
    private ?\DateTime $updatedAt;
    private float $progress;
    private bool $certificateAwarded;

    public function __construct(
        int $courseId,
        int $userId,
        string $role = 'student',
        string $status = 'pending',
        ?string $enrollmentKey = null,
        ?float $paymentAmount = null,
        ?int $id = null,
        ?\DateTime $enrolledAt = null,
        ?\DateTime $updatedAt = null,
        float $progress = 0.0,
        bool $certificateAwarded = false
    ) {
        $this->id = $id;
        $this->courseId = $courseId;
        $this->userId = $userId;
        $this->role = $role;
        $this->status = $status;
        $this->enrollmentKey = $enrollmentKey;
        $this->paymentAmount = $paymentAmount;
        $this->enrolledAt = $enrolledAt;
        $this->updatedAt = $updatedAt;
        $this->progress = $progress;
        $this->certificateAwarded = $certificateAwarded;
    }

    // Getters and Setters
    public function getId(): ?int { return $this->id; }
    public function setId(int $id): void { $this->id = $id; }

    public function getCourseId(): int { return $this->courseId; }
    public function setCourseId(int $courseId): void { $this->courseId = $courseId; }

    public function getUserId(): int { return $this->userId; }
    public function setUserId(int $userId): void { $this->userId = $userId; }

    public function getRole(): string { return $this->role; }
    public function setRole(string $role): void { $this->role = $role; }

    public function getStatus(): string { return $this->status; }
    public function setStatus(string $status): void { $this->status = $status; }

    public function getEnrollmentKey(): ?string { return $this->enrollmentKey; }
    public function setEnrollmentKey(?string $key): void { $this->enrollmentKey = $key; }

    public function getPaymentAmount(): ?float { return $this->paymentAmount; }
    public function setPaymentAmount(?float $amount): void { $this->paymentAmount = $amount; }

    public function getEnrolledAt(): ?\DateTime { return $this->enrolledAt; }
    public function setEnrolledAt(\DateTime $dt): void { $this->enrolledAt = $dt; }

    public function getUpdatedAt(): ?\DateTime { return $this->updatedAt; }
    public function setUpdatedAt(\DateTime $dt): void { $this->updatedAt = $dt; }

    public function getProgress(): float { return $this->progress; }
    public function setProgress(float $progress): void { $this->progress = $progress; }

    public function isCertificateAwarded(): bool { return $this->certificateAwarded; }
    public function setCertificateAwarded(bool $awarded): void { $this->certificateAwarded = $awarded; }
}