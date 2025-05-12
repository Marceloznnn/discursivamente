<?php

namespace Controller\pages;

use Middleware\TeacherMiddleware;
use Repositories\MaterialRepository;
use Repositories\CourseRepository;
use Services\CloudinaryService;
use PDO;
use Twig\Environment;

class TeacherMaterialController
{
    private Environment $twig;
    private MaterialRepository $materialRepo;
    private CourseRepository $courseRepo;
    private CloudinaryService $cloudService;

    public function __construct(Environment $twig, PDO $pdo, CloudinaryService $cloudService)
    {
        TeacherMiddleware::handle();

        $this->twig         = $twig;
        $this->materialRepo = new MaterialRepository($pdo);
        $this->courseRepo   = new CourseRepository($pdo);
        $this->cloudService = $cloudService;
    }

    // Lista materiais de um curso
    public function index(int $courseId): void
    {
        $course = $this->courseRepo->findById($courseId);
        $this->authorize($course);

        $materials = $this->materialRepo->findByCourseId($courseId);

        echo $this->twig->render('teacher/materials/index.twig', [
            'course'    => $course,
            'materials' => $materials
        ]);
    }

    // Form para novo material
    public function create(int $courseId): void
    {
        $course = $this->courseRepo->findById($courseId);
        $this->authorize($course);

        echo $this->twig->render('teacher/materials/create.twig', [
            'course' => $course
        ]);
    }

    // Persiste o material (upload via Cloudinary)
    public function store(int $courseId): void
    {
        $course = $this->courseRepo->findById($courseId);
        $this->authorize($course);

        // 1) Log inicial
        error_log("=== TeacherMaterialController::store START ===");
        error_log("POST: " . print_r($_POST, true));
        error_log("FILES: " . print_r($_FILES, true));

        $title = trim($_POST['title'] ?? '');
        $file  = $_FILES['file'] ?? null;

        $_SESSION['flash'] = $_SESSION['flash'] ?? [];

        if ($file && $file['error'] === UPLOAD_ERR_OK) {
            error_log("Upload recebido com error=UPLOAD_ERR_OK");
            if (!is_uploaded_file($file['tmp_name']) || !file_exists($file['tmp_name'])) {
                error_log("Falha em is_uploaded_file || file_exists");
                $_SESSION['flash']['error'][] = "Falha no envio: arquivo inválido ou expirado.";
                header("Location: /teacher/courses/{$courseId}/materials/create");
                exit;
            }

            $mimeType = mime_content_type($file['tmp_name']);
            error_log("MIME detectado: {$mimeType}");
            $allowed  = ['image/jpeg', 'image/png', 'image/webp', 'video/mp4', 'video/webm'];
            if (!in_array($mimeType, $allowed, true)) {
                error_log("MIME não permitido");
                $_SESSION['flash']['error'][] = "Tipo de arquivo não permitido: {$mimeType}";
                header("Location: /teacher/courses/{$courseId}/materials/create");
                exit;
            }

            try {
                error_log("Tentando upload no Cloudinary...");
                $uploadResult = $this->cloudService->upload($file['tmp_name'], "courses/{$courseId}");
                // Armazena o MIME detectado em vez do resource_type genérico
                $content = $uploadResult['url'];
                $type    = $mimeType;
                error_log("Upload OK: URL={$content}, MIME type={$type}");
            } catch (\Exception $e) {
                error_log("Exceção no Cloudinary: " . $e->getMessage());
                $_SESSION['flash']['error'][] = "Erro ao enviar o arquivo: " . $e->getMessage();
                header("Location: /teacher/courses/{$courseId}/materials/create");
                exit;
            }
        } else {
            error_log("Nenhum arquivo ou erro no upload: " . ($file['error'] ?? 'no-file'));
            $content = trim($_POST['content'] ?? '');
            if (empty($content)) {
                error_log("Conteúdo vazio e sem arquivo");
                $_SESSION['flash']['error'][] = "Você deve enviar um arquivo ou um link válido.";
                header("Location: /teacher/courses/{$courseId}/materials/create");
                exit;
            }
            $type = 'link';
            error_log("Usando conteúdo por link: {$content}");
        }

        // Persiste
        try {
            $material = new \App\Models\Material($courseId, $title, $content, $type);
            $this->materialRepo->save($material);
            $_SESSION['flash']['success'][] = "Material salvo com sucesso!";
            error_log("Material salvo no banco: title={$title}");
        } catch (\Exception $e) {
            error_log("Erro ao salvar material: " . $e->getMessage());
            $_SESSION['flash']['error'][] = "Erro interno ao salvar o material.";
            header("Location: /teacher/courses/{$courseId}/materials/create");
            exit;
        }

        error_log("=== TeacherMaterialController::store END ===");
        header("Location: /teacher/courses/{$courseId}/materials");
        exit;
    }

    

    // Excluir material
    public function destroy(int $courseId, int $id): void
    {
        $course = $this->courseRepo->findById($courseId);
        $this->authorize($course);

        $this->materialRepo->delete($id);
        header("Location: /teacher/courses/{$courseId}/materials");
        exit;
    }

    private function authorize($course): void
    {
        if (!$course || $course->getCreatorId() !== $_SESSION['user']['id']) {
            header('HTTP/1.1 403 Forbidden');
            echo "Acesso negado.";
            exit;
        }
    }
}
