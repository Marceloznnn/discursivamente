<?php

namespace App\Models;

class Classroom
{
    public ?int $id;
    public int $professorId;
    public string $nome;
    public string $descricao;
    public string $status; // ativa, arquivada, apagada
    public string $privacidade; // aberta, protegida, privada
    public ?string $tokenAcesso;
    public ?int $capacity;
    public ?string $regrasAprovacao;
    public ?string $calendario;
    public ?string $bibliotecaMateriais;
    public ?string $estatisticas;
    public ?string $createdAt;
    public ?string $updatedAt;

    public function __construct(
        ?int $id,
        int $professorId,
        string $nome,
        string $descricao,
        string $status,
        string $privacidade,
        ?string $tokenAcesso = null,
        ?int $capacity = null,
        ?string $regrasAprovacao = null,
        ?string $calendario = null,
        ?string $bibliotecaMateriais = null,
        ?string $estatisticas = null,
        ?string $createdAt = null,
        ?string $updatedAt = null
    ) {
        $this->id = $id;
        $this->professorId = $professorId;
        $this->nome = $nome;
        $this->descricao = $descricao;
        $this->status = $status;
        $this->privacidade = $privacidade;
        $this->tokenAcesso = $tokenAcesso;
        $this->capacity = $capacity;
        $this->regrasAprovacao = $regrasAprovacao;
        $this->calendario = $calendario;
        $this->bibliotecaMateriais = $bibliotecaMateriais;
        $this->estatisticas = $estatisticas;
        $this->createdAt = $createdAt;
        $this->updatedAt = $updatedAt;
    }
}