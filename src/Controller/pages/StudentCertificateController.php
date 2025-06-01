<?php
namespace Controller\pages;

use Middleware\AuthMiddleware;
use Repositories\CertificateRepository;
use Repositories\CourseRepository;
use App\Models\Certificate;
use PDO;
use Twig\Environment;

class StudentCertificateController
{    private Environment $twig;
    private CertificateRepository $certificateRepo;
    private CourseRepository $courseRepo;    public function __construct(Environment $twig, PDO $pdo)
    { 
        $this->twig = $twig;
        $this->certificateRepo = new CertificateRepository($pdo);
        $this->courseRepo = new CourseRepository($pdo);
        
        // Aplicar middleware para garantir que o usuário esteja logado
        (new AuthMiddleware())->handle();
    }

    // Ver todos os certificados do aluno
    public function index(): void
    {
        $userId = $_SESSION['user']['id'];
        $certificates = $this->certificateRepo->findByUser($userId);
        
        // Adicionar informações do curso para cada certificado
        $certificatesWithCourses = [];
        foreach ($certificates as $certificate) {
            $course = $this->courseRepo->findById($certificate->getCourseId());
            $certificatesWithCourses[] = [
                'certificate' => $certificate,
                'course' => $course
            ];
        }

        echo $this->twig->render('student/certificates/index.twig', [
            'certificates' => $certificatesWithCourses
        ]);
    }

    // Ver certificado específico
    public function view(int $id): void
    {
        $userId = $_SESSION['user']['id'];
        $certificates = $this->certificateRepo->findByUser($userId);
        
        $found = false;
        $targetCertificate = null;
        $course = null;
        
        foreach ($certificates as $certificate) {
            if ($certificate->getId() == $id) {
                $found = true;
                $targetCertificate = $certificate;
                $course = $this->courseRepo->findById($certificate->getCourseId());
                break;
            }
        }
        
        if (!$found) {
            $_SESSION['flash']['error'] = 'Certificado não encontrado.';
            header('Location: /student/certificates');
            exit;
        }

        echo $this->twig->render('student/certificates/view.twig', [
            'certificate' => $targetCertificate,
            'course' => $course
        ]);
    }

    // Verifica se o aluno completou o curso e emite certificado se sim
    public function checkAndGenerateCertificate(int $courseId): void
    {
        $userId = $_SESSION['user']['id'];
        
        // Verificar se já tem certificado
        $existingCertificate = $this->certificateRepo->findByUserAndCourse($userId, $courseId);
        if ($existingCertificate) {
            $_SESSION['flash']['info'] = 'Você já possui um certificado para este curso.';
            header("Location: /student/certificates/view/{$existingCertificate->getId()}");
            exit;
        }
          // Todo: Implementar nova lógica de progresso do curso
        $allCompleted = true; // Temporariamente sempre true até nova implementação
        
        if (!$allCompleted) {
            $_SESSION['flash']['error'] = 'Você precisa completar todo o conteúdo para receber o certificado.';
            header("Location: /courses/{$courseId}");
            exit;
        }
        
        // Gerar certificado
        $course = $this->courseRepo->findById($courseId);
        $certificateNumber = 'CERT-' . strtoupper(uniqid());
        $certificateUrl = $this->generateCertificateFile($userId, $courseId, $certificateNumber);
        
        $certificate = new Certificate(
            $userId,
            $courseId,
            date('Y-m-d H:i:s'),
            $certificateUrl,
            $certificateNumber
        );
        
        $this->certificateRepo->save($certificate);
        
        $_SESSION['flash']['success'] = 'Certificado gerado com sucesso!';
        header("Location: /student/certificates");
        exit;
    }
    
    private function generateCertificateFile(int $userId, int $courseId, string $certificateNumber): string
    {
        // Implementação para gerar o PDF do certificado
        // Retorna a URL do arquivo gerado
        // Este é apenas um exemplo, seria necessário usar alguma biblioteca de geração de PDF
        return "/certificates/{$userId}_{$courseId}_{$certificateNumber}.pdf";
    }
}