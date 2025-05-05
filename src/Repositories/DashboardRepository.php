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

    /**
     * Retorna dados do dashboard do professor
     *
     * @param int $professorId
     * @return DashboardProfessor
     */
    public function getProfessorDashboard(int $professorId): DashboardProfessor
    {
        // Total de turmas ativas do professor
        $turmasAtivas = (int) $this->pdo
            ->query(
                "SELECT COUNT(*)
                 FROM classrooms
                 WHERE professor_id = $professorId
                   AND status = 'ativa'"
            )
            ->fetchColumn();

        // Solicitações pendentes de matrícula
        // Nota: coluna `status` em enrollments armazena valores em português ('pendente')
        $solicitacoesPendentes = (int) $this->pdo
            ->query(
                "SELECT COUNT(*)
                 FROM enrollments
                 WHERE classroom_id IN (
                     SELECT id FROM classrooms WHERE professor_id = $professorId
                 )
                   AND status = 'pendente'"
            )
            ->fetchColumn();

        // Atividades pendentes de correção
        // A tabela `assignments` não possui coluna `status`. Se você tiver uma tabela de submissões,
        // substitua por ela; caso contrário, remova o filtro `status`.
        $atividadesACorrigir = (int) $this->pdo
            ->query(
                "SELECT COUNT(*)
                 FROM assignments
                 WHERE classroom_id IN (
                     SELECT id FROM classrooms WHERE professor_id = $professorId
                 )"
            )
            ->fetchColumn();

        // Metas pendentes (implementar lógica específica)
        $metasPendentes = 0;

        // Dados para gráficos de progresso
        $graficosProgresso = [
            'notas'    => [], // preencher com médias de notas
            'presenca' => []  // preencher com médias de presença
        ];

        // Atalhos rápidos
        $atalhosRapidos = [
            'nova_turma'     => true,
            'nova_atividade' => true
        ];

        return new DashboardProfessor(
            $professorId,
            $turmasAtivas,
            $solicitacoesPendentes,
            $atividadesACorrigir,
            $metasPendentes,
            $graficosProgresso,
            $atalhosRapidos
        );
    }

    /**
     * Retorna dados do dashboard do aluno
     *
     * @param int $alunoId
     * @return DashboardAluno
     */
    public function getAlunoDashboard(int $alunoId): DashboardAluno
    {
        // Total de turmas em que o aluno está matriculado
        $turmasMatriculadas = (int) $this->pdo
            ->query(
                "SELECT COUNT(*)
                 FROM enrollments
                 WHERE user_id = $alunoId
                   AND status = 'aprovado'"
            )
            ->fetchColumn();

        // Atividades pendentes do aluno
        // A tabela `assignments` não possui coluna `status`, ajuste se usar tabela de submissões
        $atividadesPendentes = (int) $this->pdo
            ->query(
                "SELECT COUNT(*)
                 FROM assignments
                 WHERE classroom_id IN (
                     SELECT classroom_id FROM enrollments WHERE user_id = $alunoId
                 )"
            )
            ->fetchColumn();

        // Feedbacks recentes (implementar lógica)
        $feedbacksRecentes = 0;

        // Metas pessoais (implementar lógica)
        $metasPessoais = 0;

        // Indicadores de desempenho do aluno
        $indicadoresDesempenho = [
            'media_notas'         => 0, // preencher com média real
            'percentual_entregas' => 0  // preencher com percentual real
        ];

        // Links de ação no dashboard do aluno
        $linksAcoes = [
            'enviar_atividade' => true,
            'ver_feedback'     => true
        ];

        return new DashboardAluno(
            $alunoId,
            $turmasMatriculadas,
            $atividadesPendentes,
            $feedbacksRecentes,
            $metasPessoais,
            $indicadoresDesempenho,
            $linksAcoes
        );
    }
}