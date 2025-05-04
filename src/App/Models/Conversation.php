<?php

namespace App\Models;

class Conversation
{
    private ?int $id;
    private string $subject;
    private int $createdBy;           // user_id quem iniciou
    private array $participantIds;    // array de user_id participantes
    private ?string $createdAt;
    private ?string $updatedAt;

    public function __construct(
        string $subject,
        int $createdBy,
        array $participantIds,
        ?int $id = null,
        ?string $createdAt = null,
        ?string $updatedAt = null
    ) {
        $this->id = $id;
        $this->subject = $subject;
        $this->createdBy = $createdBy;
        $this->participantIds = $participantIds;
        $this->createdAt = $createdAt;
        $this->updatedAt = $updatedAt;
    }

    // Getters
    public function getId(): ?int { return $this->id; }
    public function getSubject(): string { return $this->subject; }
    public function getCreatedBy(): int { return $this->createdBy; }
    public function getParticipantIds(): array { return $this->participantIds; }
    public function getCreatedAt(): ?string { return $this->createdAt; }
    public function getUpdatedAt(): ?string { return $this->updatedAt; }

    // Setters
    public function setParticipantIds(array $ids): void { $this->participantIds = $ids; }
    public function setUpdatedAt(string $ts): void { $this->updatedAt = $ts; }
    public function setSubject(string $subject): void {
        $this->subject = $subject;
    }
    
    public function toArray(): array
    {
        return [
            'id'             => $this->id,
            'subject'        => $this->subject,
            'created_by'     => $this->createdBy,
            'participants'   => implode(',', $this->participantIds),
            'created_at'     => $this->createdAt,
            'updated_at'     => $this->updatedAt,
        ];
    }
}
