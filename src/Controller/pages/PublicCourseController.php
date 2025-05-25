<?php

namespace Controller\pages;

use Middleware\AuthMiddleware;
use Repositories\CourseRepository;
use Repositories\CategoryRepository;
use Repositories\CourseCommentRepository;
use Repositories\UserRepository;
use Repositories\CourseParticipationRepository;
use Repositories\ModuleRepository;
use Repositories\MaterialEntryRepository;
use Repositories\UserProgressRepository;
use Services\TranscriptionService;
use App\Models\CourseComment;
use App\Models\UserProgress;
use PDO;
use Twig\Environment;

class PublicCourseController
{
    private Environment $twig;
    private PDO $pdo;
    private TranscriptionService $transcriptionService;

    private CourseRepository $courseRepo;
    private CategoryRepository $categoryRepo;
    private CourseCommentRepository $commentRepo;
    private UserRepository $userRepo;
    private CourseParticipationRepository $participationRepo;
    private ModuleRepository $moduleRepo;
    private MaterialEntryRepository $entryRepo;
    private UserProgressRepository $progressRepo;

    public function __construct(Environment $twig, PDO $pdo)
    {
        $this->twig = $twig;
        $this->pdo  = $pdo;

        $projectDir = dirname(__DIR__, 2);
        $this->transcriptionService = new TranscriptionService($projectDir);

        $this->courseRepo        = new CourseRepository($pdo);
        $this->categoryRepo      = new CategoryRepository($pdo);
        $this->commentRepo       = new CourseCommentRepository($pdo);
        $this->userRepo          = new UserRepository($pdo);
        $this->participationRepo = new CourseParticipationRepository($pdo);
        $this->moduleRepo        = new ModuleRepository($pdo);
        $this->entryRepo         = new MaterialEntryRepository($pdo);
        $this->progressRepo      = new UserProgressRepository($pdo);
    }

    public function index(): void
    {
        $q          = trim($_GET['q'] ?? '');
        $categoryId = isset($_GET['category_id']) && $_GET['category_id'] !== ''
                      ? (int) $_GET['category_id']
                      : null;
        $page       = max(1, (int) ($_GET['page'] ?? 1));
        $perPage    = 20;
        $offset     = ($page - 1) * $perPage;

        if ($categoryId) {
            $totalCourses = $this->courseRepo->countByCategory($categoryId);
            $courses      = $this->courseRepo->findPaginatedByCategory($categoryId, $perPage, $offset);
        } elseif ($q !== '') {
            $totalCourses = $this->courseRepo->countBySearch($q);
            $courses      = $this->courseRepo->searchPaginated($q, $perPage, $offset);
        } else {
            $totalCourses = $this->courseRepo->countAll();
            $courses      = $this->courseRepo->findPaginated($perPage, $offset);
        }

        $totalPages = (int) ceil($totalCourses / $perPage);
        $participantCounts = [];

        foreach ($courses as $c) {
            $participantCounts[$c->getId()] =
                $this->participationRepo->countActiveByCourse($c->getId());
        }

        $categories = $this->categoryRepo->findAll();

        echo $this->twig->render('public/courses/index.twig', [
            'courses'           => $courses,
            'q'                 => $q,
            'categories'        => $categories,
            'selectedCategory'  => $categoryId,
            'participantCounts' => $participantCounts,
            'currentPage'       => $page,
            'totalPages'        => $totalPages,
        ]);
    }

    public function show($id): void
    {
        $courseId = (int) $id;
        $course = $this->courseRepo->findById($courseId);
        if (! $course) {
            http_response_code(404);
            echo $this->twig->render('errors/404.twig');
            return;
        }

        $modules      = $this->moduleRepo->findByCourse($courseId);
        $modulesCount = count($modules);
        $category     = $course->getCategoryId()
                        ? $this->categoryRepo->findById($course->getCategoryId())
                        : null;

        $userId        = $_SESSION['user']['id'] ?? null;
        $rawComments   = $this->commentRepo->findByCourseId($courseId);
        $comments      = [];
        foreach ($rawComments as $comment) {
            $user = $this->userRepo->findById($comment->getUserId());
            $comments[] = [
                'id' => $comment->getId(),
                'text' => $comment->getComment(), // Corrigir para usar o método correto
                'rating' => $comment->getRating(),
                'createdAt' => $comment->getCreatedAt(),
                'user' => $user ? ['id' => $user->getId(), 'name' => $user->getName()] : null,
            ];
        }
        $isParticipating = $userId
            ? $this->participationRepo->isParticipating($userId, $courseId)
            : false;
        $participantCount = $this->participationRepo->countActiveByCourse($courseId);

        echo $this->twig->render('public/courses/show.twig', [
            'course'           => $course,
            'category'         => $category,
            'comments'         => $comments,
            'isParticipating'  => $isParticipating,
            'participantCount' => $participantCount,
            'modules'          => $modules,
            'modulesCount'     => $modulesCount,
        ]);
    }

    public function join($id): void
    {
        AuthMiddleware::handle();
        $userId = (int) $_SESSION['user']['id'];
        $courseId = (int) $id; // Garantir que $id seja um inteiro
        $this->participationRepo->joinCourse($userId, $courseId);
        header("Location: /courses/{$courseId}");
        exit;
    }

    public function leave($id): void
    {
        AuthMiddleware::handle();
        $userId = (int) $_SESSION['user']['id'];
        $courseId = (int) $id; // Garantir que $id seja um inteiro
        $this->participationRepo->leaveCourse($userId, $courseId);
        header("Location: /courses/{$courseId}");
        exit;
    }

    public function storeComment($id): void
    {
        if (empty($_SESSION['user']['id'])) {
            http_response_code(403);
            echo "Você precisa estar logado para comentar.";
            exit;
        }

        $courseId = (int) $id; // Garantir que $id seja um inteiro
        $commentText = trim($_POST['comment'] ?? '');
        $rating      = (int) ($_POST['rating'] ?? 0);

        if ($commentText === '' || $rating < 1 || $rating > 5) {
            $_SESSION['flash']['error'][] = "Comentário e avaliação (1–5) são obrigatórios.";
            header("Location: /courses/{$courseId}");
            exit;
        }

        $comment = new CourseComment($courseId, (int) $_SESSION['user']['id'], $commentText, $rating);
        $this->commentRepo->save($comment);
        $_SESSION['flash']['success'][] = "Comentário adicionado!";
        header("Location: /courses/{$courseId}#comments");
        exit;
    }

    public function deleteComment(int $courseId, int $commentId): void
    {
        AuthMiddleware::handle();
        $comment = $this->commentRepo->findById($commentId);
        if (!$comment || $comment->getCourseId() !== $courseId) {
            http_response_code(404);
            echo $this->twig->render('errors/404.twig');
            return;
        }
        if ($comment->getUserId() !== $_SESSION['user']['id']) {
            http_response_code(403);
            echo "Permissão negada.";
            return;
        }

        $this->commentRepo->delete($commentId);
        $_SESSION['flash']['success'][] = "Comentário excluído.";
        header("Location: /courses/{$courseId}#comments");
        exit;
    }

    public function modules(int $courseId): void
    {
        $course = $this->courseRepo->findById($courseId);
        if (! $course) {
            http_response_code(404);
            echo $this->twig->render('errors/404.twig');
            return;
        }

        $modules = $this->moduleRepo->findByCourse($courseId);
        $materials = [];
        foreach ($modules as $module) {
            $materials[$module->getId()] = $this->entryRepo->findByMaterialId($module->getId());
        }

        $completedMaterials = [];
        $userId = $_SESSION['user']['id'] ?? null;
        if ($userId) {
            $completedMaterials = $this->progressRepo->findByUserId((int)$userId);
            $completedMaterials = array_map(fn(UserProgress $p) => $p->getMaterialId(), $completedMaterials);
        }

        echo $this->twig->render('public/courses/modules.twig', [
            'course'             => $course,
            'modules'            => $modules,
            'materials'          => $materials,
            'completedMaterials' => $completedMaterials,
        ]);
    }

    public function viewMaterial(int $courseId, int $moduleId, int $entryId): void
    {
        // ... código de geração de legenda permanece ...
        $allEntries = $this->entryRepo->findByMaterialId($moduleId);
        $nextEntry  = null;
        foreach ($allEntries as $e) {
            if ($e->getId() > $entryId) {
                $nextEntry = $e;
                break;
            }
        }

        $userId   = $_SESSION['user']['id'] ?? null;
        $completed = $userId
            ? array_map(fn(UserProgress $p) => $p->getMaterialId(), $this->progressRepo->findByUserId((int)$userId))
            : [];
        $total    = count($allEntries);
        $done     = count(array_intersect($completed, array_map(fn($e) => $e->getId(), $allEntries)));
        $progress = $total ? (int)round($done / $total * 100) : 0;

        echo $this->twig->render('public/courses/material.twig', [
            'course'             => $this->courseRepo->findById($courseId),
            'module'             => $this->moduleRepo->findById($moduleId),
            'entry'              => $this->entryRepo->findById($entryId),
            'nextEntry'          => $nextEntry,
            'completedMaterials' => $completed,
            'moduleProgress'     => $progress,
        ]);
    }

    public function complete(int $courseId): void
    {
        AuthMiddleware::handle();
        $userId = (int)($_SESSION['user']['id'] ?? 0);
        $data = json_decode(file_get_contents('php://input'), true);
        $entryId = $data['entryId'] ?? null;
        if ($userId && $entryId) {
            $progress = new UserProgress($userId, (int)$entryId, date('Y-m-d H:i:s'));
            $this->progressRepo->save($progress);
            http_response_code(200);
            echo json_encode(['status' => 'ok']);
        } else {
            http_response_code(400);
            echo json_encode(['status' => 'error']);
        }
        exit;
    }

    public function uncomplete(int $courseId): void
    {
        AuthMiddleware::handle();
        $userId = (int)($_SESSION['user']['id'] ?? 0);
        $data = json_decode(file_get_contents('php://input'), true);
        $entryId = $data['entryId'] ?? null;
        if ($userId && $entryId) {
            $this->progressRepo->delete($userId, (int)$entryId);
            http_response_code(200);
            echo json_encode(['status' => 'ok']);
        } else {
            http_response_code(400);
            echo json_encode(['status' => 'error']);
        }
        exit;
    }

    public function myCourses(): void
    {
        AuthMiddleware::handle();
        $userId = (int) $_SESSION['user']['id'];

        $courses = $this->participationRepo->findCoursesByUser($userId);

        echo $this->twig->render('public/courses/my-courses.twig', [
            'courses' => $courses,
        ]);
    }
}
