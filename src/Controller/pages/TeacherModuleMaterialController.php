<?php
namespace Controller\pages;

use Middleware\TeacherMiddleware;
use Repositories\ModuleRepository;
use Repositories\CourseRepository;
use Repositories\MaterialEntryRepository;
use Services\CloudinaryService;
use App\Models\MaterialEntry;
use Twig\Environment;
use PDO;

class TeacherModuleMaterialController
{
    private Environment $twig;
    private ModuleRepository $moduleRepo;
    private CourseRepository $courseRepo;
    private MaterialEntryRepository $entryRepo;
    private CloudinaryService $cloudinary;

    public function __construct(Environment $twig, PDO $pdo, CloudinaryService $cloudinary)
    {
        TeacherMiddleware::handle();
        $this->twig       = $twig;
        $this->moduleRepo = new ModuleRepository($pdo);
        $this->courseRepo = new CourseRepository($pdo);
        $this->entryRepo  = new MaterialEntryRepository($pdo);
        $this->cloudinary = $cloudinary;
    }

    /**
     * Lista entradas de material de um módulo.
     */
    public function index(int $courseId, int $moduleId): void
    {
        $course = $this->courseRepo->findById($courseId);
        $this->authorize($course);

        $module = $this->moduleRepo->findById($moduleId);
        if (!$module || $module->getCourseId() !== $courseId) {
            http_response_code(404);
            echo "Módulo não encontrado.";
            exit;
        }

        $entries = $this->entryRepo->findByMaterialId($moduleId);
        echo $this->twig->render('teacher/modules/materials/index.twig', [
            'course'  => $course,
            'module'  => $module,
            'entries' => $entries,
        ]);
    }

    /**
     * Exibe formulário de upload de material.
     */
    public function create(int $courseId, int $moduleId): void
    {
        $course = $this->courseRepo->findById($courseId);
        $this->authorize($course);

        $module = $this->moduleRepo->findById($moduleId);
        if (!$module || $module->getCourseId() !== $courseId) {
            http_response_code(404);
            echo "Módulo não encontrado.";
            exit;
        }

        echo $this->twig->render('teacher/modules/materials/create.twig', [
            'course' => $course,
            'module' => $module,
        ]);
    }

    /**
     * Processa o upload e salva no banco.
     */
    public function store(int $courseId, int $moduleId): void
    {
        $course = $this->courseRepo->findById($courseId);
        $this->authorize($course);

        $module = $this->moduleRepo->findById($moduleId);
        if (!$module || $module->getCourseId() !== $courseId) {
            http_response_code(404);
            echo "Módulo não encontrado.";
            exit;
        }

        if (empty($_FILES['file']['tmp_name']) || $_FILES['file']['error'] !== UPLOAD_ERR_OK) {
            $_SESSION['flash']['error'][] = "Selecione um arquivo válido.";
            header("Location: /teacher/courses/{$courseId}/modules/{$moduleId}/materials/create");
            exit;
        }

        $tmpPath = $_FILES['file']['tmp_name'];
        $type    = $this->cloudinary->determineFileType($tmpPath);

        try {
            $result = $this->cloudinary->upload($tmpPath, "courses/{$courseId}/modules/{$moduleId}", $type);
        } catch (\Exception $e) {
            $_SESSION['flash']['error'][] = "Falha no upload: " . $e->getMessage();
            header("Location: /teacher/courses/{$courseId}/modules/{$moduleId}/materials/create");
            exit;
        }

        // Agora passamos 5 argumentos, incluindo o public_id
        $entry = new MaterialEntry(
            $moduleId,
            $_POST['title'] ?? pathinfo($result['public_id'], PATHINFO_BASENAME),
            $result['url'],
            $type, // Corrigido: salva o tipo correto (image, video, raw)
            $result['public_id']
        );
        $this->entryRepo->save($entry);

        $_SESSION['flash']['success'][] = "Material enviado com sucesso.";
        header("Location: /teacher/courses/{$courseId}/modules/{$moduleId}/materials");
        exit;
    }

    /**
     * Exclui uma entrada de material, apagando também do Cloudinary.
     */
    public function destroy(int $courseId, int $moduleId, int $entryId): void
    {
        $course = $this->courseRepo->findById($courseId);
        $this->authorize($course);

        $entry = $this->entryRepo->findById($entryId);
        if (!$entry || $entry->getMaterialId() !== $moduleId) {
            http_response_code(404);
            echo "Entrada de material não encontrada.";
            exit;
        }

        // Apaga do Cloudinary
        try {
            $this->cloudinary->deleteFile($entry->getPublicId());
        } catch (\Exception $e) {
            $_SESSION['flash']['error'][] = "Erro ao remover arquivo remoto.";
        }

        // Apaga do banco
        $this->entryRepo->delete($entryId);
        $_SESSION['flash']['success'][] = "Entrada de material removida com sucesso.";

        header("Location: /teacher/courses/{$courseId}/modules/{$moduleId}/materials");
        exit;
    }

    /**
     * Valida acesso do professor (criador do curso).
     */
    private function authorize($course): void
    {
        if (!$course || $course->getCreatorId() !== ($_SESSION['user']['id'] ?? null)) {
            header('HTTP/1.1 403 Forbidden');
            echo "Acesso negado.";
            exit;
        }
    }
}
