<?php
// src/Repositories/CourseRepository.php
namespace Repositories;

use App\Models\Course;
use PDO;

class CourseRepository
{
    public function __construct(private PDO $pdo) {}

    public function findAll(): array
    {
        $stmt = $this->pdo->query("SELECT * FROM course_main ORDER BY created_at DESC");
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return array_map(fn($r) => $this->hydrate($r), $rows);
    }

    public function findById(int $id): ?Course
    {
        $stmt = $this->pdo->prepare("SELECT * FROM course_main WHERE id = ?");
        $stmt->execute([$id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ? $this->hydrate($row) : null;
    }

    public function save(Course $course): void
    {
        if ($course->getId() === null) {
            $sql = "INSERT INTO course_main
                (title, short_description, long_description, category_id, creator_id, status,
                 cover_image, course_requirements, learning_objectives, enrollment_method,
                 search_hits, click_throughs)
             VALUES (?,?,?,?,?,?,?,?,?,?,?,?)";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([
                $course->getTitle(),
                $course->getShortDescription(),
                $course->getLongDescription(),
                $course->getCategoryId(),
                $course->getCreatorId(),
                $course->getStatus(),
                $course->getCoverImage(),
                $course->getCourseRequirements(),
                $course->getLearningObjectives(),
                $course->getEnrollmentMethod(),
                $course->getSearchHits(),
                $course->getClickThroughs(),
            ]);
            $courseId = (int)$this->pdo->lastInsertId();
            $course->setId($courseId);
        } else {
            $sql = "UPDATE course_main SET
                title = ?, short_description = ?, long_description = ?, category_id = ?, status = ?,
                cover_image = ?, course_requirements = ?, learning_objectives = ?, enrollment_method = ?,
                search_hits = ?, click_throughs = ?
             WHERE id = ?";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([
                $course->getTitle(),
                $course->getShortDescription(),
                $course->getLongDescription(),
                $course->getCategoryId(),
                $course->getStatus(),
                $course->getCoverImage(),
                $course->getCourseRequirements(),
                $course->getLearningObjectives(),
                $course->getEnrollmentMethod(),
                $course->getSearchHits(),
                $course->getClickThroughs(),
                $course->getId(),
            ]);
        }
    }

    public function delete(int $id): void
    {
        $stmt = $this->pdo->prepare("DELETE FROM course_main WHERE id = ?");
        $stmt->execute([$id]);
    }
    

    private function hydrate(array $data): Course
    {
        return new Course(
            $data['title'],
            $data['short_description'],
            (int)$data['creator_id'],
            $data['status'],
            $data['enrollment_method'],
            $data['category_id'] !== null ? (int)$data['category_id'] : null,
            $data['long_description'],
            $data['cover_image'],
            $data['course_requirements'],
            $data['learning_objectives'],
            (int)$data['id'],
            new \DateTime($data['created_at']),
            new \DateTime($data['updated_at']),
            (int)$data['search_hits'],
            (int)$data['click_throughs']
        );
    }
}
