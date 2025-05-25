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
                'text' => $comment->getComment(),
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
        $courseId = (int) $id;
        $this->participationRepo->joinCourse($userId, $courseId);
        header("Location: /courses/{$courseId}");
        exit;
    }

    public function leave($id): void
    {
        AuthMiddleware::handle();
        $userId = (int) $_SESSION['user']['id'];
        $courseId = (int) $id;
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

        $courseId = (int) $id;
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
        // Verificar se o curso existe
        $course = $this->courseRepo->findById($courseId);
        if (!$course) {
            http_response_code(404);
            echo $this->twig->render('errors/404.twig');
            return;
        }

        // Verificar se o módulo existe e pertence ao curso
        $module = $this->moduleRepo->findById($moduleId);
        if (!$module || $module->getCourseId() !== $courseId) {
            http_response_code(404);
            echo $this->twig->render('errors/404.twig');
            return;
        }

        // Verificar se a entrada existe e pertence ao módulo
        $entry = $this->entryRepo->findById($entryId);
        if (!$entry || $entry->getMaterialId() !== $moduleId) {
            http_response_code(404);
            echo $this->twig->render('errors/404.twig');
            return;
        }

        // Buscar todas as entradas do módulo para navegação
        $allEntries = $this->entryRepo->findByMaterialId($moduleId);
        $nextEntry = null;
        $previousEntry = null;
        
        for ($i = 0; $i < count($allEntries); $i++) {
            if ($allEntries[$i]->getId() === $entryId) {
                if (isset($allEntries[$i + 1])) {
                    $nextEntry = $allEntries[$i + 1];
                }
                if (isset($allEntries[$i - 1])) {
                    $previousEntry = $allEntries[$i - 1];
                }
                break;
            }
        }

        // Calcular progresso do usuário
        $userId = $_SESSION['user']['id'] ?? null;
        $completed = [];
        $progress = 0;
        
        if ($userId) {
            $userProgress = $this->progressRepo->findByUserId((int)$userId);
            $completed = array_map(fn(UserProgress $p) => $p->getMaterialId(), $userProgress);
            $total = count($allEntries);
            $done = count(array_intersect($completed, array_map(fn($e) => $e->getId(), $allEntries)));
            $progress = $total ? (int)round($done / $total * 100) : 0;
        }

        // Preparar dados do material para o template
        $materialData = [
            'course' => $course,
            'module' => $module,
            'entry' => $entry,
            'nextEntry' => $nextEntry,
            'previousEntry' => $previousEntry,
            'completedMaterials' => $completed,
            'moduleProgress' => $progress,
            'totalMaterials' => count($allEntries),
            'currentPosition' => array_search($entry, $allEntries) + 1,
        ];

        // Adicionar headers específicos para PDFs
        if ($entry->getContentType() === 'pdf') {
            // Headers para melhor suporte a PDFs
            header('X-Frame-Options: SAMEORIGIN');
            header('Content-Security-Policy: frame-src \'self\' data:');
            
            // Verificar se o arquivo PDF existe e é acessível
            $pdfUrl = $entry->getContentUrl();
            if ($this->isPdfAccessible($pdfUrl)) {
                $materialData['pdfAccessible'] = true;
            } else {
                $materialData['pdfAccessible'] = false;
                $_SESSION['flash']['warning'][] = "O arquivo PDF pode não estar disponível no momento.";
            }
        }

        echo $this->twig->render('public/courses/material.twig', $materialData);
    }

    /**
     * Verifica se um PDF é acessível
     */
    private function isPdfAccessible(string $url): bool {
        try {
            error_log("[PDF-CHECK] Verificando acessibilidade do PDF: " . $url);
            
            if (strpos($url, '/') === 0) {
                $filePath = $_SERVER['DOCUMENT_ROOT'] . $url;
                error_log("[PDF-CHECK] Caminho local do arquivo: " . $filePath);
                
                if (!file_exists($filePath)) {
                    error_log("[PDF-ERROR] Arquivo não existe: " . $filePath);
                    return false;
                }
                
                if (!is_readable($filePath)) {
                    error_log("[PDF-ERROR] Arquivo não pode ser lido: " . $filePath);
                    return false;
                }
                
                $fileSize = filesize($filePath);
                error_log("[PDF-INFO] Tamanho do arquivo: " . ($fileSize / 1024 / 1024) . "MB");
                
                // Verifica se é realmente um PDF
                $finfo = finfo_open(FILEINFO_MIME_TYPE);
                $mimeType = finfo_file($finfo, $filePath);
                finfo_close($finfo);
                
                error_log("[PDF-INFO] Mime Type: " . $mimeType);
                
                if ($mimeType !== 'application/pdf') {
                    error_log("[PDF-ERROR] Arquivo não é um PDF válido");
                    return false;
                }
                
                return true;
            }
            
            // Para URLs externas
            $headers = get_headers($url, 1);
            error_log("[PDF-CHECK] Headers da URL externa: " . json_encode($headers));
            
            if ($headers === false) {
                error_log("[PDF-ERROR] Não foi possível obter headers da URL");
                return false;
            }
            
            $statusCode = substr($headers[0], 9, 3);
            error_log("[PDF-INFO] Status code: " . $statusCode);
            
            if (isset($headers['Content-Type'])) {
                error_log("[PDF-INFO] Content-Type: " . $headers['Content-Type']);
            }
            
            return $statusCode >= 200 && $statusCode < 400;
            
        } catch (\Exception $e) {
            error_log("[PDF-ERROR] Exceção ao verificar PDF: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Endpoint para servir PDFs com headers apropriados
     */
    public function servePdf(int $courseId, int $moduleId, int $entryId): void {
        try {
            // Registrar informações detalhadas de debug sobre o caminho
            error_log("[PDF-DEBUG] Request URI: " . $_SERVER['REQUEST_URI']);
            error_log("[PDF-DEBUG] servePdf chamado com: courseId=$courseId, moduleId=$moduleId, entryId=$entryId");
            
            $entry = $this->entryRepo->findById($entryId);
            
            if (!$entry) {
                error_log("[PDF-ERROR] Entry não encontrada: " . $entryId);
                http_response_code(404);
                echo json_encode(['error' => 'PDF não encontrado']);
                exit;
            }
            
            if ($entry->getContentType() !== 'pdf') {
                error_log("[PDF-ERROR] Tipo de conteúdo inválido: " . $entry->getContentType());
                http_response_code(400);
                echo json_encode(['error' => 'Tipo de arquivo inválido']);
                exit;
            }
            
            $pdfPath = $entry->getContentUrl();
            error_log("[PDF-INFO] URL do PDF: " . $pdfPath);
            
            // Caminho local no servidor
            if (strpos($pdfPath, '/') === 0) {
                $fullPath = $_SERVER['DOCUMENT_ROOT'] . $pdfPath;
                error_log("[PDF-INFO] Caminho completo: " . $fullPath);
                
                if (!file_exists($fullPath)) {
                    error_log("[PDF-ERROR] Arquivo não encontrado: " . $fullPath);
                    http_response_code(404);
                    echo json_encode(['error' => 'Arquivo não encontrado no servidor']);
                    exit;
                }
                
                $fileSize = filesize($fullPath);
                error_log("[PDF-INFO] Tamanho do arquivo: " . ($fileSize / 1024 / 1024) . "MB");
                
                // Headers simplificados para máxima compatibilidade
                header('Content-Type: application/pdf');
                header('Content-Disposition: inline; filename="' . basename($fullPath) . '"');
                header('Content-Length: ' . $fileSize);
                header('Cache-Control: public, max-age=86400');
                header('Expires: ' . gmdate('D, d M Y H:i:s', time() + 86400) . ' GMT');
                header('Last-Modified: ' . gmdate('D, d M Y H:i:s', filemtime($fullPath)) . ' GMT');
                
                if (ob_get_level()) ob_end_clean(); // Limpa output buffers
                readfile($fullPath); // Mais simples e confiável
                exit;
            }
            
            // URL externa - redirecionar diretamente
            error_log("[PDF-INFO] Redirecionando para URL externa: " . $pdfPath);
            header("Location: $pdfPath");
            exit;
            
        } catch (\Exception $e) {
            error_log("[PDF-ERROR] Erro fatal ao servir PDF: " . $e->getMessage() . "\n" . $e->getTraceAsString());
            http_response_code(500);
            echo json_encode(['error' => 'Erro interno ao tentar exibir o PDF']);
            exit;
        }
    }

    public function complete(int $courseId): void
    {
        AuthMiddleware::handle();
        $userId = (int)($_SESSION['user']['id'] ?? 0);
        $data = json_decode(file_get_contents('php://input'), true);
        $entryId = $data['entryId'] ?? null;
        
        if ($userId && $entryId) {
            // Verificar se o progresso já existe para evitar duplicatas
            $existingProgress = $this->progressRepo->findByUserAndMaterial($userId, (int)$entryId);
            
            if (!$existingProgress) {
                $progress = new UserProgress($userId, (int)$entryId, date('Y-m-d H:i:s'));
                $this->progressRepo->save($progress);
            }
            
            http_response_code(200);
            echo json_encode(['status' => 'ok', 'message' => 'Material marcado como concluído']);
        } else {
            http_response_code(400);
            echo json_encode(['status' => 'error', 'message' => 'Dados inválidos']);
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
            echo json_encode(['status' => 'ok', 'message' => 'Material desmarcado como concluído']);
        } else {
            http_response_code(400);
            echo json_encode(['status' => 'error', 'message' => 'Dados inválidos']);
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