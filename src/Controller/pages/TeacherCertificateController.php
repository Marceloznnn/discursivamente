<?php
namespace Controller\pages;

use Middleware\TeacherMiddleware;
use Repositories\CertificateRepository;
use Repositories\CourseRepository;
use Repositories\UserRepository;
use PDO;
use Twig\Environment;

class TeacherCertificateController
{
    private Environment $twig;
    private CertificateRepository $certificateRepo;
    private CourseRepository $courseRepo;
    private UserRepository $userRepo;

    public function __construct(Environment $twig, PDO $pdo)
    {
        $this->twig = $twig;
        $this->certificateRepo = new CertificateRepository($pdo);
        $this->courseRepo = new CourseRepository($pdo);
        $this->userRepo = new UserRepository($pdo);
        
        // Aplicar middleware para garantir que apenas professores acessem
        (new TeacherMiddleware())->handle();
    }

    // Listar todos os certificados emitidos para um curso
    public function listByCourse(int $courseId): void
    {
        $course = $this->courseRepo->findById($courseId);
        if (!$course) {
            header('Location: /teacher/courses');
            exit;
        }

        // Verificar se o professor é o criador do curso
        if ($course->getCreatorId() !== $_SESSION['user']['id']) {
            $_SESSION['flash']['error'] = 'Você não tem permissão para gerenciar este curso.';
            header('Location: /teacher/courses');
            exit;
        }

        $certificates = $this->certificateRepo->findByCourseId($courseId);
        
        // Pegar informações dos usuários
        $certificatesWithUsers = [];
        foreach ($certificates as $certificate) {
            $user = $this->userRepo->findById($certificate->getUserId());
            $certificatesWithUsers[] = [
                'certificate' => $certificate,
                'user' => $user
            ];
        }

        echo $this->twig->render('teacher/certificates/list.twig', [
            'certificates' => $certificatesWithUsers,
            'course' => $course
        ]);
    }

    // Revogar um certificado (ex: em caso de fraude)
    public function revokeCertificate(int $id): void
    {
        // Implementação para revogar certificado
    }

    // Gerar relatório de certificados
    public function generateReport(int $courseId): void
    {
        // Implementação para gerar relatório de certificados em CSV ou PDF
    }
}