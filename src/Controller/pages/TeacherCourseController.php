<?php

namespace Controller\pages;

use Middleware\TeacherMiddleware;
use Repositories\CourseRepository;
use Repositories\CategoryRepository;
use Repositories\CourseCommentRepository;
use Repositories\CourseParticipationRepository;
use Services\CloudinaryService;
use App\Models\Course;
use PDO;
use Twig\Environment;

class TeacherCourseController
{
    private Environment $twig;
    private PDO $pdo;
    private CourseRepository $courseRepo;
    private CategoryRepository $categoryRepo;
    private CourseCommentRepository $commentRepo;
    private CourseParticipationRepository $participationRepo;
    private CloudinaryService $cloudService;

    // Configuração da paginação
    private const COURSES_PER_PAGE = 16;

    public function __construct(Environment $twig, PDO $pdo, CloudinaryService $cloudService)
    {
        TeacherMiddleware::handle();

        $this->twig              = $twig;
        $this->pdo               = $pdo;
        $this->courseRepo        = new CourseRepository($pdo);
        $this->categoryRepo      = new CategoryRepository($pdo);
        $this->commentRepo       = new CourseCommentRepository($pdo);
        $this->participationRepo = new CourseParticipationRepository($pdo);
        $this->cloudService      = $cloudService;
    }

    // 1) Lista todos os cursos do professor com paginação
    public function index(): void
    {
        $userId = $_SESSION['user']['id'];
        $page = max(1, (int)($_GET['page'] ?? 1));
        
        // Busca o total de cursos
        $totalCourses = $this->courseRepo->countByCreatorId($userId);
        
        // Calcula a paginação
        $totalPages = ceil($totalCourses / self::COURSES_PER_PAGE);
        $offset = ($page - 1) * self::COURSES_PER_PAGE;
        
        // Busca os cursos da página atual
        $courses = $this->courseRepo->findByCreatorIdPaginated($userId, self::COURSES_PER_PAGE, $offset);

        echo $this->twig->render('teacher/courses/index.twig', [
            'courses' => $courses,
            'pagination' => [
                'current_page' => $page,
                'total_pages' => $totalPages,
                'total_courses' => $totalCourses,
                'per_page' => self::COURSES_PER_PAGE,
                'has_prev' => $page > 1,
                'has_next' => $page < $totalPages,
                'prev_page' => $page - 1,
                'next_page' => $page + 1
            ]
        ]);
    }

    // 2) Exibe o formulário de criação
    public function create(): void
    {
        $categories = $this->categoryRepo->findAll();
        echo $this->twig->render('teacher/courses/create.twig', [
            'categories' => $categories
        ]);
    }

    // 3) Persiste o novo curso
    public function store(): void
    {
        $title       = $_POST['title'] ?? '';
        $description = $_POST['description'] ?? '';
        $creatorId   = $_SESSION['user']['id'];
        $categoryId  = isset($_POST['category_id']) && $_POST['category_id'] !== ''
                         ? (int) $_POST['category_id']
                         : null;

        $course = new Course($title, $description, $creatorId, $categoryId);
        $this->courseRepo->save($course);

        header('Location: /teacher/courses');
        exit;
    }

    // 4) Exibe o form de edição
    public function edit(int $id): void
    {
        $course = $this->courseRepo->findById($id);
        $this->authorize($course);
        $categories = $this->categoryRepo->findAll();

        echo $this->twig->render('teacher/courses/edit.twig', [
            'course'     => $course,
            'categories' => $categories
        ]);
    }

    // 5) Atualiza o curso
    public function update(int $id): void
    {
        $course = $this->courseRepo->findById($id);
        $this->authorize($course);

        $course->setTitle($_POST['title'] ?? $course->getTitle());
        $course->setDescription($_POST['description'] ?? $course->getDescription());
        $categoryId = isset($_POST['category_id']) && $_POST['category_id'] !== ''
                       ? (int) $_POST['category_id']
                       : null;
        $course->setCategoryId($categoryId);

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

        $commentCount     = count($this->commentRepo->findByCourseId($id));
        $participantCount = $this->participationRepo->countActiveByCourse($id);

        // nova linha: busca a categoria pelo ID (pode ser null)
        $category = $course->getCategoryId()
            ? $this->categoryRepo->findById($course->getCategoryId())
            : null;

        echo $this->twig->render('teacher/courses/show.twig', [
            'course'           => $course,
            'commentCount'     => $commentCount,
            'participantCount' => $participantCount,
            'category'         => $category,
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