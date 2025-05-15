<?php
// src/Controller/pages/TeacherCourseController.php

namespace Controller\pages;

use Middleware\TeacherMiddleware;
use Repositories\CourseRepository;
use Repositories\CourseCommentRepository;
use Repositories\CourseParticipationRepository;
use Services\CloudinaryService;
use PDO;
use Twig\Environment;

class TeacherCourseController
{
    private Environment $twig;
    private PDO $pdo;
    private CourseRepository $courseRepo;
    private CourseCommentRepository $commentRepo;
    private CourseParticipationRepository $participationRepo;
    private CloudinaryService $cloudService;

    public function __construct(Environment $twig, PDO $pdo, CloudinaryService $cloudService)
    {
        TeacherMiddleware::handle();

        $this->twig              = $twig;
        $this->pdo               = $pdo;
        $this->courseRepo        = new CourseRepository($pdo);
        $this->commentRepo       = new CourseCommentRepository($pdo);
        $this->participationRepo = new CourseParticipationRepository($pdo);
        $this->cloudService      = $cloudService;
    }


    // 1) Lista todos os cursos do professor
    public function index(): void
    {
        $userId  = $_SESSION['user']['id'];
        $courses = $this->courseRepo->findByCreatorId($userId);

        echo $this->twig->render('teacher/courses/index.twig', [
            'courses' => $courses
        ]);
    }

    // 2) Exibe o formulário de criação
    public function create(): void
    {
        echo $this->twig->render('teacher/courses/create.twig');
    }

    // 3) Persiste o novo curso
    public function store(): void
    {
        $title       = $_POST['title'] ?? '';
        $description = $_POST['description'] ?? '';
        $creatorId   = $_SESSION['user']['id'];

        $course = new \App\Models\Course($title, $description, $creatorId);
        $this->courseRepo->save($course);

        header('Location: /teacher/courses');
        exit;
    }

    // 4) Exibe o form de edição
    public function edit(int $id): void
    {
        $course = $this->courseRepo->findById($id);
        $this->authorize($course);

        echo $this->twig->render('teacher/courses/edit.twig', [
            'course' => $course
        ]);
    }

    // 5) Atualiza o curso
    public function update(int $id): void
    {
        $course = $this->courseRepo->findById($id);
        $this->authorize($course);

        $course->setTitle($_POST['title'] ?? $course->getTitle());
        $course->setDescription($_POST['description'] ?? $course->getDescription());
        $this->courseRepo->save($course);

        header('Location: /teacher/courses');
        exit;
    }

    // 6) Remove o curso
    public function destroy(int $id): void
    {
        $course = $this->courseRepo->findById($id);
        $this->authorize($course);

        $this->courseRepo->delete($id);
        header('Location: /teacher/courses');
        exit;
    }

    // 7) Exibe detalhes de um curso
    public function show(int $id): void
    {
        $course = $this->courseRepo->findById($id);
        $this->authorize($course);

        // comentários
        $commentCount = count($this->commentRepo->findByCourseId($id));

        // participantes ativos
        $participantCount = $this->participationRepo->countActiveByCourse($id);

        echo $this->twig->render('teacher/courses/show.twig', [
            'course'           => $course,
            'commentCount'     => $commentCount,
            'participantCount' => $participantCount,
        ]);
    }

    // 8) Exibe a lista de comentários de um curso
    public function comments(int $id): void
    {
        $course   = $this->courseRepo->findById($id);
        $this->authorize($course);

        $comments = $this->commentRepo->findByCourseId($id);

        echo $this->twig->render('teacher/courses/comments.twig', [
            'course'   => $course,
            'comments' => $comments
        ]);
    }

    // Valida que o usuário logado é o criador
    private function authorize($course): void
    {
        if (!$course || $course->getCreatorId() !== $_SESSION['user']['id']) {
            header('HTTP/1.1 403 Forbidden');
            echo "Acesso negado.";
            exit;
        }
    }
}
