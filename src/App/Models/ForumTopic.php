<?php
namespace App\Models;

class ForumTopic
{
    private int $id;
    private int $courseId;
    private int $userId;
    private string $title;
    private string $createdAt;

    public function __construct(array $data = [])
    {
        $this->id        = $data['id'] ?? 0;
        $this->courseId  = $data['course_id'] ?? 0;
        $this->userId    = $data['user_id'] ?? 0;
        $this->title     = $data['title'] ?? '';
        $this->createdAt = $data['created_at'] ?? date('Y-m-d H:i:s');
    }

    public function getId(): int               { return $this->id; }
    public function getCourseId(): int         { return $this->courseId; }
    public function getUserId(): int           { return $this->userId; }
    public function getTitle(): string         { return $this->title; }
    public function getCreatedAt(): string     { return $this->createdAt; }

    public function setTitle(string $t): void  { $this->title = $t; }
}
