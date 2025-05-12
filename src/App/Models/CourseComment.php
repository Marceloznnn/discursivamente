<?php

namespace App\Models;

class CourseComment
{
    private ?int $id;
    private int $courseId;
    private int $userId;
    private string $comment;
    private int $rating;
    private ?string $createdAt;

    public function __construct(
        int $courseId,
        int $userId,
        string $comment,
        int $rating,
        ?int $id = null,
        ?string $createdAt = null
    ) {
        $this->id        = $id;
        $this->courseId  = $courseId;
        $this->userId    = $userId;
        $this->comment   = $comment;
        $this->rating    = $rating;
        $this->createdAt = $createdAt;
    }

    // Getters
    public function getId(): ?int       { return $this->id; }
    public function getCourseId(): int  { return $this->courseId; }
    public function getUserId(): int    { return $this->userId; }
    public function getComment(): string{ return $this->comment; }
    public function getRating(): int    { return $this->rating; }
    public function getCreatedAt(): ?string { return $this->createdAt; }

    // Setters
    public function setComment(string $comment): void { $this->comment = $comment; }
    public function setRating(int $rating): void     { $this->rating = $rating; }

    public function toArray(): array
    {
        return [
            'id'         => $this->id,
            'course_id'  => $this->courseId,
            'user_id'    => $this->userId,
            'comment'    => $this->comment,
            'rating'     => $this->rating,
            'created_at' => $this->createdAt,
        ];
    }
}
