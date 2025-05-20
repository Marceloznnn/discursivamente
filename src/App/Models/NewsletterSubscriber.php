<?php
namespace App\Models;

class NewsletterSubscriber
{
    private ?int $id;
    private string $email;
    private bool $prefCursos;
    private bool $prefEventos;
    private bool $prefConteudos;
    private string $createdAt;

    public function __construct(string $email, bool $prefCursos, bool $prefEventos, bool $prefConteudos, ?int $id = null, ?string $createdAt = null)
    {
        $this->id = $id;
        $this->email = $email;
        $this->prefCursos = $prefCursos;
        $this->prefEventos = $prefEventos;
        $this->prefConteudos = $prefConteudos;
        $this->createdAt = $createdAt ?? date('Y-m-d H:i:s');
    }

    public function getId(): ?int { return $this->id; }
    public function getEmail(): string { return $this->email; }
    public function getPrefCursos(): bool { return $this->prefCursos; }
    public function getPrefEventos(): bool { return $this->prefEventos; }
    public function getPrefConteudos(): bool { return $this->prefConteudos; }
    public function getCreatedAt(): string { return $this->createdAt; }
}
