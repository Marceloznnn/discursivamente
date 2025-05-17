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
    public function toggle(int|string $materialId): void
    {
        $materialId = (int) $materialId;
        header('Content-Type: application/json');
        try {
            if (!isset($_SESSION['user']['id'])) {
                http_response_code(401);
                echo json_encode([
                    'error' => 'Usuário não autenticado',
                    'completed' => false
                ]);
                exit;
            }
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

            echo json_encode([
                'materialId' => $materialId,
                'completed'  => !$exists
            ]);
            exit;
        } catch (\Throwable $e) {
            http_response_code(500);
            echo json_encode([
                'error' => 'Erro interno ao atualizar progresso',
                'completed' => false
            ]);
            exit;
        }
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
