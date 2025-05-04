<?php

namespace App\Models;

class Message
{
    private ?int $id;
    private int $conversationId;
    private int $senderId;            // user_id do autor
    private string $content;
    private string $contentType;      // 'text', 'image', 'file', etc.
    private ?string $attachmentUrl;   // caminho caso seja file/image
    private ?string $createdAt;
    private ?string $readAt;          // marca quando foi lida

    public function __construct(
        int $conversationId,
        int $senderId,
        string $content,
        string $contentType = 'text',
        ?string $attachmentUrl = null,
        ?int $id = null,
        ?string $createdAt = null,
        ?string $readAt = null
    ) {
        $this->id = $id;
        $this->conversationId = $conversationId;
        $this->senderId = $senderId;
        $this->content = $content;
        $this->contentType = $contentType;
        $this->attachmentUrl = $attachmentUrl;
        $this->createdAt = $createdAt;
        $this->readAt = $readAt;
    }

    // Getters
    public function getId(): ?int { return $this->id; }
    public function getConversationId(): int { return $this->conversationId; }
    public function getSenderId(): int { return $this->senderId; }
    public function getContent(): string { return $this->content; }
    public function getContentType(): string { return $this->contentType; }
    public function getAttachmentUrl(): ?string { return $this->attachmentUrl; }
    public function getCreatedAt(): ?string { return $this->createdAt; }
    public function getReadAt(): ?string { return $this->readAt; }

    // Setters
    public function markAsRead(): void
    {
        $this->readAt = date('Y-m-d H:i:s');
    }

    public function toArray(): array
    {
        return [
            'id'               => $this->id,
            'conversation_id'  => $this->conversationId,
            'sender_id'        => $this->senderId,
            'content'          => $this->content,
            'content_type'     => $this->contentType,
            'attachment_url'   => $this->attachmentUrl,
            'created_at'       => $this->createdAt,
            'read_at'          => $this->readAt,
        ];
    }
}
