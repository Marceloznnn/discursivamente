<?php
namespace App\Models;

class ForumReply
{
    private int $id;
    private int $topicId;
    private int $userId;
    private string $content;
    private string $createdAt;

    public function __construct(array $data = [])
    {
        $this->id        = $data['id'] ?? 0;
        $this->topicId   = $data['topic_id'] ?? 0;
        $this->userId    = $data['user_id'] ?? 0;
        $this->content   = $data['content'] ?? '';
        $this->createdAt = $data['created_at'] ?? date('Y-m-d H:i:s');
    }

    public function getId(): int            { return $this->id; }
    public function getTopicId(): int       { return $this->topicId; }
    public function getUserId(): int        { return $this->userId; }
    public function getContent(): string    { return $this->content; }
    public function getCreatedAt(): string  { return $this->createdAt; }

    public function setContent(string $c): void { $this->content = $c; }
}
