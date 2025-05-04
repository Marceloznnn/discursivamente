<?php

namespace App\Models;

class Enrollment {
    public ?int $id;
    public int $userId;
    public int $classroomId;
    public string $status; // pendente, aprovado, recusado
    public ?string $informacoesAdicionais;
    public ?string $dataSolicitacao;
    public ?string $dataAprovacao;
    public ?string $dataRecusa;
    public ?string $motivoRecusa;
    public ?string $createdAt;
    public ?string $updatedAt;

    public function __construct(
        ?int $id,
        int $userId,
        int $classroomId,
        string $status,
        ?string $informacoesAdicionais = null,
        ?string $dataSolicitacao = null,
        ?string $dataAprovacao = null,
        ?string $dataRecusa = null,
        ?string $motivoRecusa = null,
        ?string $createdAt = null,
        ?string $updatedAt = null
    ) {
        $this->id = $id;
        $this->userId = $userId;
        $this->classroomId = $classroomId;
        $this->status = $status;
        $this->informacoesAdicionais = $informacoesAdicionais;
        $this->dataSolicitacao = $dataSolicitacao;
        $this->dataAprovacao = $dataAprovacao;
        $this->dataRecusa = $dataRecusa;
        $this->motivoRecusa = $motivoRecusa;
        $this->createdAt = $createdAt;
        $this->updatedAt = $updatedAt;
    }
}