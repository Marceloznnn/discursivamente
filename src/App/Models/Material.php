<?php

namespace App\Models;

use DateTime;

class Material
{
    private ?int $id;
    private int $courseId;
    private string $title;
    private string $content;
    private string $contentType;
    private ?int $moduleId;
    private DateTime $createdAt;
    private ?DateTime $updatedAt;

    /**
     * Construtor.
     *
     * @param int         $courseId
     * @param string      $title
     * @param string      $content
     * @param string      $contentType
     * @param int|null    $moduleId
     * @param int|null    $id
     * @param DateTime|null $createdAt
     * @param DateTime|null $updatedAt
     */
    public function __construct(
        int $courseId,
        string $title,
        string $content,
        string $contentType,
        ?int $moduleId = null,
        ?int $id = null,
        ?DateTime $createdAt = null,
        ?DateTime $updatedAt = null
    ) {
        $this->id           = $id;
        $this->courseId     = $courseId;
        $this->title        = $title;
        $this->content      = $content;
        $this->contentType  = $contentType;
        $this->moduleId     = $moduleId;
        $this->createdAt    = $createdAt ?? new DateTime();
        $this->updatedAt    = $updatedAt;
    }

    // ─── Getters ────────────────────────────────────────────────────────────────

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCourseId(): int
    {
        return $this->courseId;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getContent(): string
    {
        return $this->content;
    }

    public function getContentType(): string
    {
        return $this->contentType;
    }

    public function getModuleId(): ?int
    {
        return $this->moduleId;
    }

    public function getCreatedAt(): DateTime
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): ?DateTime
    {
        return $this->updatedAt;
    }

    // ─── Setters ────────────────────────────────────────────────────────────────

    public function setCourseId(int $courseId): void
    {
        $this->courseId = $courseId;
    }

    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    public function setContent(string $content): void
    {
        $this->content = $content;
    }

    public function setContentType(string $contentType): void
    {
        $this->contentType = $contentType;
    }

    public function setModuleId(?int $moduleId): void
    {
        $this->moduleId = $moduleId;
    }

    public function setUpdatedAt(DateTime $updatedAt): void
    {
        $this->updatedAt = $updatedAt;
    }

    // ─── Conversão para array ───────────────────────────────────────────────────

    /**
     * Converte o objeto em array (útil para persistência ou json).
     *
     * @return array
     */
    public function toArray(): array
    {
        return [
            'id'           => $this->id,
            'course_id'    => $this->courseId,
            'title'        => $this->title,
            'content'      => $this->content,
            'content_type' => $this->contentType,
            'module_id'    => $this->moduleId,
            'created_at'   => $this->createdAt->format('Y-m-d H:i:s'),
            'updated_at'   => $this->updatedAt?->format('Y-m-d H:i:s'),
        ];
    }
}
