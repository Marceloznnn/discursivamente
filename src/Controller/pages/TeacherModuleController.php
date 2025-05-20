<?php

namespace Controller\pages;

use Middleware\TeacherMiddleware;
use Repositories\ModuleRepository;
use Repositories\CourseRepository;
use Twig\Environment;
use PDO;

class TeacherModuleController
{
    private Environment $twig;
    private ModuleRepository $moduleRepo;
    private CourseRepository $courseRepo;

    public function __construct(
        Environment $twig,
        PDO $pdo
    ) {
        TeacherMiddleware::handle();

        $this->twig        = $twig;
        $this->moduleRepo  = new ModuleRepository($pdo);
        $this->courseRepo  = new CourseRepository($pdo);
    }

    /**
     * Lista todos os módulos de um curso.
     */
    public function index(int $courseId): void
    {
        $course = $this->courseRepo->findById($courseId);
        $this->authorize($course);

        $modules = $this->moduleRepo->findByCourse($courseId);
        echo $this->twig->render('teacher/modules/index.twig', [
            'course'  => $course,
            'modules' => $modules,
        ]);
    }

    /**
     * Exibe formulário de criação de módulo.
     */
    public function create(int $courseId): void
    {
        $course = $this->courseRepo->findById($courseId);
        $this->authorize($course);

        echo $this->twig->render('teacher/modules/create.twig', [
            'course' => $course,
        ]);
    }

    /**
     * Persiste novo módulo.
     */
    public function store(int $courseId): void
    {
        $course = $this->courseRepo->findById($courseId);
        $this->authorize($course);

        $title       = trim($_POST['title'] ?? '');
        $description = trim($_POST['description'] ?? null);
        $position    = (int)($_POST['position'] ?? 1);

        if (empty($title)) {
            $_SESSION['flash']['error'][] = "Título é obrigatório.";
            header("Location: /teacher/courses/{$courseId}/modules/create");
            exit;
        }

        $module = new \App\Models\Module(
            courseId:   $courseId,
            title:      $title,
            description:$description,
            position:   $position
        );

        $this->moduleRepo->save($module);
        $_SESSION['flash']['success'][] = "Módulo criado com sucesso.";

        header("Location: /teacher/courses/{$courseId}/modules");
        exit;
    }

    /**
     * Exibe formulário de edição de módulo.
     */
    public function edit(int $courseId, int $moduleId): void
    {
        $course = $this->courseRepo->findById($courseId);
        $this->authorize($course);

        $module = $this->moduleRepo->findById($moduleId);
        if (!$module) {
            http_response_code(404);
            echo "Módulo não encontrado.";
            exit;
        }

        echo $this->twig->render('teacher/modules/edit.twig', [
            'course' => $course,
            'module' => $module,
        ]);
    }

    /**
     * Atualiza módulo.
     */
    public function update(int $courseId, int $moduleId): void
    {
        $course = $this->courseRepo->findById($courseId);
        $this->authorize($course);

        $module = $this->moduleRepo->findById($moduleId);
        if (!$module) {
            http_response_code(404);
            echo "Módulo não encontrado."; 
            exit;
        }

        $title       = trim($_POST['title'] ?? '');
        $description = trim($_POST['description'] ?? null);
        $position    = (int)($_POST['position'] ?? $module->getPosition());

        if (empty($title)) {
            $_SESSION['flash']['error'][] = "Título é obrigatório.";
            header("Location: /teacher/courses/{$courseId}/modules/{$moduleId}/edit");
            exit;
        }

        $module->setTitle($title);
        $module->setDescription($description);
        $module->setPosition($position);
        $module->setUpdatedAt((new \DateTime())->format('Y-m-d H:i:s'));

        $this->moduleRepo->save($module);
        $_SESSION['flash']['success'][] = "Módulo atualizado com sucesso.";

        header("Location: /teacher/courses/{$courseId}/modules");
        exit;
    }

    /**
     * Exclui módulo.
     */
    public function destroy(int $courseId, int $moduleId): void
    {
        $course = $this->courseRepo->findById($courseId);
        $this->authorize($course);

        $this->moduleRepo->delete($moduleId);
        $_SESSION['flash']['success'][] = "Módulo excluído com sucesso.";

        header("Location: /teacher/courses/{$courseId}/modules");
        exit;
    }

    /**
     * Reordena módulos via AJAX.
     */
    public function reorder(int $courseId): void
    {
        $course = $this->courseRepo->findById($courseId);
        $this->authorize($course);

        $order = $_POST['order'] ?? [];
        $this->moduleRepo->reorderModules($courseId, $order);
        echo json_encode(['success' => true]);
        exit;
    }

    /**
     * Valida acesso do professor.
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