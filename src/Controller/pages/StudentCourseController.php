<?php
// src/Controller/pages/StudentCourseController.php

namespace Controller\pages;

use Middleware\AuthMiddleware;
use Repositories\CourseRepository;
use Repositories\CourseParticipationRepository;
use PDO;
use Twig\Environment;

class StudentCourseController
{
    private Environment $twig;
    private PDO $pdo;
    private CourseRepository $courseRepo;
    private CourseParticipationRepository $participationRepo;

    public function __construct(Environment $twig, PDO $pdo)
    {
        // Garante que só usuários autenticados acessem
        AuthMiddleware::handle();

        $this->twig              = $twig;
        $this->pdo               = $pdo;
        $this->courseRepo        = new CourseRepository($pdo);
        $this->participationRepo = new CourseParticipationRepository($pdo);
    }

    /**
     * 1) Lista todos os cursos disponíveis e destaca nos quais o aluno já participa.
     */
    public function index(): void
    {
        $userId = $_SESSION['user']['id'];
        $allCourses      = $this->courseRepo->findAll();
        $myParticipating = $this->participationRepo->findByUser($userId);

        // extrai apenas os course_id das participações ativas
        $activeCourseIds = array_map(
            fn($p) => $p->getCourseId(),
            array_filter($myParticipating, fn($p) => $p->getLeftAt() === null)
        );

        echo $this->twig->render('student/courses/index.twig', [
            'courses'         => $allCourses,
            'activeCourseIds' => $activeCourseIds,
        ]);
    }

    /**
     * 2) Ação para entrar (join) em um curso.
     */
    public function join(int $courseId): void
    {
        $userId = $_SESSION['user']['id'];

        // opcional: verificar se o curso existe
        $course = $this->courseRepo->findById($courseId);
        if (! $course) {
            header('HTTP/1.1 404 Not Found');
            echo "Curso não encontrado.";
            exit;
        }

        $this->participationRepo->joinCourse($userId, $courseId);

        header("Location: /student/courses");
        exit;
    }

    /**
     * 3) Ação para sair (leave) de um curso.
     */
    public function leave(int $courseId): void
    {
        $userId = $_SESSION['user']['id'];

        $this->participationRepo->leaveCourse($userId, $courseId);

        header("Location: /student/courses");
        exit;
    }

    /**
     * 4) (Opcional) Lista de participantes de um curso — só para instrutores/verificação.
     */
    public function participants(int $courseId): void
    {
        // Se quiser restringir: TeacherMiddleware::handle();
        $course      = $this->courseRepo->findById($courseId);
        if (! $course) {
            header('HTTP/1.1 404 Not Found');
            echo "Curso não encontrado.";
            exit;
        }

        $allParts = $this->participationRepo->findByCourse($courseId);
        echo $this->twig->render('teacher/courses/participants.twig', [
            'course'       => $course,
            'participations' => $allParts,
        ]);
    }
}
