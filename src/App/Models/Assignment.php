<?php

namespace App\Models;

class Assignment {
    public ?int $id;
    public int $professorId;
    public int $classroomId;
    public string $titulo;
    public string $descricao;
    public string $tipo; // dissertativa, multipla_escolha, upload_arquivo, etc.
    public ?float $peso;
    public ?string $dataInicio;
    public ?string $dataFim;
    public ?array $anexos;
    public ?array $gruposAlvo; // IDs de grupos específicos
    public ?string $configuracoes; // JSON com configurações específicas por tipo
    public ?string $createdAt;
    public ?string $updatedAt;

    public function __construct(
        ?int $id,
        int $professorId,
        int $classroomId,
        string $titulo,
        string $descricao,
        string $tipo,
        ?float $peso = null,
        ?string $dataInicio = null,
        ?string $dataFim = null,
        ?array $anexos = null,
        ?array $gruposAlvo = null,
        ?string $configuracoes = null,
        ?string $createdAt = null,
        ?string $updatedAt = null
    ) {
        $this->id = $id;
        $this->professorId = $professorId;
        $this->classroomId = $classroomId;
        $this->titulo = $titulo;
        $this->descricao = $descricao;
        $this->tipo = $tipo;
        $this->peso = $peso;
        $this->dataInicio = $dataInicio;
        $this->dataFim = $dataFim;
        $this->anexos = $anexos;
        $this->gruposAlvo = $gruposAlvo;
        $this->configuracoes = $configuracoes;
        $this->createdAt = $createdAt;
        $this->updatedAt = $updatedAt;
    }
}