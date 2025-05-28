<?php

namespace App\Models;

class SupportChat
{
    private ?int $id;
    private int $userId;
    private string $message;
    private bool $isSupport;
    private string $chatId;
    private ?string $createdAt;

    public function __construct(
        int $userId,
        string $message,
        bool $isSupport,
        string $chatId,
        ?int $id = null,
        ?string $createdAt = null
    ) {
        $this->id = $id;
        $this->userId = $userId;
        $this->message = $message;
        $this->isSupport = $isSupport;
        $this->chatId = $chatId;
        $this->createdAt = $createdAt;
    }

    // Getters
    public function getId(): ?int { return $this->id; }
    public function getUserId(): int { return $this->userId; }
    public function getMessage(): string { return $this->message; }
    public function isSupport(): bool { return $this->isSupport; }
    public function getChatId(): string { return $this->chatId; }
    public function getCreatedAt(): ?string { return $this->createdAt; }

    // Setters
    public function setMessage(string $message): void { $this->message = $message; }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'user_id' => $this->userId,
            'message' => $this->message,
            'is_support' => $this->isSupport,
            'chat_id' => $this->chatId,
            'created_at' => $this->createdAt,
        ];
    }
}
