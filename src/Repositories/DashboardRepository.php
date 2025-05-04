<?php

namespace Repositories;

use PDO;
use App\Models\DashboardProfessor;
use App\Models\DashboardAluno;

class DashboardRepository
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    // Retorna dados do dashboard do professor
    public function getProfessorDashboard(int $professorId): DashboardProfessor
    {
        // Exemplos de queries, ajuste conforme seu banco
        $turmasAtivas = $this->pdo->query("SELECT COUNT(*) FROM classes WHERE professor_id = $professorId AND status = 'ativa'")->fetchColumn();
        $solicitacoesPendentes = $this->pdo->query("SELECT COUNT(*) FROM enrollments WHERE class_id IN (SELECT id FROM classes WHERE professor_id = $professorId) AND status = 'pending'")->fetchColumn();
        $atividadesACorrigir = $this->pdo->query("SELECT COUNT(*) FROM assignments WHERE class_id IN (SELECT id FROM classes WHERE professor_id = $professorId) AND status = 'pending'")->fetchColumn();
        $metasPendentes = 0; // Implemente conforme sua lógica

        $graficosProgresso = [
            'notas' => [], // Busque médias de notas
            'presenca' => [] // Busque médias de presença
        ];
        $atalhosRapidos = [
            'nova_turma' => true,
            'nova_atividade' => true
        ];

        return new DashboardProfessor(
            $professorId,
            (int)$turmasAtivas,
            (int)$solicitacoesPendentes,
            (int)$atividadesACorrigir,
            (int)$metasPendentes,
            $graficosProgresso,
            $atalhosRapidos
        );
    }

    // Retorna dados do dashboard do aluno
    public function getAlunoDashboard(int $alunoId): DashboardAluno
    {
        $turmasMatriculadas = $this->pdo->query("SELECT COUNT(*) FROM enrollments WHERE user_id = $alunoId AND status = 'approved'")->fetchColumn();
        $atividadesPendentes = $this->pdo->query("SELECT COUNT(*) FROM assignments WHERE class_id IN (SELECT class_id FROM enrollments WHERE user_id = $alunoId) AND status = 'pending'")->fetchColumn();
        $feedbacksRecentes = 0; // Implemente conforme sua lógica
        $metasPessoais = 0; // Implemente conforme sua lógica

        $indicadoresDesempenho = [
            'media_notas' => 0, // Busque média real
            'percentual_entregas' => 0 // Busque percentual real
        ];
        $linksAcoes = [
            'enviar_atividade' => true,
            'ver_feedback' => true
        ];

        return new DashboardAluno(
            $alunoId,
            (int)$turmasMatriculadas,
            (int)$atividadesPendentes,
            (int)$feedbacksRecentes,
            (int)$metasPessoais,
            $indicadoresDesempenho,
            $linksAcoes
        );
    }
}