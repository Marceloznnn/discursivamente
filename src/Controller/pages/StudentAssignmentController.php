<?php

namespace Controller\pages;

use Repositories\AssignmentRepository;
use Repositories\SubmissionRepository;
use Infrastructure\Database\Connection;
use Models\Submission;
use PDO;

class StudentAssignmentController
{
    private $twig;
    private PDO $pdo;
    private AssignmentRepository $assignmentRepo;
    private SubmissionRepository $submissionRepo;

    public function __construct($twig)
    {
        $this->twig = $twig;
        $this->pdo = Connection::getInstance();
        $this->assignmentRepo = new AssignmentRepository($this->pdo);
        $this->submissionRepo = new SubmissionRepository($this->pdo);
    }

    /**
     * Exibe detalhe de uma atividade para o aluno
     * URL: GET /classes/aluno/{classroomId}/assignments/{id}
     */
    public function showAssignmentAluno(int $classroomId, int $assignmentId): void
    {
        // Busca a atividade
        $assignment = $this->assignmentRepo->findById($assignmentId);

        // Busca submissão do aluno, se existir
        $studentId = $_SESSION['user']['id'];
        $submission = $this->submissionRepo->findByAssignmentAndStudent($assignmentId, $studentId);

        echo $this->twig->render('classes/aluno/show_assignment.twig', [
            'classroomId' => $classroomId,
            'assignment' => $assignment,
            'submission' => $submission,
        ]);
    }

    /**
     * Processa envio de resposta do aluno
     * URL: POST /classes/aluno/{classroomId}/assignments/{id}/submit
     */
    public function submitAssignmentAluno(int $classroomId, int $assignmentId): void
    {
        $studentId = $_SESSION['user']['id'];
        $answer = trim($_POST['resposta'] ?? '');
        $file = $_FILES['arquivo'] ?? null;

        // Validação básica
        if ($answer === '') {
            $_SESSION['flash_error'] = 'A resposta não pode ficar em branco.';
            header("Location: /classes/aluno/{$classroomId}/assignments/{$assignmentId}");
            exit;
        }

        // Lidar com upload de arquivo, se houver
        $arquivoPath = null;
        if ($file && $file['error'] === UPLOAD_ERR_OK) {
            $uploadDir = 'uploads/';  // Defina o diretório de upload
            $arquivoPath = $uploadDir . basename($file['name']);
            move_uploaded_file($file['tmp_name'], $arquivoPath);
        }

        // Cria objeto Submission e popula dados
        $submission = new Submission();
        $submission->setAssignmentId($assignmentId);
        $submission->setStudentId($studentId);
        $submission->setContent($answer);
        $submission->setArquivoPath($arquivoPath);
        $submission->setStatus('entregue');  // Inicialmente a submissão está 'entregue'
        $submission->setSubmittedAt(date('Y-m-d H:i:s'));

        // Salva no banco
        $this->submissionRepo->create($submission);

        $_SESSION['flash_success'] = 'Resposta enviada com sucesso!';
        header("Location: /classes/aluno/{$classroomId}/assignments/{$assignmentId}");
        exit;
    }

    /**
     * Lista atividades pendentes do aluno
     * URL: GET /classes/aluno/{classroomId}/assignments/pending
     */
    public function listPendingAssignments(int $classroomId): void
    {
        $studentId = $_SESSION['user']['id'];

        // Busca todas as atividades da turma
        $assignments = $this->assignmentRepo->findByClassroom($classroomId);

        // Busca todas as submissões do aluno
        $submissions = $this->submissionRepo->findByStudent($studentId);
        $submittedAssignmentIds = array_column($submissions, 'assignment_id');

        // Filtra atividades ainda não respondidas
        $pendingAssignments = array_filter($assignments, function ($assignment) use ($submittedAssignmentIds) {
            return !in_array($assignment['id'], $submittedAssignmentIds);
        });

        echo $this->twig->render('classes/aluno/assignments_pending.twig', [
            'classroomId' => $classroomId,
            'assignments' => $pendingAssignments,
        ]);
    }
}
