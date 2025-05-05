<?php

namespace Controller\pages;

use Repositories\ClassroomRepository;
use Repositories\EnrollmentRepository;
use Repositories\AssignmentRepository;
use App\Models\Assignment;
use Infrastructure\Database\Connection;
use PDO;

class ClassroomController
{
    private $twig;
    private PDO $pdo;
    private EnrollmentRepository $enrollmentRepo;
    private AssignmentRepository $assignmentRepo;

    public function __construct($twig)
    {
        $this->twig           = $twig;
        $this->pdo            = Connection::getInstance();
        $this->enrollmentRepo = new EnrollmentRepository($this->pdo);
        $this->assignmentRepo = new AssignmentRepository($this->pdo);
    }

    /**
     * Lista turmas do professor
     */
    public function indexProfessor()
    {
        $professorId = $_SESSION['user']['id'];
        $stmt = $this->pdo->prepare(
            "SELECT id, nome, descricao, status, privacidade
             FROM classrooms
             WHERE professor_id = :professorId"
        );
        $stmt->execute(['professorId' => $professorId]);
        $classes = $stmt->fetchAll(PDO::FETCH_ASSOC);

        echo $this->twig->render('classes/professor/index.twig', [
            'classes' => $classes
        ]);
    }

    /**
     * Exibe resumo de solicitações pendentes por turma do professor
     */
    public function manageRequestsOverview()
    {
        $professorId = $_SESSION['user']['id'];
        $summary = $this->enrollmentRepo->countPendingByProfessor($professorId);

        echo $this->twig->render('classes/professor/overview_requests.twig', [
            'summary' => $summary
        ]);
    }

    /**
     * Lista atividades de uma turma para o professor
     */
    public function listAssignments(int $classroomId)
    {
        $activities = $this->assignmentRepo->findByClassroom($classroomId);
        echo $this->twig->render('classes/professor/assignments.twig', [
            'classroomId' => $classroomId,
            'activities'  => $activities
        ]);
    }

    /**
     * Exibe formulário para criar nova atividade
     */
    public function showCreateAssignmentForm(int $classroomId)
    {
        echo $this->twig->render('classes/professor/create_assignment.twig', [
            'classroomId' => $classroomId
        ]);
    }

    /**
     * Persiste nova atividade
     */
    public function storeAssignment(int $classroomId)
    {
        $assignment = new Assignment(
            null,
            (int) $_SESSION['user']['id'],
            $classroomId,
            trim($_POST['titulo'] ?? ''),
            trim($_POST['descricao'] ?? ''),
            $_POST['tipo'] ?? 'dissertativa',
            isset($_POST['peso']) ? (float)$_POST['peso'] : null,
            $_POST['dataInicio'] ?? null,
            $_POST['dataFim'] ?? null,
            null,
            null,
            null,
            null,
            null
        );
        $this->assignmentRepo->create($assignment);

        header("Location: /classes/professor/{$classroomId}/assignments");
exit;
    }

    public function indexAluno()
    {
        $alunoId = $_SESSION['user']['id'];
        $stmt = $this->pdo->prepare(
            "SELECT id, nome, descricao, status, privacidade
             FROM classrooms
             WHERE privacidade = 'aberta'"
        );
        $stmt->execute();
        $classes = $stmt->fetchAll(PDO::FETCH_ASSOC);

        echo $this->twig->render('classes/aluno/index.twig', [
            'classes' => $classes
        ]);
    }

    public function view($classroomId)
    {
        $sql = "
            SELECT c.*, u.name AS professor_nome
            FROM classrooms c
            JOIN users u ON c.professor_id = u.id
            WHERE c.id = :classroomId
        ";

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':classroomId', $classroomId, PDO::PARAM_INT);
        $stmt->execute();
        $classroom = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$classroom) {
            header('Location: classes/explore');
            exit;
        }

        echo $this->twig->render('classes/aluno/view.twig', [
            'classroom' => $classroom
        ]);
    }

    public function join($classroomId)
    {
        $alunoId = $_SESSION['user']['id'];
        $stmt = $this->pdo->prepare(
            "SELECT COUNT(*) FROM classroom_students
             WHERE classroom_id = :classroomId AND aluno_id = :alunoId"
        );
        $stmt->execute([
            'classroomId' => $classroomId,
            'alunoId' => $alunoId
        ]);
        if ($stmt->fetchColumn()) {
            header('Location: classes/aluno');
            exit;
        }
        $insert = $this->pdo->prepare(
            "INSERT INTO classroom_students (classroom_id, aluno_id)
             VALUES (:classroomId, :alunoId)"
        );
        $insert->execute([
            'classroomId' => $classroomId,
            'alunoId' => $alunoId
        ]);

        header('Location: classes/aluno');
        exit;
    }

    public function edit($classroomId)
    {
        $professorId = $_SESSION['user']['id'];
        $stmt = $this->pdo->prepare(
            "SELECT id, nome, descricao, status, privacidade
             FROM classrooms
             WHERE professor_id = :professorId AND id = :classroomId"
        );
        $stmt->execute([
            'professorId' => $professorId,
            'classroomId' => $classroomId
        ]);
        $classroom = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$classroom) {
            header('Location: classes/professor');
            exit;
        }
        echo $this->twig->render('classes/professor/edit.twig', [
            'classroom' => $classroom
        ]);
    }

    public function update($classroomId)
    {
        $professorId = $_SESSION['user']['id'];
        $nome = trim($_POST['nome'] ?? '');
        $descricao = trim($_POST['descricao'] ?? '');
        $status = $_POST['status'] ?? 'ativa';
        $privacidade = $_POST['privacidade'] ?? 'aberta';
        $stmt = $this->pdo->prepare(
            "UPDATE classrooms
             SET nome = :nome, descricao = :descricao, status = :status, privacidade = :privacidade
             WHERE id = :classroomId AND professor_id = :professorId"
        );
        $stmt->execute([
            'nome' => $nome,
            'descricao' => $descricao,
            'status' => $status,
            'privacidade' => $privacidade,
            'classroomId' => $classroomId,
            'professorId' => $professorId
        ]);

        header('Location: classes/professor');
        exit;
    }

    public function explore()
    {
        $repo = new ClassroomRepository($this->pdo);
        $classes = $repo->findPublicAndActiveWithProfessorName();

        echo $this->twig->render('classes/aluno/explore.twig', [
            'classes' => $classes
        ]);
    }

    public function create()
    {
        echo $this->twig->render('classes/professor/create.twig');
    }

    public function editAssignment(int $classroomId, int $assignmentId): void
{
    $assignmentRepository = new AssignmentRepository($this->pdo);
    $assignment = $assignmentRepository->findById($assignmentId);

    echo $this->twig->render('classes/professor/edit_assignment.twig', [
        'classroomId' => $classroomId,
        'assignment' => $assignment
    ]);
}


    public function store()
    {
        $nome = trim($_POST['nome'] ?? '');
        $descricao = trim($_POST['descricao'] ?? '');
        $status = $_POST['status'] ?? 'ativa';
        $privacidade = $_POST['privacidade'] ?? 'aberta';
        $professorId = $_SESSION['user']['id'];
        $stmt = $this->pdo->prepare(
            "INSERT INTO classrooms (professor_id, nome, descricao, status, privacidade)
             VALUES (:professorId, :nome, :descricao, :status, :privacidade)"
        );
        $stmt->execute([
            'professorId' => $professorId,
            'nome' => $nome,
            'descricao' => $descricao,
            'status' => $status,
            'privacidade' => $privacidade
        ]);

        header('Location: classes/professor');
        exit;
    }

    public function showRequestForm($classroomId)
    {
        $stmt = $this->pdo->prepare("SELECT id, nome, descricao FROM classrooms WHERE id = :classroomId");
        $stmt->execute(['classroomId' => $classroomId]);
        $classroom = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$classroom) {
            header('Location: classes/explore');
            exit;
        }
        echo $this->twig->render('classes/aluno/solicitar.twig', [
            'classroom' => $classroom
        ]);
    }

    // logo após editAssignment, adicione:
    public function showAssignment(int $classroomId, int $assignmentId): void
    {
        // buscar os dados da activity
        $assignment = $this->assignmentRepo->findById($assignmentId);
        
        // renderizar o template de detalhes
        echo $this->twig->render('classes/professor/show_assignment.twig', [
            'classroomId' => $classroomId,
            'assignment'  => $assignment,
        ]);
    }

    public function requestEnrollment($classroomId)
    {
        $userId = $_SESSION['user']['id'];
        $info   = trim($_POST['informacoesAdicionais'] ?? null);
        $this->enrollmentRepo->create($userId, $classroomId, $info);
        header('Location: classes/aluno');
        exit;
    }

    public function listRequests($classroomId)
    {
        $requests = $this->enrollmentRepo->findPendingByClassroom($classroomId);
        echo $this->twig->render('classes/professor/solicitacoes.twig', [
            'requests' => $requests
        ]);
    }

    public function approveRequest($classroomId, $requestId)
    {
        $this->enrollmentRepo->approve($requestId);
        header("Location: /classes/professor/{$classroomId}/requests");
        exit;
    }

    public function rejectRequest($classroomId, $requestId)
    {
        $motivo = trim($_POST['motivoRecusa'] ?? null);
        $this->enrollmentRepo->reject($requestId, $motivo);
        header("Location: classes/professor/{$classroomId}/requests");
        exit;
    }

    public function updateAssignment(int $classroomId, int $assignmentId): void
{
    $assignment = new Assignment(
        $assignmentId,
        (int) $_SESSION['user']['id'],
        $classroomId,
        trim($_POST['titulo'] ?? ''),
        trim($_POST['descricao'] ?? ''),
        $_POST['tipo'] ?? 'dissertativa',
        isset($_POST['peso']) ? (float)$_POST['peso'] : null,
        $_POST['dataInicio'] ?? null,
        $_POST['dataFim'] ?? null,
        null,
        null,
        null,
        null,
        $_POST['configuracoes'] ?? '{}'
    );

    $this->assignmentRepo->update($assignment);

    header("Location: /classes/professor/{$classroomId}/assignments");
    exit;
}
}