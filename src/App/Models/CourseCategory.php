<?php

namespace App\Models;

class CourseCategory
{
    private ?int $id;
    private string $name;

    public function __construct(string $name, ?int $id = null)
    {
        $this->id = $id;
        $this->name = $name;
    }

    public function getId(): ?int { return $this->id; }
    public function getName(): string { return $this->name; }

    public function setName(string $name): void { $this->name = $name; }

    public function toArray(): array
    {
        return [
            'id'   => $this->id,
            'name' => $this->name,
        ];
    }
}
