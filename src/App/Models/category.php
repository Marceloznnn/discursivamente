<?php
namespace App\Models;

class Category
{
    private ?int $id;
    private string $name;
    private ?string $createdAt;
    private ?string $updatedAt;

    public function __construct(
        string $name,
        ?int $id = null,
        ?string $createdAt = null,
        ?string $updatedAt = null
    ) {
        $this->id = $id;
        $this->name = $name;
        $this->createdAt = $createdAt;
        $this->updatedAt = $updatedAt;
    }

    // Getters
    public function getId(): ?int       { return $this->id; }
    public function getName(): string   { return $this->name; }
    public function getCreatedAt(): ?string { return $this->createdAt; }
    public function getUpdatedAt(): ?string { return $this->updatedAt; }

    // Setters
    public function setName(string $name): void { $this->name = $name; }
    public function setUpdatedAt(string $ts): void { $this->updatedAt = $ts; }

    public function toArray(): array
    {
        return [
            'id'         => $this->id,
            'name'       => $this->name,
            'created_at' => $this->createdAt,
            'updated_at' => $this->updatedAt,
        ];
    }
}
