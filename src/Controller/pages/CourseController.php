<?php
// src/Controller/pages/CourseController.php
namespace Controller\pages;

use Infrastructure\Database\Connection;
use Repositories\CourseRepository;
use Repositories\CourseMemberRepository;
use App\Models\Course;
use Twig\Environment;

class CourseController
{
    private Environment $twig;
    private CourseRepository $repo;
    private CourseMemberRepository $memberRepo;

    public function __construct(Environment $twig)
    {
        $this->twig = $twig;
        $pdo = Connection::getInstance();
        $this->repo = new CourseRepository($pdo);
        $this->memberRepo = new CourseMemberRepository($pdo);
    }

    // Lista todos os cursos (com busca opcional)
    public function index(): void
    {
        $search = trim(filter_input(INPUT_GET, 'q', FILTER_SANITIZE_SPECIAL_CHARS) ?? '');
        $courses = $this->repo->findAll();

        if ($search !== '') {
            $courses = array_filter($courses, fn(Course $c) => stripos($c->getTitle(), $search) !== false);
        }

        // Atualiza métricas de exibição de busca
        foreach ($courses as $course) {
            $course->incrementSearchHits();
            $this->repo->save($course);
        }

        echo $this->twig->render('curso/index.twig', [
            'courses' => $courses,
            'search'  => $search,
        ]);
    }

    public function search(): void
    {
        // Alias para index (pode direcionar para index ou usar search_results.twig)
        $this->index();
    }

    // Formulário de criação
    public function create(): void
    {
        echo $this->twig->render('curso/form.twig', ['course' => null]);
    }

    // Armazena novo curso
    public function store(): void
    {
        $data = filter_input_array(INPUT_POST, [
            'title'               => FILTER_SANITIZE_SPECIAL_CHARS,
            'short_description'   => FILTER_SANITIZE_SPECIAL_CHARS,
            'long_description'    => FILTER_DEFAULT,
            'category_id'         => FILTER_VALIDATE_INT,
            'creator_id'          => FILTER_VALIDATE_INT,
            'status'              => FILTER_SANITIZE_SPECIAL_CHARS,
            'cover_image'         => FILTER_SANITIZE_URL,
            'course_requirements' => FILTER_DEFAULT,
            'learning_objectives' => FILTER_DEFAULT,
            'enrollment_method'   => FILTER_SANITIZE_SPECIAL_CHARS,
        ]);

        $course = new Course(
            $data['title'],
            $data['short_description'],
            $data['creator_id'],
            $data['status'] ?? 'draft',
            $data['enrollment_method'] ?? 'open',
            $data['category_id'],
            $data['long_description'],
            $data['cover_image'],
            $data['course_requirements'],
            $data['learning_objectives']
        );

        $this->repo->save($course);
        header('Location: /curso');
        exit;
    }

    // Formulário de edição
    public function edit(int $id): void
    {
        $course = $this->repo->findById($id);
        if (!$course) {
            header('Location: /curso');
            exit;
        }
        echo $this->twig->render('curso/form.twig', ['course' => $course]);
    }

    // Atualiza os dados do curso
    public function update(int $id): void
    {
        $course = $this->repo->findById($id);
        if (!$course) {
            header('Location: /curso');
            exit;
        }
        $data = filter_input_array(INPUT_POST, [
            'title'               => FILTER_SANITIZE_SPECIAL_CHARS,
            'short_description'   => FILTER_SANITIZE_SPECIAL_CHARS,
            'long_description'    => FILTER_DEFAULT,
            'category_id'         => FILTER_VALIDATE_INT,
            'status'              => FILTER_SANITIZE_SPECIAL_CHARS,
            'cover_image'         => FILTER_SANITIZE_URL,
            'course_requirements' => FILTER_DEFAULT,
            'learning_objectives' => FILTER_DEFAULT,
            'enrollment_method'   => FILTER_SANITIZE_SPECIAL_CHARS,
        ]);

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
    }

    // Deleta um curso
    public function delete(int $id): void
    {
        $this->repo->delete($id);
        header('Location: /curso');
        exit;
    }

    // Exibe detalhes de um curso (incrementa clickThroughs)
    // src/Controller/pages/CourseController.php

public function show(int $id): void
{
    $course = $this->repo->findById($id);
    if (!$course) {
        header('Location: /curso');
        exit;
    }

    // Incrementa métrica de clique
    $course->incrementClickThroughs();
    $this->repo->save($course);

    // Número de membros
    $memberCount = $this->memberRepo->countByCourseId($id);
    $course->setMemberCount($memberCount); // Adiciona o número de membros ao objeto

    // Exemplo: pega o usuário logado (supondo que você guarde em session)
    $currentUserId = $_SESSION['user_id'] ?? null;

    // Verifica se o usuário logado já é membro
    $isMember = false;
    if ($currentUserId) {
        $isMember = $this->memberRepo->exists($id, $currentUserId);
    }

    // Renderiza a página de detalhes do curso
    echo $this->twig->render('curso/show.twig', [
        'course'    => $course,
        'isMember'  => $isMember,
        'userId'    => $currentUserId,
    ]);
}


}
