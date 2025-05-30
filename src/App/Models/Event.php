<?php

namespace App\Models;

class Event
{
    private ?int $id;
    private string $title;
    private string $description;
    private string $dateTime;    // formato: Y-m-d H:i:s
    private string $visibility;   // public, restricted, etc.
    private ?string $image;
    private bool $isFeatured;
    private int $featurePriority;
    private ?string $createdAt;
    private ?string $updatedAt;
    private ?string $imagePublicId;

    public function __construct(
        string $title,
        string $description,
        string $dateTime,
        string $visibility = 'public',
        ?string $image = null,
        bool $isFeatured = false,
        int $featurePriority = 0,
        ?int $id = null,
        ?string $createdAt = null,
        ?string $updatedAt = null,
        ?string $imagePublicId = null
    ) {
        $this->id = $id;
        $this->title = $title;
        $this->description = $description;
        $this->dateTime = $dateTime;
        $this->visibility = $visibility;
        $this->image = $image;
        $this->isFeatured = $isFeatured;
        $this->featurePriority = $featurePriority;
        $this->createdAt = $createdAt;
        $this->updatedAt = $updatedAt;
        $this->imagePublicId = $imagePublicId;
    }

    // Getters ...
    public function getId(): ?int { return $this->id; }
    public function getTitle(): string { return $this->title; }
    public function getDescription(): string { return $this->description; }
    public function getDateTime(): string { return $this->dateTime; }
    public function getVisibility(): string { return $this->visibility; }
    public function getImage(): ?string { return $this->image; }
    public function getIsFeatured(): bool { return $this->isFeatured; }
    public function getFeaturePriority(): int { return $this->featurePriority; }
    public function getCreatedAt(): ?string { return $this->createdAt; }
    public function getUpdatedAt(): ?string { return $this->updatedAt; }
    public function getImagePublicId(): ?string { return $this->imagePublicId; }

    // Setters ...
    public function setTitle(string $t): void { $this->title = $t; }
    public function setDescription(string $d): void { $this->description = $d; }
    public function setDateTime(string $dt): void { $this->dateTime = $dt; }
    public function setVisibility(string $v): void { $this->visibility = $v; }
    public function setImage(?string $i): void { $this->image = $i; }
    public function setIsFeatured(bool $f): void { $this->isFeatured = $f; }
    public function setFeaturePriority(int $p): void { $this->featurePriority = $p; }
    public function setUpdatedAt(string $u): void { $this->updatedAt = $u; }
    public function setImagePublicId(?string $id): void { $this->imagePublicId = $id; }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'date_time' => $this->dateTime,
            'visibility' => $this->visibility,
            'image' => $this->image,
            'is_featured' => $this->isFeatured,
            'feature_priority' => $this->featurePriority,
            'created_at' => $this->createdAt,
            'updated_at' => $this->updatedAt,
            'image_public_id' => $this->imagePublicId,
        ];
    }
}
