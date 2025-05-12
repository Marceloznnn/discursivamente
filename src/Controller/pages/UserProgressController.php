<?php
// src/Controller/pages/UserProgressController.php

namespace Controller\pages;

use Repositories\UserProgressRepository;
use Repositories\MaterialRepository;
use PDO;
use Twig\Environment;

class UserProgressController
{
    private Environment $twig;
    private UserProgressRepository $progressRepo;
    private MaterialRepository $materialRepo;

    public function __construct(Environment $twig, PDO $pdo)
    {
        $this->twig         = $twig;
        $this->progressRepo = new \Repositories\UserProgressRepository($pdo);
        $this->materialRepo = new MaterialRepository($pdo);
    }

    /**
     * Marca ou desmarca um material como concluído via POST (pode ser AJAX).
     */
    public function toggle(int $materialId): void
    {
        $userId = $_SESSION['user']['id'];
        $exists = $this->progressRepo->find($userId, $materialId);

        if ($exists) {
            // já concluído → desmarca
            $this->progressRepo->delete($userId, $materialId);
        } else {
            // marca como concluído
            $progress = new \App\Models\UserProgress($userId, $materialId, date('Y-m-d H:i:s'));
            $this->progressRepo->save($progress);
        }

        // Se for AJAX, retorne JSON com status; senão, redirect de volta
        if (!empty($_SERVER['HTTP_X_REQUESTED_WITH'])) {
            header('Content-Type: application/json');
            echo json_encode([
                'materialId' => $materialId,
                'completed'  => $exists ? false : true
            ]);
        } else {
            header("Location: " . $_SERVER['HTTP_REFERER']);
        }
        exit;
    }

    /**
     * Exibe o progresso do usuário num curso, se quiser ter uma página dedicada.
     */
    public function showCourseProgress(int $courseId): void
    {
        $userId    = $_SESSION['user']['id'];
        $materials = $this->materialRepo->findByCourseId($courseId);
        $doneList  = array_map(
            fn($p) => $p->getMaterialId(),
            $this->progressRepo->findByUserId($userId)
        );

        echo $this->twig->render('user/progress/show.twig', [
            'materials' => $materials,
            'doneList'  => $doneList,
            'courseId'  => $courseId,
        ]);
    }
}
