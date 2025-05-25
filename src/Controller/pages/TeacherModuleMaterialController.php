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
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }
        error_log('DEBUG: Entrou no método store do upload de material');

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
            error_log('DEBUG: Falha no upload - arquivo não selecionado ou erro no upload.');
            header("Location: /teacher/courses/{$courseId}/modules/{$moduleId}/materials/create");
            exit;
        }

        try {
            $uploadResult = $this->handleFileUpload($_FILES['file'], $courseId, $moduleId);
        } catch (\Exception $e) {
            $_SESSION['flash']['error'][] = "Falha no upload: " . $e->getMessage();
            error_log('DEBUG: Exceção no upload Cloudinary: ' . $e->getMessage());
            header("Location: /teacher/courses/{$courseId}/modules/{$moduleId}/materials/create");
            exit;
        }

        // Criação do MaterialEntry só deve acontecer se o upload foi bem-sucedido
        $entry = new MaterialEntry(
            $moduleId,
            $_POST['title'] ?? pathinfo($uploadResult['public_id'], PATHINFO_BASENAME),
            $uploadResult['url'],
            $uploadResult['resource_type'],
            $uploadResult['public_id']
        );
        try {
            $this->entryRepo->save($entry);
        } catch (\Exception $e) {
            $_SESSION['flash']['error'][] = "Erro ao salvar material no banco: " . $e->getMessage();
            error_log('DEBUG: Exceção ao salvar no banco: ' . $e->getMessage());
            header("Location: /teacher/courses/{$courseId}/modules/{$moduleId}/materials/create");
            exit;
        }

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

    private function handleFileUpload($file, $courseId, $moduleId): array
    {
        $tmpPath = $file['tmp_name'];
        if (!file_exists($tmpPath)) {
            throw new \RuntimeException('Arquivo temporário não encontrado');
        }

        $type = $this->cloudinary->determineFileType($tmpPath);
        $folder = "courses/{$courseId}/modules/{$moduleId}";

        try {
            return $this->cloudinary->uploadFile($tmpPath, $type, $folder);
        } catch (\Exception $e) {
            error_log("Falha no upload para Cloudinary: " . $e->getMessage());
            error_log("Stack trace: " . $e->getTraceAsString());
            throw $e;
        }    }
}
