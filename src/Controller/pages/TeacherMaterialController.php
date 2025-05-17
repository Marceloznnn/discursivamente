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

    /**
     * Construtor com injeção de dependências.
     * Aplica middleware para garantir acesso de professor.
     */
    public function __construct(
        Environment $twig,
        MaterialRepository $materialRepo,
        CourseRepository $courseRepo,
        CloudinaryService $cloudService
    ) {
        TeacherMiddleware::handle();

        $this->twig         = $twig;
        $this->materialRepo = $materialRepo;
        $this->courseRepo   = $courseRepo;
        $this->cloudService = $cloudService;
    }

    /**
     * Lista materiais de um curso.
     */
    public function index(int $courseId): void
    {
        $course = $this->courseRepo->findById($courseId);
        $this->authorize($course);

        $materials = $this->materialRepo->findByCourseId($courseId);

        echo $this->twig->render('teacher/materials/index.twig', [
            'course'    => $course,
            'materials' => $materials,
        ]);
    }

    /**
     * Formulário para criar novo material.
     */
    public function create(int $courseId): void
    {
        $course = $this->courseRepo->findById($courseId);
        $this->authorize($course);

        echo $this->twig->render('teacher/materials/create.twig', [
            'course' => $course,
        ]);
    }

    /**
     * Persiste o material (upload via Cloudinary ou link).
     */
    public function store(int $courseId): void
    {
        $course = $this->courseRepo->findById($courseId);
        $this->authorize($course);

        $_SESSION['flash'] = $_SESSION['flash'] ?? [];

        $title = trim($_POST['title'] ?? '');
        $file  = $_FILES['file'] ?? null;

        if (empty($title)) {
            $_SESSION['flash']['error'][] = "O título do material é obrigatório.";
            header("Location: /teacher/courses/{$courseId}/materials/create");
            exit;
        }

        if ($file && $file['error'] === UPLOAD_ERR_OK) {
            if (!is_uploaded_file($file['tmp_name']) || !file_exists($file['tmp_name'])) {
                $_SESSION['flash']['error'][] = "Falha no envio: arquivo inválido ou expirado.";
                header("Location: /teacher/courses/{$courseId}/materials/create");
                exit;
            }

            $mimeType = mime_content_type($file['tmp_name']);
            $allowed  = ['image/jpeg', 'image/png', 'image/webp', 'video/mp4', 'video/webm', 'audio/mpeg', 'audio/ogg', 'audio/wav', 'application/pdf'];

            if (!in_array($mimeType, $allowed, true)) {
                $_SESSION['flash']['error'][] = "Tipo de arquivo não permitido: {$mimeType}";
                header("Location: /teacher/courses/{$courseId}/materials/create");
                exit;
            }

            try {
                $uploadResult = $this->cloudService->upload($file['tmp_name'], "courses/{$courseId}");
                $content = $uploadResult['url'];
                $type    = $mimeType;
            } catch (\Exception $e) {
                $_SESSION['flash']['error'][] = "Erro ao enviar o arquivo: " . $e->getMessage();
                header("Location: /teacher/courses/{$courseId}/materials/create");
                exit;
            }
        } else {
            // Caso não tenha arquivo, tenta pegar conteúdo via link
            $content = trim($_POST['content'] ?? '');
            if (empty($content)) {
                $_SESSION['flash']['error'][] = "Você deve enviar um arquivo ou um link válido.";
                header("Location: /teacher/courses/{$courseId}/materials/create");
                exit;
            }
            $type = 'link';
        }

        try {
            $material = new \App\Models\Material($courseId, $title, $content, $type);
            $this->materialRepo->save($material);
            $_SESSION['flash']['success'][] = "Material salvo com sucesso!";
        } catch (\Exception $e) {
            $_SESSION['flash']['error'][] = "Erro interno ao salvar o material.";
            header("Location: /teacher/courses/{$courseId}/materials/create");
            exit;
        }

        header("Location: /teacher/courses/{$courseId}/materials");
        exit;
    }

    /**
     * Excluir material.
     */
    public function destroy(int $courseId, int $id): void
    {
        $course = $this->courseRepo->findById($courseId);
        $this->authorize($course);

        $this->materialRepo->delete($id);

        $_SESSION['flash']['success'][] = "Material excluído com sucesso!";
        header("Location: /teacher/courses/{$courseId}/materials");
        exit;
    }

    // Exibe detalhe de um material específico
public function show(int $courseId, int $id): void
{
    $course   = $this->courseRepo->findById($courseId);
    $this->authorize($course);

    $material = $this->materialRepo->findById($id);
    if (!$material || $material->getCourseId() !== $courseId) {
        header('HTTP/1.1 404 Not Found');
        echo "Material não encontrado.";
        exit;
    }

    // --- Novo: buscar todos os materiais deste mesmo módulo ---
    $moduleId = $material->getModuleId(); 
    $moduleMaterials = $moduleId
        ? $this->materialRepo->findByModuleId($courseId, $moduleId)
        : [];

    echo $this->twig->render('teacher/materials/show.twig', [
        'course'           => $course,
        'material'         => $material,
        'moduleMaterials'  => $moduleMaterials,
        'moduleId'         => $moduleId,
    ]);
}


    /**
     * Verifica se o usuário tem autorização para acessar o curso.
     */
    private function authorize($course): void
    {
        if (!$course || $course->getCreatorId() !== ($_SESSION['user']['id'] ?? null)) {
            header('HTTP/1.1 403 Forbidden');
            echo "Acesso negado.";
            exit;
        }
    }
    public function addToModule(int $courseId, int $moduleId): void
{
    $course = $this->courseRepo->findById($courseId);
    $this->authorize($course);

    echo $this->twig->render('teacher/materials/add_to_module.twig', [
        'course' => $course,
        'moduleId' => $moduleId,
    ]);
}

public function storeToModule(int $courseId, int $moduleId): void
{
    $course = $this->courseRepo->findById($courseId);
    $this->authorize($course);

    $_SESSION['flash'] = $_SESSION['flash'] ?? [];

    $title = trim($_POST['title'] ?? '');
    $file  = $_FILES['file'] ?? null;

    if (empty($title)) {
        $_SESSION['flash']['error'][] = "O título do material é obrigatório.";
        header("Location: /teacher/courses/{$courseId}/modules/{$moduleId}/materials/add");
        exit;
    }

    if ($file && $file['error'] === UPLOAD_ERR_OK) {
        $mimeType = mime_content_type($file['tmp_name']);
        $allowed = ['image/jpeg', 'image/png', 'image/webp', 'video/mp4', 'video/webm', 'audio/mpeg', 'audio/ogg', 'audio/wav', 'application/pdf'];

        if (!in_array($mimeType, $allowed)) {
            $_SESSION['flash']['error'][] = "Tipo de arquivo não permitido: {$mimeType}";
            header("Location: /teacher/courses/{$courseId}/modules/{$moduleId}/materials/add");
            exit;
        }

        try {
            $uploadResult = $this->cloudService->upload($file['tmp_name'], "courses/{$courseId}");
            $content = $uploadResult['url'];
            $type = $mimeType;
        } catch (\Exception $e) {
            $_SESSION['flash']['error'][] = "Erro ao enviar o arquivo.";
            header("Location: /teacher/courses/{$courseId}/modules/{$moduleId}/materials/add");
            exit;
        }
    } else {
        $content = trim($_POST['content'] ?? '');
        if (empty($content)) {
            $_SESSION['flash']['error'][] = "Você deve enviar um arquivo ou link.";
            header("Location: /teacher/courses/{$courseId}/modules/{$moduleId}/materials/add");
            exit;
        }
        $type = 'link';
    }

    try {
        $material = new \App\Models\Material($courseId, $title, $content, $type, $moduleId);
        $this->materialRepo->save($material);
        $_SESSION['flash']['success'][] = "Material adicionado ao módulo com sucesso!";
    } catch (\Exception $e) {
        $_SESSION['flash']['error'][] = "Erro interno ao salvar o material.";
        header("Location: /teacher/courses/{$courseId}/modules/{$moduleId}/materials/add");
        exit;
    }

    header("Location: /teacher/courses/{$courseId}/materials");
    exit;
}

}
