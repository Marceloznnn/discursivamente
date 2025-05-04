<?php

namespace App\Models;

class DashboardProfessor
{
    public int $userId;
    public int $turmasAtivas;
    public int $solicitacoesPendentes;
    public int $atividadesACorrigir;
    public int $metasPendentes;
    public array $graficosProgresso; // Ex: ['notas' => [...], 'presenca' => [...]]
    public array $atalhosRapidos;    // Ex: ['nova_turma' => true, 'nova_atividade' => true]

    public function __construct(
        int $userId,
        int $turmasAtivas,
        int $solicitacoesPendentes,
        int $atividadesACorrigir,
        int $metasPendentes,
        array $graficosProgresso,
        array $atalhosRapidos
    ) {
        $this->userId = $userId;
        $this->turmasAtivas = $turmasAtivas;
        $this->solicitacoesPendentes = $solicitacoesPendentes;
        $this->atividadesACorrigir = $atividadesACorrigir;
        $this->metasPendentes = $metasPendentes;
        $this->graficosProgresso = $graficosProgresso;
        $this->atalhosRapidos = $atalhosRapidos;
    }
}