<?php

namespace App\Models;

class ForumMessage
{
    private int $id;
    private int $userId;
    private int $courseId;
    private string $message;
    private string $createdAt;

    public function __construct(array $data = [])
    {
        $this->id = $data['id'] ?? 0;
        $this->userId = $data['user_id'] ?? 0;
        $this->courseId = $data['course_id'] ?? 0;
        $this->message = $data['message'] ?? '';
        $this->createdAt = $data['created_at'] ?? date('Y-m-d H:i:s');
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getUserId(): int
    {
        return $this->userId;
    }

    public function setUserId(int $userId): void
    {
        $this->userId = $userId;
    }

    public function getCourseId(): int
    {
        return $this->courseId;
    }

    public function setCourseId(int $courseId): void
    {
        $this->courseId = $courseId;
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    public function setMessage(string $message): void
    {
        $this->message = $message;
    }

    public function getCreatedAt(): string
    {
        return $this->createdAt;
    }

    public function setCreatedAt(string $createdAt): void
    {
        $this->createdAt = $createdAt;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'user_id' => $this->userId,
            'course_id' => $this->courseId,
            'message' => $this->message,
            'created_at' => $this->createdAt
        ];
    }
}