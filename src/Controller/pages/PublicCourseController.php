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
use App\Models\CourseComment;
use PDO;
use Twig\Environment;

class PublicCourseController
{
    private Environment $twig;
    private PDO $pdo;

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
        $this->twig              = $twig;
        $this->pdo               = $pdo;
        $this->courseRepo        = new CourseRepository($pdo);
        $this->categoryRepo      = new CategoryRepository($pdo);
        $this->commentRepo       = new CourseCommentRepository($pdo);
        $this->userRepo          = new UserRepository($pdo);
        $this->participationRepo = new CourseParticipationRepository($pdo);
        $this->moduleRepo        = new ModuleRepository($pdo);
        $this->entryRepo         = new MaterialEntryRepository($pdo);
        $this->progressRepo      = new UserProgressRepository($pdo);
    }

    /**
     * 1) Lista todos os cursos públicos, com filtros e contagem de participantes.
     */
    public function index(): void
    {
        $q          = trim($_GET['q'] ?? '');
        $categoryId = isset($_GET['category_id']) && $_GET['category_id'] !== ''
                      ? (int) $_GET['category_id']
                      : null;
        $page       = max(1, (int)($_GET['page'] ?? 1));
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

    /**
     * 2) Detalhes do curso: categoria, comentários e participação.
     */
    public function show($id): void
{
    $courseId = (int) $id;
    $course   = $this->courseRepo->findById($courseId);

    if (! $course) {
        http_response_code(404);
        echo $this->twig->render('errors/404.twig');
        return;
    }

    // --- NOVO: buscar módulos e contar ---
    $modules     = $this->moduleRepo->findByCourse($courseId);
    $modulesCount = count($modules);

    $category = null;
    if ($cid = $course->getCategoryId()) {
        $category = $this->categoryRepo->findById($cid);
    }

    $userId = $_SESSION['user']['id'] ?? null;

    // restante do código…
    $rawComments     = $this->commentRepo->findByCourseId($courseId);
    $comments = [];
    foreach ($rawComments as $comment) {
        $user = $this->userRepo->findById($comment->getUserId());
        $comments[] = [
            'comment' => $comment,
            'user'    => $user,
        ];
    }
    $isParticipating = $userId
        ? $this->participationRepo->isParticipating($userId, $courseId)
        : false;

    $participantCount = $this->participationRepo->countActiveByCourse($courseId);

    echo $this->twig->render('public/courses/show.twig', [
        'course'            => $course,
        'category'          => $category,
        'comments'          => $comments,
        'isParticipating'   => $isParticipating,
        'participantCount'  => $participantCount,

        // --- passar os módulos e a contagem ---
        'modules'           => $modules,
        'modulesCount'      => $modulesCount,
    ]);
}


    /**
     * 3) Entrar no curso.
     */
    public function join($id): void
    {
        AuthMiddleware::handle();
        $courseId = (int) $id;
        $userId   = (int) $_SESSION['user']['id'];

        if (! $this->courseRepo->findById($courseId)) {
            http_response_code(404);
            echo "Curso não encontrado.";
            exit;
        }

        $this->participationRepo->joinCourse($userId, $courseId);
        header("Location: /courses/{$courseId}");
        exit;
    }

    /**
     * 4) Sair do curso.
     */
    public function leave($id): void
    {
        AuthMiddleware::handle();
        $courseId = (int) $id;
        $userId   = (int) $_SESSION['user']['id'];

        $this->participationRepo->leaveCourse($userId, $courseId);
        header("Location: /courses/{$courseId}");
        exit;
    }

    /**
     * 7) Salvar comentário.
     */
    public function storeComment($id): void
    {
        $courseId = (int) $id;
        if (empty($_SESSION['user']['id'])) {
            http_response_code(403);
            echo "Você precisa estar logado para comentar.";
            exit;
        }

        $userId = (int) $_SESSION['user']['id'];
        $text   = trim($_POST['comment'] ?? '');
        $rating = (int) ($_POST['rating'] ?? 0);

        if ($text === '' || $rating < 1 || $rating > 5) {
            $_SESSION['flash']['error'][] = "Comentário e avaliação (1–5) são obrigatórios.";
            header("Location: /courses/{$courseId}");
            exit;
        }

        $comment = new CourseComment($courseId, $userId, $text, $rating);
        $this->commentRepo->save($comment);
        $_SESSION['flash']['success'][] = "Comentário adicionado!";
        header("Location: /courses/{$courseId}#comments");
        exit;
    }

    /**
     * 8) Excluir comentário.
     */
    public function deleteComment($courseId, $commentId): void
    {
        AuthMiddleware::handle();
        $userId    = (int) $_SESSION['user']['id'];
        $courseId  = (int) $courseId;
        $commentId = (int) $commentId;

        $comment = $this->commentRepo->findById($commentId);
        if (!$comment || $comment->getCourseId() !== $courseId) {
            http_response_code(404);
            echo $this->twig->render('errors/404.twig');
            return;
        }

        if ($comment->getUserId() !== $userId) {
            http_response_code(403);
            echo "Você não tem permissão para excluir este comentário.";
            return;
        }

        $this->commentRepo->delete($commentId);
        $_SESSION['flash']['success'][] = "Comentário excluído.";
        header("Location: /courses/{$courseId}#comments");
        exit;
    }

    /**
     * 9) Listar módulos e materiais de um curso público.
     */
    public function modules(int $courseId): void
    {
        $course = $this->courseRepo->findById($courseId);
        if (! $course) {
            http_response_code(404);
            echo $this->twig->render('errors/404.twig');
            return;
        }

        $modules   = $this->moduleRepo->findByCourse($courseId);
        $materials = [];
        foreach ($modules as $mod) {
            $materials[$mod->getId()] = $this->entryRepo->findByMaterialId($mod->getId());
        }

        // IDs de materiais concluídos pelo usuário
        $userId              = $_SESSION['user']['id'] ?? null;
        $completedMaterials  = $userId
            ? $this->participationRepo->getCompletedIdsByUser((int)$userId, $courseId)
            : [];

        echo $this->twig->render('public/courses/modules.twig', [
            'course'             => $course,
            'modules'            => $modules,
            'materials'          => $materials,
            'completedMaterials' => $completedMaterials,
        ]);
    }

    /**
     * 10) Visualizar um material específico de um módulo público.
     */
    public function viewMaterial(int $courseId, int $moduleId, int $entryId): void
    {
        $course = $this->courseRepo->findById($courseId);
        if (! $course) {
            http_response_code(404);
            echo $this->twig->render('errors/404.twig');
            return;
        }

        $module = $this->moduleRepo->findById($moduleId);
        if (! $module || $module->getCourseId() !== $courseId) {
            http_response_code(404);
            echo $this->twig->render('errors/404.twig');
            return;
        }

        $entry = $this->entryRepo->findById($entryId);
        if (! $entry || $entry->getMaterialId() !== $moduleId) {
            http_response_code(404);
            echo $this->twig->render('errors/404.twig');
            return;
        }

        // Próximo material
        $allEntries = $this->entryRepo->findByMaterialId($moduleId);
        $nextEntry  = null;
        foreach ($allEntries as $e) {
            if ($e->getId() > $entry->getId()) {
                $nextEntry = $e;
                break;
            }
        }

        // Progresso do módulo
        $userId             = $_SESSION['user']['id'] ?? null;
        $completedMaterials = $userId
            ? $this->participationRepo->getCompletedIdsByUser((int)$userId, $courseId)
            : [];

        $totalMaterials = $this->entryRepo->countByMaterialId($moduleId);
        $doneMaterials  = 0;
        foreach ($allEntries as $e) {
            if (in_array($e->getId(), $completedMaterials, true)) {
                $doneMaterials++;
            }
        }
        $moduleProgress = $totalMaterials
            ? (int) round($doneMaterials / $totalMaterials * 100)
            : 0;

        echo $this->twig->render('public/courses/material.twig', [
            'course'             => $course,
            'module'             => $module,
            'entry'              => $entry,
            'nextEntry'          => $nextEntry,
            'completedMaterials' => $completedMaterials,
            'moduleProgress'     => $moduleProgress,
        ]);
    }
}
