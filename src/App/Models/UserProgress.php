<?php

namespace App\Models;

class UserProgress
{
    private int $userId;
    private int $materialId;
    private ?string $completedAt;

    public function __construct( 
        int $userId,
        int $materialId,
        ?string $completedAt = null
    ) {
        $this->userId = $userId;
        $this->materialId = $materialId;
        $this->completedAt = $completedAt;
    }

    // Getters
    public function getUserId(): int
    {
        return $this->userId;
    }

    public function getMaterialId(): int
    {
        return $this->materialId;
    }

    public function getCompletedAt(): ?string
    {
        return $this->completedAt;
    }

    // Setters
    public function setCompletedAt(?string $completedAt): void
    {
        $this->completedAt = $completedAt;
    }

    public function toArray(): array
    {
        return [
            'user_id'      => $this->userId,
            'material_id'  => $this->materialId,
            'completed_at' => $this->completedAt,
        ];
    }
}
