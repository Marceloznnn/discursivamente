<?php
// src/Controller/pages/CourseController.php
namespace Controller\pages;

use Infrastructure\Database\Connection;
use Repositories\CourseRepository;
use Repositories\CourseMemberRepository;
use Repositories\CourseMaterialRepository;
use App\Models\Course;
use Twig\Environment;

class CourseController
{
    private Environment $twig;
    private CourseRepository $repo;
    private CourseMemberRepository $memberRepo;
    private CourseMaterialRepository $materialRepo;

    public function __construct(Environment $twig)
    {
        $this->twig = $twig;
        $pdo = Connection::getInstance();
        $this->repo = new CourseRepository($pdo);
        $this->memberRepo = new CourseMemberRepository($pdo);
        $this->materialRepo = new CourseMaterialRepository($pdo);
    }

    public function index(): void
    {
        error_log('[CourseController::index] Carregando lista de cursos.');
        $search = trim(filter_input(INPUT_GET, 'q', FILTER_SANITIZE_SPECIAL_CHARS) ?? '');
        $courses = $this->repo->findAll();

        if ($search !== '') {
            $courses = array_filter($courses, fn(Course $c) => stripos($c->getTitle(), $search) !== false);
            foreach ($courses as $course) {
                $course->incrementSearchHits();
                $this->repo->save($course);
            }
        }

        echo $this->twig->render('curso/index.twig', ['courses' => $courses, 'search' => $search]);
    }

    public function search(): void
    {
        $this->index();
    }

    public function create(): void
    {
        error_log('[CourseController::create] Exibindo formulário de criação de curso.');
        echo $this->twig->render('curso/form.twig', ['course' => null]);
    }

    public function store(): void
    {
        error_log('[CourseController::store] Iniciando criação de curso.');
        $creatorId = $_SESSION['user']['id'] ?? null;
        error_log('[CourseController::store] creatorId = ' . var_export($creatorId, true));

        if (!$creatorId) {
            error_log('[CourseController::store] Usuário não autenticado. Redirecionando.');
            header('Location: /login');
            exit;
        }

        $data = filter_input_array(INPUT_POST, [
            'title' => FILTER_SANITIZE_SPECIAL_CHARS,
            'short_description' => FILTER_SANITIZE_SPECIAL_CHARS,
            'long_description' => FILTER_DEFAULT,
            'category_id' => FILTER_VALIDATE_INT,
            'status' => FILTER_SANITIZE_SPECIAL_CHARS,
            'cover_image' => FILTER_SANITIZE_URL,
            'course_requirements' => FILTER_DEFAULT,
            'learning_objectives' => FILTER_DEFAULT,
            'enrollment_method' => FILTER_SANITIZE_SPECIAL_CHARS,
        ]);
        error_log('[CourseController::store] Dados POST: ' . json_encode($data));

        try {
            $course = new Course(
                $data['title'], $data['short_description'], $creatorId,
                $data['status'] ?? 'draft', $data['enrollment_method'] ?? 'open',
                $data['category_id'], $data['long_description'], $data['cover_image'],
                $data['course_requirements'], $data['learning_objectives']
            );
            error_log('[CourseController::store] Course object instanciado.');

            $this->repo->save($course);
            $courseId = $course->getId();
            error_log('[CourseController::store] Curso salvo com sucesso. ID: ' . $courseId);

            $this->memberRepo->addMember($courseId, $creatorId, 'admin');
            error_log('[CourseController::store] Membro admin adicionado.');

            // Redireciona para a página principal de cursos
            header('Location: /curso');
            exit;

        } catch (\Throwable $e) {
            error_log('[CourseController::store] Erro ao criar curso: ' . $e->getMessage());
            echo $this->twig->render('curso/form.twig', [
                'course' => null,
                'error' => 'Não foi possível criar o curso. Por favor, tente novamente mais tarde.'
            ]);
            exit;
        }
    }

    public function edit(int $id): void
    {
        error_log('[CourseController::edit] Exibindo formulário de edição para curso ID: ' . $id);
        $course = $this->repo->findById($id);
        if (!$course) {
            header('Location: /curso'); exit;
        }
        echo $this->twig->render('curso/form.twig', ['course' => $course]);
    }

    public function update(int $id): void
    {
        error_log('[CourseController::update] Atualizando curso ID: ' . $id);
        $course = $this->repo->findById($id);
        if (!$course) { header('Location: /curso'); exit; }

        $data = filter_input_array(INPUT_POST, [
            'title' => FILTER_SANITIZE_SPECIAL_CHARS,
            'short_description' => FILTER_SANITIZE_SPECIAL_CHARS,
            'long_description' => FILTER_DEFAULT,
            'category_id' => FILTER_VALIDATE_INT,
            'status' => FILTER_SANITIZE_SPECIAL_CHARS,
            'cover_image' => FILTER_SANITIZE_URL,
            'course_requirements' => FILTER_DEFAULT,
            'learning_objectives' => FILTER_DEFAULT,
            'enrollment_method' => FILTER_SANITIZE_SPECIAL_CHARS,
        ]);

        try {
            $course->setTitle($data['title'] ?? $course->getTitle());
            $course->setShortDescription($data['short_description'] ?? $course->getShortDescription());
            $course->setLongDescription($data['long_description'] ?? $course->getLongDescription());
            $course->setCategoryId($data['category_id'] ?? $course->getCategoryId());
            $course->setStatus($data['status'] ?? $course->getStatus());
            $course->setCoverImage($data['cover_image'] ?? $course->getCoverImage());
            $course->setCourseRequirements($data['course_requirements'] ?? $course->getCourseRequirements());
            $course->setLearningObjectives($data['learning_objectives'] ?? $course->getLearningObjectives());
            $course->setEnrollmentMethod($data['enrollment_method'] ?? $course->getEnrollmentMethod());

            $this->repo->save($course);
            header('Location: /curso');
            exit;
        } catch (\Throwable $e) {
            error_log('[CourseController::update] Erro ao atualizar curso: ' . $e->getMessage());
            echo $this->twig->render('curso/form.twig', ['course' => $course, 'error' => 'Erro ao atualizar']);
            exit;
        }
    }

    public function delete(int $id): void
    {
        $this->repo->delete($id);
        header('Location: /curso');
        exit;
    }

    public function show(int $id): void
    {
        $course = $this->repo->findById($id);
        if (!$course) { header('Location: /curso'); exit; }

        $course->incrementClickThroughs();
        $this->repo->save($course);

        $memberCount = $this->memberRepo->countByCourseId($id);
        $course->setMemberCount($memberCount);

        $currentUserId = $_SESSION['user']['id'] ?? null;
        $isMember = $currentUserId ? $this->memberRepo->exists($id, $currentUserId) : false;

        echo $this->twig->render('curso/show.twig', ['course' => $course, 'isMember' => $isMember, 'userId' => $currentUserId]);
    }
}
