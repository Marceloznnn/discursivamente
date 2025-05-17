<?php

namespace Controller\pages;

use Middleware\AuthMiddleware;
use Repositories\CourseRepository;
use Repositories\CategoryRepository;
use Repositories\MaterialRepository;
use Repositories\UserProgressRepository;
use Repositories\CourseCommentRepository;
use Repositories\UserRepository;
use Repositories\CourseParticipationRepository;
use App\Models\CourseComment;
use PDO;
use Twig\Environment;

class PublicCourseController
{
    private Environment $twig;
    private PDO $pdo;
    private CourseRepository $courseRepo;
    private CategoryRepository $categoryRepo;
    private MaterialRepository $materialRepo;
    private UserProgressRepository $progressRepo;
    private CourseCommentRepository $commentRepo;
    private UserRepository $userRepo;
    private CourseParticipationRepository $participationRepo;

    public function __construct(Environment $twig, PDO $pdo)
    {
        $this->twig              = $twig;
        $this->pdo               = $pdo;
        $this->courseRepo        = new CourseRepository($pdo);
        $this->categoryRepo      = new CategoryRepository($pdo);
        $this->materialRepo      = new MaterialRepository($pdo);
        $this->progressRepo      = new UserProgressRepository($pdo);
        $this->commentRepo       = new CourseCommentRepository($pdo);
        $this->userRepo          = new UserRepository($pdo);
        $this->participationRepo = new CourseParticipationRepository($pdo);
    }

    /** 1) Lista todos os cursos públicos, permite busca por texto ou por categoria, e contagem de participantes. */
    public function index(): void
    {
        $q           = trim($_GET['q'] ?? '');
        $categoryId  = isset($_GET['category_id']) && $_GET['category_id'] !== ''
                       ? (int) $_GET['category_id']
                       : null;

        if ($categoryId) {
            // Filtra por categoria
            $courses = $this->courseRepo->findByCategoryId($categoryId);
        } elseif ($q !== '') {
            // Busca por texto
            $courses = $this->courseRepo->search($q);
        } else {
            // Tudo
            $courses = $this->courseRepo->findAll();
        }

        // Conta participantes de cada curso
        $participantCounts = [];
        foreach ($courses as $c) {
            $participantCounts[$c->getId()] =
                $this->participationRepo->countActiveByCourse($c->getId());
        }

        // Lista de todas as categorias (para filtro no template)
        $categories = $this->categoryRepo->findAll();

        echo $this->twig->render('public/courses/index.twig', [
            'courses'           => $courses,
            'q'                 => $q,
            'categories'        => $categories,
            'selectedCategory'  => $categoryId,
            'participantCounts' => $participantCounts,
        ]);
    }

    /** 2) Mostra detalhes do curso, materiais, progresso, comentários, join/leave e categoria. */
    public function show($id): void
    {
        $courseId = (int)$id;
        $course   = $this->courseRepo->findById($courseId);

        if (! $course) {
            http_response_code(404);
            echo $this->twig->render('errors/404.twig');
            return;
        }

        // Busca a categoria para exibir
        $category = null;
        if ($course->getCategoryId()) {
            $category = $this->categoryRepo->findById($course->getCategoryId());
        }

        // Materiais e progresso
        $materials = $this->materialRepo->findByCourseId($courseId);
        $userId    = $_SESSION['user']['id'] ?? null;
        $completed = [];
        if ($userId) {
            $prs = $this->progressRepo->findByUserId((int)$userId);
            $completed = array_map(fn($p) => $p->getMaterialId(), $prs);
        }
        $total    = count($materials);
        $progress = $total ? (int) round(count(array_intersect(array_map(fn($m) => $m->getId(), $materials), $completed)) / $total * 100) : 0;

        // Comentários
        $rawComments = $this->commentRepo->findByCourseId($courseId);
        $comments = array_map(function(CourseComment $c) {
            $user = $this->userRepo->findById($c->getUserId());
            return [
                'id'        => $c->getId(),
                'userId'    => $c->getUserId(),
                'userName'  => $user ? $user->getName() : "Usuário #{$c->getUserId()}",
                'comment'   => $c->getComment(),
                'rating'    => $c->getRating(),
                'createdAt' => $c->getCreatedAt()
            ];
        }, $rawComments);

        // Participação
        $isParticipating  = $userId
            ? $this->participationRepo->isParticipating((int)$userId, $courseId)
            : false;
        $participantCount = $this->participationRepo->countActiveByCourse($courseId);

        echo $this->twig->render('public/courses/show.twig', [
            'course'             => $course,
            'category'           => $category,
            'materials'          => $materials,
            'completedMaterials' => $completed,
            'progress'           => $progress,
            'comments'           => $comments,
            'isParticipating'    => $isParticipating,
            'participantCount'   => $participantCount,
        ]);
    }

    /** 3) Entrar no curso (join). */
    public function join($id): void
    {
        AuthMiddleware::handle();
        $courseId = (int)$id;
        $userId   = (int)$_SESSION['user']['id'];

        if (! $this->courseRepo->findById($courseId)) {
            http_response_code(404);
            echo "Curso não encontrado.";
            exit;
        }

        $this->participationRepo->joinCourse($userId, $courseId);
        header("Location: /courses/{$courseId}/materials");
        exit;
    }

    /** 4) Sair do curso (leave). */
    public function leave($id): void
    {
        AuthMiddleware::handle();
        $courseId = (int)$id;
        $userId   = (int)$_SESSION['user']['id'];

        $this->participationRepo->leaveCourse($userId, $courseId);
        header("Location: /courses/{$courseId}");
        exit;
    }

    /** 5) Lista materiais (após participação). */
    public function listMaterials($id): void
    {
        $courseId = (int)$id;
        $course   = $this->courseRepo->findById($courseId);
        if (! $course) {
            http_response_code(404);
            echo $this->twig->render('errors/404.twig');
            return;
        }

        $materials = $this->materialRepo->findByCourseId($courseId);
        echo $this->twig->render('public/courses/materials.twig', [
            'course'    => $course,
            'materials' => $materials,
        ]);
    }

    /** 6) Exibe conteúdo de um material específico. */
    public function showMaterial($courseId, $materialId): void
    {
        $courseId   = (int)$courseId;
        $materialId = (int)$materialId;

        $course   = $this->courseRepo->findById($courseId);
        $material = $this->materialRepo->findById($materialId);

        if (! $course || ! $material || $material->getCourseId() !== $courseId) {
            http_response_code(404);
            echo $this->twig->render('errors/404.twig');
            return;
        }

        echo $this->twig->render('public/courses/material.twig', [
            'course'   => $course,
            'material' => $material,
        ]);
    }

    /** 7) Salva comentário via POST. */
    public function storeComment($id): void
    {
        $courseId = (int)$id;
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

    /** 8) Exclui comentário (somente autor). */
    public function deleteComment($courseId, $commentId): void
    {
        AuthMiddleware::handle();
        $userId    = (int) $_SESSION['user']['id'];
        $courseId  = (int) $courseId;
        $commentId = (int) $commentId;

        $comment = $this->commentRepo->findById($commentId);
        if (! $comment || $comment->getCourseId() !== $courseId) {
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
}
