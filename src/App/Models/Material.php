<?php

namespace App\Models;

class Material
{
    private int $courseId;
    private string $title;
    private string $content; // URL
    private string $contentType;
    private ?int $id;
    private ?string $createdAt;
    private ?string $updatedAt;

    public function __construct(
        int $courseId,
        string $title,
        string $content,         // <- aqui representa 'content_url'
        string $contentType,
        ?int $id = null,
        ?string $createdAt = null,
        ?string $updatedAt = null
    ) {
        $this->courseId = $courseId;
        $this->title = $title;
        $this->content = $content;
        $this->contentType = $contentType;
        $this->id = $id;
        $this->createdAt = $createdAt;
        $this->updatedAt = $updatedAt;
    }

    // Getters
    public function getId(): ?int { return $this->id; }
    public function getCourseId(): int { return $this->courseId; }
    public function getTitle(): string { return $this->title; }
    public function getContent(): string { return $this->content; }
    public function getContentType(): string { return $this->contentType; }
    public function getCreatedAt(): ?string { return $this->createdAt; }
    public function getUpdatedAt(): ?string { return $this->updatedAt; }

    // Setters
    public function setCourseId(int $courseId): void { $this->courseId = $courseId; }
    public function setTitle(string $title): void { $this->title = $title; }
    public function setContent(string $content): void { $this->content = $content; }
    public function setContentType(string $contentType): void { $this->contentType = $contentType; }
    public function setUpdatedAt(string $updatedAt): void { $this->updatedAt = $updatedAt; }

    public function toArray(): array
    {
        return [
            'id'           => $this->id,
            'course_id'    => $this->courseId,
            'title'        => $this->title,
            'content'      => $this->content,
            'content_type' => $this->contentType,
            'created_at'   => $this->createdAt,
            'updated_at'   => $this->updatedAt,
        ];
    }
}
