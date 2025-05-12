<?php

namespace App\Models;

class Course
{
    private ?int $id;
    private string $title;
    private string $description;
    private int $creatorId;
    private float $price;
    private ?string $createdAt;
    private ?string $updatedAt;

    public function __construct(
        string $title,
        string $description,
        int $creatorId,
        ?int $id = null,
        float $price = 0.00,
        ?string $createdAt = null,
        ?string $updatedAt = null
    ) {
        $this->id = $id;
        $this->title = $title;
        $this->description = $description;
        $this->creatorId = $creatorId;
        $this->price = $price;
        $this->createdAt = $createdAt;
        $this->updatedAt = $updatedAt;
    }

    // Getters
    public function getId(): ?int { return $this->id; }
    public function getTitle(): string { return $this->title; }
    public function getDescription(): string { return $this->description; }
    public function getCreatorId(): int { return $this->creatorId; }
    public function getPrice(): float { return $this->price; }
    public function getCreatedAt(): ?string { return $this->createdAt; }
    public function getUpdatedAt(): ?string { return $this->updatedAt; }

    // Setters
    public function setTitle(string $title): void { $this->title = $title; }
    public function setDescription(string $description): void { $this->description = $description; }
    public function setCreatorId(int $creatorId): void { $this->creatorId = $creatorId; }
    public function setPrice(float $price): void { $this->price = $price; }
    public function setUpdatedAt(string $updatedAt): void { $this->updatedAt = $updatedAt; }

    public function toArray(): array
    {
        return [
            'id'          => $this->id,
            'title'       => $this->title,
            'description' => $this->description,
            'creator_id'  => $this->creatorId,
            'price'       => $this->price,
            'created_at'  => $this->createdAt,
            'updated_at'  => $this->updatedAt,
        ];
    }
}
