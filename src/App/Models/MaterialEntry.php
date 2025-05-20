<?php
// src/Models/MaterialEntry.php
namespace App\Models;

class MaterialEntry
{
    private ?int $id;
    private int $materialId;
    private string $title;
    private string $contentUrl;
    private string $contentType;
    private string $publicId;
    private \DateTime $createdAt;

    public function __construct(
        int $materialId,
        string $title,
        string $contentUrl,
        string $contentType,
        string $publicId
    ) {
        $this->id          = null;
        $this->materialId  = $materialId;
        $this->title       = $title;
        $this->contentUrl  = $contentUrl;
        $this->contentType = $contentType;
        $this->publicId    = $publicId;
        $this->createdAt   = new \DateTime();
    }

    // getters...
    public function getId(): ?int        { return $this->id; }
    public function getMaterialId(): int { return $this->materialId; }
    public function getTitle(): string   { return $this->title; }
    public function getContentUrl(): string { return $this->contentUrl; }
    public function getContentType(): string { return $this->contentType; }
    public function getPublicId(): string   { return $this->publicId; }
    public function getCreatedAt(): \DateTime { return $this->createdAt; }

    // setters...
    public function setId(int $id): void       { $this->id = $id; }
}
