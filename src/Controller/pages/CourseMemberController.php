<?php
// src/Controller/pages/CourseMemberController.php
namespace Controller\pages;

use Infrastructure\Database\Connection;
use Repositories\CourseMemberRepository;
use Repositories\CourseRepository;
use App\Models\CourseMember;
use Twig\Environment;

class CourseMemberController
{
    private Environment $twig;
    private CourseMemberRepository $memberRepo;
    private CourseRepository $courseRepo;

    public function __construct(Environment $twig)
    {
        $this->twig       = $twig;
        $pdo              = Connection::getInstance();
        $this->memberRepo = new CourseMemberRepository($pdo);
        $this->courseRepo = new CourseRepository($pdo);
    }

    public function index(int $courseId): void
    {
        $course = $this->courseRepo->findById($courseId);
        if (!$course) {
            header('Location: /curso');
            exit;
        }

        // NÃO use $this->pdo aqui — já tem o repo
        $members = $this->memberRepo->findByCourse($courseId);

        echo $this->twig->render('curso/membros/index.twig', [
            'course'  => $course,
            'members' => $members,
        ]);
    }

    public function addForm(int $courseId): void
    {
        echo $this->twig->render('curso/membros/form.twig', [
            'courseId' => $courseId,
            'member'   => null,
        ]);
    }

    public function store(int $courseId): void
    {
        $data = filter_input_array(INPUT_POST, [
            'user_id' => FILTER_VALIDATE_INT,
            'role'    => FILTER_SANITIZE_SPECIAL_CHARS,
        ]);

        if (!$data['user_id'] || !$data['role']) {
            header("Location: /curso/{$courseId}/membros");
            exit;
        }

        $member = new CourseMember(
            courseId:        $courseId,
            userId:          $data['user_id'],
            role:            $data['role'],
        );

        $this->memberRepo->save($member);
        header("Location: /curso/{$courseId}/membros");
        exit;
    }

    public function delete(int $courseId, int $memberId): void
    {
        $this->memberRepo->delete($memberId);
        header("Location: /curso/{$courseId}/membros");
        exit;
    }
}
