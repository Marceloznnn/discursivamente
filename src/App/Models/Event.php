<?php

namespace App\Models;

class Event
{
    private ?int $id;
    private string $title;
    private string $description;
    private string $dateTime;    // formato: Y-m-d H:i:s
    private ?string $image;
    private string $visibility;   // public, restricted, etc.
    private ?string $createdAt;
    private ?string $updatedAt;

    public function __construct(
        string $title,
        string $description,
        string $dateTime,
        string $visibility = 'public',
        ?string $image = null,
        ?int $id = null,
        ?string $createdAt = null,
        ?string $updatedAt = null
    ) {
        $this->id = $id;
        $this->title = $title;
        $this->description = $description;
        $this->dateTime = $dateTime;
        $this->visibility = $visibility;
        $this->image = $image;
        $this->createdAt = $createdAt;
        $this->updatedAt = $updatedAt;
    }

    // Getters
    public function getId(): ?int { return $this->id; }
    public function getTitle(): string { return $this->title; }
    public function getDescription(): string { return $this->description; }
    public function getDateTime(): string { return $this->dateTime; }
    public function getImage(): ?string { return $this->image; }
    public function getVisibility(): string { return $this->visibility; }
    public function getCreatedAt(): ?string { return $this->createdAt; }
    public function getUpdatedAt(): ?string { return $this->updatedAt; }

    // Setters
    public function setTitle(string $title): void { $this->title = $title; }
    public function setDescription(string $description): void { $this->description = $description; }
    public function setDateTime(string $dateTime): void { $this->dateTime = $dateTime; }
    public function setImage(?string $image): void { $this->image = $image; }
    public function setVisibility(string $visibility): void { $this->visibility = $visibility; }
    public function setUpdatedAt(string $updatedAt): void { $this->updatedAt = $updatedAt; }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'date_time' => $this->dateTime,
            'image' => $this->image,
            'visibility' => $this->visibility,
            'created_at' => $this->createdAt,
            'updated_at' => $this->updatedAt,
        ];
    }
}


class Feedback
{
    private ?int $id;
    private int $userId;
    private string $comment;
    private ?int $rating;       // nota de 1 a 5, pode ser null
    private string $status;     // pending, resolved, rejected
    private ?string $createdAt;
    private ?string $updatedAt;

    public function __construct(
        int $userId,
        string $comment,
        ?int $rating = null,
        string $status = 'pending',
        ?int $id = null,
        ?string $createdAt = null,
        ?string $updatedAt = null
    ) {
        $this->id = $id;
        $this->userId = $userId;
        $this->comment = $comment;
        $this->rating = $rating;
        $this->status = $status;
        $this->createdAt = $createdAt;
        $this->updatedAt = $updatedAt;
    }

    // Getters
    public function getId(): ?int { return $this->id; }
    public function getUserId(): int { return $this->userId; }
    public function getComment(): string { return $this->comment; }
    public function getRating(): ?int { return $this->rating; }
    public function getStatus(): string { return $this->status; }
    public function getCreatedAt(): ?string { return $this->createdAt; }
    public function getUpdatedAt(): ?string { return $this->updatedAt; }

    // Setters
    public function setComment(string $comment): void { $this->comment = $comment; }
    public function setRating(?int $rating): void { $this->rating = $rating; }
    public function setStatus(string $status): void { $this->status = $status; }
    public function setUpdatedAt(string $updatedAt): void { $this->updatedAt = $updatedAt; }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'user_id' => $this->userId,
            'comment' => $this->comment,
            'rating' => $this->rating,
            'status' => $this->status,
            'created_at' => $this->createdAt,
            'updated_at' => $this->updatedAt,
        ];
    }
}


class SupportMessage
{
    private ?int $id;
    private int $userId;
    private string $subject;
    private string $message;
    private string $status;       // pending, answered
    private ?string $response;    // texto de resposta do admin
    private ?string $createdAt;
    private ?string $updatedAt;

    public function __construct(
        int $userId,
        string $subject,
        string $message,
        string $status = 'pending',
        ?string $response = null,
        ?int $id = null,
        ?string $createdAt = null,
        ?string $updatedAt = null
    ) {
        $this->id = $id;
        $this->userId = $userId;
        $this->subject = $subject;
        $this->message = $message;
        $this->status = $status;
        $this->response = $response;
        $this->createdAt = $createdAt;
        $this->updatedAt = $updatedAt;
    }

    // Getters
    public function getId(): ?int { return $this->id; }
    public function getUserId(): int { return $this->userId; }
    public function getSubject(): string { return $this->subject; }
    public function getMessage(): string { return $this->message; }
    public function getStatus(): string { return $this->status; }
    public function getResponse(): ?string { return $this->response; }
    public function getCreatedAt(): ?string { return $this->createdAt; }
    public function getUpdatedAt(): ?string { return $this->updatedAt; }

    // Setters
    public function setSubject(string $subject): void { $this->subject = $subject; }
    public function setMessage(string $message): void { $this->message = $message; }
    public function setStatus(string $status): void { $this->status = $status; }
    public function setResponse(?string $response): void { $this->response = $response; }
    public function setUpdatedAt(string $updatedAt): void { $this->updatedAt = $updatedAt; }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'user_id' => $this->userId,
            'subject' => $this->subject,
            'message' => $this->message,
            'status' => $this->status,
            'response' => $this->response,
            'created_at' => $this->createdAt,
            'updated_at' => $this->updatedAt,
        ];
    }
}
