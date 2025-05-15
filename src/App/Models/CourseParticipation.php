<?php

namespace App\Models;

class CourseParticipation
{
    private ?int $id;
    private int $userId;
    private int $courseId;
    private string $joinedAt;
    private ?string $leftAt;

    public function __construct(
        int $userId,
        int $courseId,
        ?int $id = null,
        ?string $joinedAt = null,
        ?string $leftAt = null
    ) {
        $this->id = $id;
        $this->userId = $userId;
        $this->courseId = $courseId;
        $this->joinedAt = $joinedAt ?? date('Y-m-d H:i:s');
        $this->leftAt = $leftAt;
    }

    // Getters
    public function getId(): ?int { return $this->id; }
    public function getUserId(): int { return $this->userId; }
    public function getCourseId(): int { return $this->courseId; }
    public function getJoinedAt(): string { return $this->joinedAt; }
    public function getLeftAt(): ?string { return $this->leftAt; }

    // Setters
    public function setLeftAt(?string $leftAt): void { $this->leftAt = $leftAt; }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'user_id' => $this->userId,
            'course_id' => $this->courseId,
            'joined_at' => $this->joinedAt,
            'left_at' => $this->leftAt,
        ];
    }
}
