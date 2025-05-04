<?php

namespace App\Models;

class DashboardAluno
{
    public int $userId;
    public int $turmasMatriculadas;
    public int $atividadesPendentes;
    public int $feedbacksRecentes;
    public int $metasPessoais;
    public array $indicadoresDesempenho; // Ex: ['media_notas' => 8.5, 'percentual_entregas' => 92]
    public array $linksAcoes;            // Ex: ['enviar_atividade' => true, 'ver_feedback' => true]

    public function __construct(
        int $userId,
        int $turmasMatriculadas,
        int $atividadesPendentes,
        int $feedbacksRecentes,
        int $metasPessoais,
        array $indicadoresDesempenho,
        array $linksAcoes
    ) {
        $this->userId = $userId;
        $this->turmasMatriculadas = $turmasMatriculadas;
        $this->atividadesPendentes = $atividadesPendentes;
        $this->feedbacksRecentes = $feedbacksRecentes;
        $this->metasPessoais = $metasPessoais;
        $this->indicadoresDesempenho = $indicadoresDesempenho;
        $this->linksAcoes = $linksAcoes;
    }
}