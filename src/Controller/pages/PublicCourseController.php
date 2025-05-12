<?php
// src/Controller/pages/PublicCourseController.php

namespace Controller\pages;

use Repositories\CourseRepository;
use Repositories\MaterialRepository;
use Repositories\UserProgressRepository;
use Repositories\CourseCommentRepository;
use Repositories\UserRepository;
use App\Models\CourseComment;
use PDO;
use Twig\Environment;

class PublicCourseController
{
    private Environment $twig;
    private PDO $pdo;
    private CourseRepository $courseRepo;
    private MaterialRepository $materialRepo;
    private UserProgressRepository $progressRepo;
    private CourseCommentRepository $commentRepo;
    private UserRepository $userRepo;

    public function __construct(Environment $twig, PDO $pdo)
    {
        $this->twig         = $twig;
        $this->pdo          = $pdo;
        $this->courseRepo   = new CourseRepository($pdo);
        $this->materialRepo = new MaterialRepository($pdo);
        $this->progressRepo = new UserProgressRepository($pdo);
        $this->commentRepo  = new CourseCommentRepository($pdo);
        $this->userRepo     = new UserRepository($pdo);
    }

    /**
     * Lista todos os cursos públicos
     */
    public function index(): void
    {
        error_log(__METHOD__ . " - Fetching all courses");
        $courses = $this->courseRepo->findAll();
        error_log(__METHOD__ . " - Found " . count($courses) . " courses");

        echo $this->twig->render('public/courses/index.twig', [
            'courses' => $courses,
        ]);
    }

    /**
     * Mostra detalhes do curso e comentários
     */
    public function show($id): void
    {
        $courseId = (int) $id;
        error_log(__METHOD__ . " - Show courseID={$courseId}");

        $course = $this->courseRepo->findById($courseId);
        if (!$course) {
            error_log(__METHOD__ . " - Course not found");
            http_response_code(404);
            echo $this->twig->render('errors/404.twig');
            return;
        }

        // Busca materiais e progresso
        $materials = $this->materialRepo->findByCourseId($courseId);
        error_log(__METHOD__ . " - Found " . count($materials) . " materials for courseID={$courseId}");

        $completed = [];
        if (!empty($_SESSION['user']['id'])) {
            $uid = (int) $_SESSION['user']['id'];
            $prs = $this->progressRepo->findByUserId($uid);
            $completed = array_map(fn($p) => $p->getMaterialId(), $prs);
            error_log(__METHOD__ . " - User {$uid} completed " . count($completed) . " materials");
        }

        $total    = count($materials);
        $progress = $total ? (int) round(count($completed) / $total * 100) : 0;
        error_log(__METHOD__ . " - Progress={$progress}%");

        // Busca comentários já com nome de usuário
        $rawComments = $this->commentRepo->findByCourseId($courseId);
        error_log(__METHOD__ . " - Found " . count($rawComments) . " comments");
        $comments = array_map(function(CourseComment $c) {
            $user = $this->userRepo->findById($c->getUserId());
            return [
                'userName'  => $user ? $user->getName() : "Usuário #{$c->getUserId()}",
                'comment'   => $c->getComment(),
                'rating'    => $c->getRating(),
                'createdAt' => $c->getCreatedAt(),
            ];
        }, $rawComments);

        echo $this->twig->render('public/courses/show.twig', [
            'course'             => $course,
            'materials'          => $materials,
            'completedMaterials' => $completed,
            'progress'           => $progress,
            'comments'           => $comments,
        ]);
    }

    /**
     * Lista todos os materiais de um curso (materials.twig)
     */
    public function listMaterials($id): void
    {
        $courseId = (int) $id;
        error_log(__METHOD__ . " - List materials for courseID={$courseId}");

        $course = $this->courseRepo->findById($courseId);
        if (!$course) {
            error_log(__METHOD__ . " - Course not found");
            http_response_code(404);
            echo $this->twig->render('errors/404.twig');
            return;
        }

        $materials = $this->materialRepo->findByCourseId($courseId);
        error_log(__METHOD__ . " - Found " . count($materials) . " materials");

        echo $this->twig->render('public/courses/materials.twig', [
            'course'    => $course,
            'materials' => $materials,
        ]);
    }

    /**
     * Exibe o conteúdo de um material específico (material.twig)
     */
    public function showMaterial($courseId, $materialId): void
    {
        $courseId   = (int) $courseId;
        $materialId = (int) $materialId;
        error_log(__METHOD__ . " - Show materialID={$materialId} for courseID={$courseId}");

        $course   = $this->courseRepo->findById($courseId);
        $material = $this->materialRepo->findById($materialId);

        if (!$course || !$material || $material->getCourseId() !== $courseId) {
            error_log(__METHOD__ . " - Course or Material not valid");
            http_response_code(404);
            echo $this->twig->render('errors/404.twig');
            return;
        }

        error_log(__METHOD__ . " - Material content=" . $material->getContent() . ", type=" . $material->getContentType());
        echo $this->twig->render('public/courses/material.twig', [
            'course'   => $course,
            'material' => $material,
        ]);
    }

    /**
     * Salva comentário enviado via POST
     */
    public function storeComment($id): void
    {
        $courseId = (int) $id;
        error_log(__METHOD__ . " - Store comment for courseID={$courseId}");

        if (empty($_SESSION['user']['id'])) {
            error_log(__METHOD__ . " - Unauthorized comment attempt");
            http_response_code(403);
            echo "Você precisa estar logado para comentar.";
            exit;
        }

        $userId = (int) $_SESSION['user']['id'];
        $text   = trim($_POST['comment'] ?? '');
        $rating = (int) ($_POST['rating'] ?? 0);
        error_log(__METHOD__ . " - Comment text length=" . strlen($text) . ", rating={$rating}");

        if ($text === '' || $rating < 1 || $rating > 5) {
            $_SESSION['flash']['error'][] = "Comentário e avaliação (1–5) são obrigatórios.";
            header("Location: /courses/{$courseId}");
            exit;
        }

        $comment = new CourseComment(
            $courseId,
            $userId,
            $text,
            $rating
        );
        $this->commentRepo->save($comment);
        $_SESSION['flash']['success'][] = "Comentário adicionado!";
        error_log(__METHOD__ . " - Comment saved");

        header("Location: /courses/{$courseId}#comments");
        exit;
    }
}
