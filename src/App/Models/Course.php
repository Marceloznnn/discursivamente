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
    // 1) Declaração da nova propriedade
    private ?int $categoryId;
    // Propriedade para contar participantes ativos
    private int $activeCount = 0;

    public function __construct(
        string $title,
        string $description,
        int $creatorId,
        ?int $categoryId = null,   // 2) Parâmetro opcional
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
        $this->categoryId = $categoryId; // inicializa a categoria
    }

    // Getters
    public function getId(): ?int       { return $this->id; }
    public function getTitle(): string  { return $this->title; }
    public function getDescription(): string { return $this->description; }
    public function getCreatorId(): int { return $this->creatorId; }
    public function getPrice(): float   { return $this->price; }
    public function getCreatedAt(): ?string { return $this->createdAt; }
    public function getUpdatedAt(): ?string { return $this->updatedAt; }
    // 3) Ajuste do tipo de retorno para nullable
    public function getCategoryId(): ?int { return $this->categoryId; }
    // Getter e Setter para activeCount
    public function getActiveCount(): int { return $this->activeCount; }

    // Setters
    public function setTitle(string $title): void         { $this->title = $title; }
    public function setDescription(string $description): void { $this->description = $description; }
    public function setCreatorId(int $creatorId): void    { $this->creatorId = $creatorId; }
    public function setPrice(float $price): void          { $this->price = $price; }
    public function setUpdatedAt(string $updatedAt): void { $this->updatedAt = $updatedAt; }
    public function setCategoryId(?int $categoryId): void { $this->categoryId = $categoryId; }
    public function setActiveCount(int $count): void { $this->activeCount = $count; }

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
            'category_id' => $this->categoryId,
        ];
    }
}
