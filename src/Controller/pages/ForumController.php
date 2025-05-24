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
            http_response_code(404);
            echo $this->twig->render('errors/404.twig');
            return;
        }        $messages = $this->forumMessageService->getMessages($courseId, 100);

        $teacher = $this->userRepo->findById($course->getCreatorId());

        echo $this->twig->render('public/courses/forum.twig', [
            'course' => $course,
            'messages' => $messages,
            'currentUser' => $_SESSION['user'] ?? null,
            'teacher' => $teacher
        ]);
    }    public function viewTopic(int $courseId, int $topicId): void
    {
        AuthMiddleware::handle();

        // Buscar o curso
        $course = $this->courseRepo->findById($courseId);
        if (!$course) {
            http_response_code(404);
            echo $this->twig->render('errors/404.twig');
            return;
        }

        // Buscar o tópico
        $topic = $this->forumTopicRepo->findByIdAndCourse($topicId, $courseId);
        if (!$topic) {
            http_response_code(404);
            echo $this->twig->render('errors/404.twig');
            return;
        }

        // Buscar as respostas do tópico
        $replies = $this->forumReplyRepo->getByTopicId($topicId);

        // Renderizar a view
        echo $this->twig->render('public/courses/forum/view-topic.twig', [
            'course' => $course,
            'topic' => $topic,
            'replies' => $replies
    ]);
}


    /**
     * Salva uma nova mensagem no fórum.
     */
    public function post(int $courseId): void
    {
        AuthMiddleware::handle();

        $userId = (int) $_SESSION['user']['id'];
        $message = trim($_POST['message'] ?? '');

        if ($message === '') {
            $_SESSION['flash']['error'][] = "A mensagem não pode estar vazia.";
            header("Location: /courses/{$courseId}/forum");
            exit;
        }

        $this->forumMessageService->saveMessage($userId, $courseId, $message);
        $_SESSION['flash']['success'][] = "Mensagem enviada!";
        header("Location: /courses/{$courseId}/forum");
        exit;
    }
}
