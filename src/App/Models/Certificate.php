<?php
namespace App\Models;

class Certificate
{
    private ?int $id;
    private int $userId;
    private int $courseId;
    private string $issuedAt;
    private string $certificateUrl;
    private string $certificateNumber;

    public function __construct(
        int $userId, 
        int $courseId, 
        string $issuedAt, 
        string $certificateUrl,
        string $certificateNumber,
        ?int $id = null
    ) {
        $this->id = $id;
        $this->userId = $userId;
        $this->courseId = $courseId;
        $this->issuedAt = $issuedAt;
        $this->certificateUrl = $certificateUrl;
        $this->certificateNumber = $certificateNumber;
    }

    // Getters
    public function getId(): ?int { return $this->id; }
    public function getUserId(): int { return $this->userId; }
    public function getCourseId(): int { return $this->courseId; }
    public function getIssuedAt(): string { return $this->issuedAt; }
    public function getCertificateUrl(): string { return $this->certificateUrl; }
    public function getCertificateNumber(): string { return $this->certificateNumber; }
    
    // Setters
    public function setId(?int $id): void { $this->id = $id; }
    public function setCertificateUrl(string $url): void { $this->certificateUrl = $url; }
}