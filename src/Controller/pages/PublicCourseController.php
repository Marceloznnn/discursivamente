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

        // Diretório raiz do projeto (onde está transcribe.py e pasta public)
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

    /**
     * Lista todos os cursos públicos, com filtros e paginação.
     */
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

    /**     * Exibe os detalhes de um curso, incluindo comentários e participação.
     * 
     * @param string|int $id ID do curso
     */
    public function show($id): void
    {
        $courseId = (int) $id;
        $course = $this->courseRepo->findById($courseId);
        if (! $course) {
            http_response_code(404);
            echo $this->twig->render('errors/404.twig');
            return;
        }

        $modules      = $this->moduleRepo->findByCourse($id);
        $modulesCount = count($modules);
        $category     = $course->getCategoryId()
                        ? $this->categoryRepo->findById($course->getCategoryId())
                        : null;

        $userId        = $_SESSION['user']['id'] ?? null;
        $rawComments   = $this->commentRepo->findByCourseId($id);
        $comments      = [];
        foreach ($rawComments as $comment) {
            $comments[] = [
                'comment' => $comment,
                'user'    => $this->userRepo->findById($comment->getUserId()),
            ];
        }
        $isParticipating = $userId
            ? $this->participationRepo->isParticipating($userId, $id)
            : false;
        $participantCount = $this->participationRepo->countActiveByCourse($id);

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

    

    /**
     * Inscreve o usuário no curso.
     */
    public function join(int $id): void
    {
        AuthMiddleware::handle();
        $userId = (int) $_SESSION['user']['id'];
        $this->participationRepo->joinCourse($userId, $id);
        header("Location: /courses/{$id}");
        exit;
    }

    /**
     * Desinscreve o usuário do curso.
     */
    public function leave(int $id): void
    {
        AuthMiddleware::handle();
        $userId = (int) $_SESSION['user']['id'];
        $this->participationRepo->leaveCourse($userId, $id);
        header("Location: /courses/{$id}");
        exit;
    }

    /**
     * Salva um comentário de curso.
     */
    public function storeComment(int $id): void
    {
        if (empty($_SESSION['user']['id'])) {
            http_response_code(403);
            echo "Você precisa estar logado para comentar.";
            exit;
        }

        $commentText = trim($_POST['comment'] ?? '');
        $rating      = (int) ($_POST['rating'] ?? 0);

        if ($commentText === '' || $rating < 1 || $rating > 5) {
            $_SESSION['flash']['error'][] = "Comentário e avaliação (1–5) são obrigatórios.";
            header("Location: /courses/{$id}");
            exit;
        }

        $comment = new CourseComment($id, (int) $_SESSION['user']['id'], $commentText, $rating);
        $this->commentRepo->save($comment);
        $_SESSION['flash']['success'][] = "Comentário adicionado!";
        header("Location: /courses/{$id}#comments");
        exit;
    }

    /**
     * Exclui um comentário.
     */
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

    /**
     * Lista módulos e materiais do curso.
     */
    public function modules(int $courseId): void
    {
        $course = $this->courseRepo->findById($courseId);
        if (! $course) {
            http_response_code(404);
            echo $this->twig->render('errors/404.twig');
            return;
        }        $modules = $this->moduleRepo->findByCourse($courseId);
        
        // Busca os materiais para cada módulo 
        $materials = [];
        foreach ($modules as $module) {
            $materials[$module->getId()] = $this->entryRepo->findByMaterialId($module->getId());
        }
        
        $completedMaterials = [];
        $userId = $_SESSION['user']['id'] ?? null;
        if ($userId) {
            $completedMaterials = $this->participationRepo
                ->getCompletedIdsByUser((int)$userId, $courseId);
        }

        echo $this->twig->render('public/courses/modules.twig', [
            'course' => $course,
            'modules' => $modules,
            'materials' => $materials,
            'completedMaterials' => $completedMaterials,
        ]);
    }

    /**
     * Exibe um material específico, gerando legenda se for vídeo.
     */
    public function viewMaterial(int $courseId, int $moduleId, int $entryId): void
    {
        $course = $this->courseRepo->findById($courseId);
        if (!$course || !($module = $this->moduleRepo->findById($moduleId)) 
            || $module->getCourseId() !== $courseId
        ) {
            http_response_code(404);
            echo $this->twig->render('errors/404.twig');
            return;
        }

        $entry = $this->entryRepo->findById($entryId);
        if (!$entry || $entry->getMaterialId() !== $moduleId) {
            http_response_code(404);
            echo $this->twig->render('errors/404.twig');
            return;
        }        // Tenta gerar legenda para vídeo
        if ($entry->getContentType() === 'video') {
            if ($entry->getSubtitleUrl()) {
                error_log("[Material] Entrada {$entryId} já possui legenda: {$entry->getSubtitleUrl()}");
            } else {
                error_log("[Material] Tentando gerar legenda para entrada {$entryId}");
                error_log("[Material] - URL do vídeo: {$entry->getContentUrl()}");
                
                try {
                    $subtitleUrl = $this->transcriptionService
                        ->generateSubtitleFromUrl($entry->getContentUrl());
                    
                    error_log("[Material] Legenda gerada com sucesso: {$subtitleUrl}");
                    error_log("[Material] Atualizando banco de dados...");
                    
                    $this->entryRepo->updateSubtitle($entryId, $subtitleUrl);
                    $entry->setSubtitleUrl($subtitleUrl);
                    
                    error_log("[Material] Banco de dados atualizado");
                } catch (\Throwable $e) {
                    error_log("[Material] ERRO ao gerar legenda para entrada {$entryId}:");
                    error_log("[Material] - Mensagem: " . $e->getMessage());
                    error_log("[Material] - Arquivo: " . $e->getFile() . ":" . $e->getLine());
                    error_log("[Material] - Stack trace:\n" . $e->getTraceAsString());
                }
            }
        }

        // Próximo e progresso
        $allEntries = $this->entryRepo->findByMaterialId($moduleId);
        $nextEntry  = null;
        foreach ($allEntries as $e) {
            if ($e->getId() > $entryId) {
                $nextEntry = $e;
                break;
            }
        }

        $userId    = $_SESSION['user']['id'] ?? null;
        $completed = $userId
            ? $this->participationRepo->getCompletedIdsByUser((int)$userId, $courseId)
            : [];
        $total     = count($allEntries);
        $done      = count(array_intersect($completed, array_map(fn($e) => $e->getId(), $allEntries)));
        $progress  = $total ? (int)round($done / $total * 100) : 0;

        echo $this->twig->render('public/courses/material.twig', [
            'course'             => $course,
            'module'             => $module,
            'entry'              => $entry,
            'nextEntry'          => $nextEntry,
            'completedMaterials' => $completed,
            'moduleProgress'     => $progress,
        ]);
    }
}
