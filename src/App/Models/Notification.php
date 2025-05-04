<?php

namespace App\Models;

class Notification
{
    private ?int $id;
    private int $userId;            // destinatário
    private string $type;           // ex: 'new_feedback', 'new_message'
    private string $content;        // texto ou payload resumido
    private bool $read;             // lida ou não
    private ?string $createdAt;

    public function __construct(
        int $userId,
        string $type,
        string $content,
        bool $read = false,
        ?int $id = null,
        ?string $createdAt = null
    ) {
        $this->id        = $id;
        $this->userId    = $userId;
        $this->type      = $type;
        $this->content   = $content;
        $this->read      = $read;
        $this->createdAt = $createdAt;
    }

    // Getters
    public function getId(): ?int        { return $this->id; }
    public function getUserId(): int     { return $this->userId; }
    public function getType(): string    { return $this->type; }
    public function getContent(): string { return $this->content; }
    public function isRead(): bool       { return $this->read; }
    public function getCreatedAt(): ?string { return $this->createdAt; }

    // Setters
    public function markAsRead(): void { $this->read = true; }

    public function toArray(): array
    {
        return [
            'id'         => $this->id,
            'user_id'    => $this->userId,
            'type'       => $this->type,
            'content'    => $this->content,
            'read'       => $this->read,
            'created_at' => $this->createdAt,
        ];
    }
}
