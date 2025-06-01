<?php
namespace App\Models;

class Module
{
    private ?int $id;
    private int $courseId;
    private string $title;
    private ?string $description;
    private int $position;
    private ?string $createdAt;
    private ?string $updatedAt;

    public function __construct(
        int $courseId, 
        string $title,
        ?string $description = null,
        int $position = 1,
        ?int $id = null,
        ?string $createdAt = null,
        ?string $updatedAt = null
    ) {
        $this->id = $id;
        $this->courseId = $courseId;
        $this->title = $title;
        $this->description = $description;
        $this->position = $position;
        $this->createdAt = $createdAt;
        $this->updatedAt = $updatedAt;
    }

    // Getters
    public function getId(): ?int { return $this->id; }
    public function getCourseId(): int { return $this->courseId; }
    public function getTitle(): string { return $this->title; }
    public function getDescription(): ?string { return $this->description; }
    public function getPosition(): int { return $this->position; }
    public function getCreatedAt(): ?string { return $this->createdAt; }
    public function getUpdatedAt(): ?string { return $this->updatedAt; }
    
    // Setters
    public function setId(?int $id): void { $this->id = $id; }
    public function setTitle(string $title): void { $this->title = $title; }
    public function setDescription(?string $description): void { $this->description = $description; }
    public function setPosition(int $position): void { $this->position = $position; }
    public function setUpdatedAt(?string $updatedAt): void { $this->updatedAt = $updatedAt; }
}