<?php

namespace Controller\pages;

use Middleware\AuthMiddleware;
use Repositories\CourseRepository;
use Repositories\UserRepository;
use Repositories\ForumTopicRepository;
use Repositories\ForumReplyRepository;
use Services\ForumMessageService;
use PDO;
use Twig\Environment;

class ForumController
{
    private Environment $twig;
    private PDO $pdo;
    private ForumMessageService $forumMessageService;
    private CourseRepository $courseRepo;
    private UserRepository $userRepo;
    private ForumTopicRepository $forumTopicRepo;
    private ForumReplyRepository $forumReplyRepo;

    public function __construct(Environment $twig, PDO $pdo)
    {
        $this->twig = $twig;
        $this->pdo = $pdo;

        $this->forumMessageService = new ForumMessageService($pdo);
        $this->courseRepo = new CourseRepository($pdo);
        $this->userRepo = new UserRepository($pdo);
        $this->forumTopicRepo = new ForumTopicRepository($pdo);
        $this->forumReplyRepo = new ForumReplyRepository($pdo);
    }

    /**
     * Exibe o fórum de um curso específico.
     */
    public function index(int $courseId): void
    {
        AuthMiddleware::handle();

        $course = $this->courseRepo->findById($courseId);

        if (!$course) {
            $this->renderNotFound();
            return;
        }

        $messages = $this->forumMessageService->getMessages($courseId, 100);
        $teacher = $this->userRepo->findById($course->getCreatorId());

        echo $this->twig->render('public/courses/forum.twig', [
            'course' => $course,
            'messages' => $messages,
            'currentUser' => $_SESSION['user'] ?? null,
            'teacher' => $teacher
        ]);
    }

    /**
     * Visualiza um tópico específico no fórum.
     */
    public function viewTopic(int $courseId, int $topicId): void
    {
        AuthMiddleware::handle();

        $course = $this->courseRepo->findById($courseId);
        if (!$course) {
            $this->renderNotFound();
            return;
        }

        $topic = $this->forumTopicRepo->findByIdAndCourse($topicId, $courseId);
        if (!$topic) {
            $this->renderNotFound();
            return;
        }

        $replies = $this->forumReplyRepo->getByTopicId($topicId);

        echo $this->twig->render('public/courses/forum/view-topic.twig', [
            'course' => $course,
            'topic' => $topic,
            'replies' => $replies,
            'currentUser' => $_SESSION['user'] ?? null,
        ]);
    }

    /**
     * Salva uma nova mensagem no fórum do curso.
     */
    public function post(int $courseId): void
    {
        AuthMiddleware::handle();

        $userId = (int) ($_SESSION['user']['id'] ?? 0);
        $message = trim($_POST['message'] ?? '');

        if ($message === '') {
            $_SESSION['flash']['error'][] = "A mensagem não pode estar vazia.";
            $this->redirectToForum($courseId);
            return;
        }

        $this->forumMessageService->saveMessage($userId, $courseId, $message);
        $_SESSION['flash']['success'][] = "Mensagem enviada!";
        $this->redirectToForum($courseId);
    }

    /**
     * Renderiza a página de erro 404.
     */
    private function renderNotFound(): void
    {
        http_response_code(404);
        echo $this->twig->render('errors/404.twig');
    }

    /**
     * Redireciona para o fórum do curso.
     */
    private function redirectToForum(int $courseId): void
    {
        header("Location: /courses/{$courseId}/forum");
        exit;
    }
}
