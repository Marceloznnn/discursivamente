<?php

namespace App\Models;

class Feedback
{
    private ?int $id;
    private int $userId;
    private string $message;
    private ?int $rating;
    private string $status; // pendente, resolvido, ignorado
    private ?string $createdAt;

    public function __construct(
        int $userId,
        string $message,
        string $status = 'pendente',
        ?int $rating = null,
        ?int $id = null,
        ?string $createdAt = null
    ) {
        $this->id = $id;
        $this->userId = $userId;
        $this->message = $message;
        $this->status = $status;
        $this->rating = $rating;
        $this->createdAt = $createdAt;
    }

    // Getters
    public function getId(): ?int { return $this->id; }
    public function getUserId(): int { return $this->userId; }
    public function getMessage(): string { return $this->message; }
    public function getRating(): ?int { return $this->rating; }
    public function getStatus(): string { return $this->status; }
    public function getCreatedAt(): ?string { return $this->createdAt; }

    // Setters
    public function setStatus(string $status): void { $this->status = $status; }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'user_id' => $this->userId,
            'message' => $this->message,
            'rating' => $this->rating,
            'status' => $this->status,
            'created_at' => $this->createdAt
        ];
    }
}
