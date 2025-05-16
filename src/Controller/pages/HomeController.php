<?php
// filepath: src/Controller/pages/HomeController.php

namespace Controller\pages;

use Services\CloudinaryService;
use Repositories\EventRepository;
use Repositories\CourseRepository;
use Repositories\CourseParticipationRepository;
use Infrastructure\Database\Connection;

class HomeController {
    private $twig;
    private $cloudinaryService;

    public function __construct($twig)
    {
        $this->twig = $twig;
        $this->cloudinaryService = new CloudinaryService();
    }

    public function index()
    {
        $connection = Connection::getInstance();

        // Eventos em destaque
        $eventRepository = new EventRepository($connection);
        $allEvents       = $eventRepository->findAll();
        $featured        = array_filter($allEvents, fn($e) => $e->getIsFeatured());
        usort($featured, fn($a, $b) => $b->getFeaturePriority() <=> $a->getFeaturePriority());
        $topFeatured     = array_slice($featured, 0, 4);

        // Cursos mais populares
        $courseRepo        = new CourseRepository($connection);
        $participationRepo = new CourseParticipationRepository($connection);

        $allCourses = $courseRepo->findAll();

        $coursesWithCount = array_map(function($course) use ($participationRepo) {
            $count = $participationRepo->countActiveByCourse($course->getId());
            $course->activeCount = $count;
            return $course;
        }, $allCourses);

        usort($coursesWithCount, fn($a, $b) => $b->activeCount <=> $a->activeCount);
        $popularCourses = array_slice($coursesWithCount, 0, 3);

        // Renderiza a view
        echo $this->twig->render('home/index.twig', [
            'featuredEvents' => $topFeatured,
            'recentUploads'  => $this->getRecentUploads(),
            'popularCourses' => $popularCourses
        ]);
    }

    /**
     * Faz upload de imagem ou vídeo para o Cloudinary
     *
     * @param array $file Arquivo enviado via $_FILES
     * @param string $folder Pasta onde será armazenado (opcional)
     * @return array|false Dados do upload ou false em caso de erro
     */
    public function uploadMedia($file, $folder = 'home')
    {
        if (!isset($file) || $file['error'] !== UPLOAD_ERR_OK) {
            return false;
        }

        try {
            $tmpPath = $file['tmp_name'];
            $result  = $this->cloudinaryService->upload($tmpPath, $folder);
            $this->logUpload($result, $file['name']);
            return $result;
        } catch (\Exception $e) {
            error_log('Erro ao fazer upload para o Cloudinary: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Processa formulário de upload
     */
    public function processUpload()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /');
            exit;
        }

        $result = false;
        if (isset($_FILES['media'])) {
            $result = $this->uploadMedia($_FILES['media']);
        }

        if ($result) {
            $_SESSION['flash_message'] = [
                'type'    => 'success',
                'message' => 'Arquivo enviado com sucesso!'
            ];
            $_SESSION['uploaded_file'] = $result['url'];
        } else {
            $_SESSION['flash_message'] = [
                'type'    => 'error',
                'message' => 'Falha ao enviar o arquivo. Tente novamente.'
            ];
        }

        header('Location: /');
        exit;
    }

    /**
     * Exclui um arquivo do Cloudinary
     *
     * @param string $publicId ID público do arquivo
     * @param string|null $fileType Tipo do arquivo ('image' ou 'video')
     * @return bool
     */
    public function deleteMedia($publicId, $fileType = null)
    {
        if (!$publicId) {
            return false;
        }

        if (!$fileType) {
            $extension = pathinfo($publicId, PATHINFO_EXTENSION);
            $fileType  = $this->cloudinaryService->determineFileType("dummy.$extension");
        }

        try {
            $result = $this->cloudinaryService->deleteFile($publicId, $fileType);
            if (isset($result['result']) && $result['result'] === 'ok') {
                $this->removeUploadLog($publicId);
                return true;
            }
            return false;
        } catch (\Exception $e) {
            error_log('Erro ao excluir arquivo do Cloudinary: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Registra o upload em um banco de dados
     */
    private function logUpload(array $uploadData, string $originalFilename)
    {
        // implementação para salvar em banco, se desejar
    }

    /**
     * Remove o registro de upload do banco de dados
     */
    private function removeUploadLog(string $publicId)
    {
        // implementação para remover em banco, se desejar
    }

    /**
     * Recupera uploads recentes do banco de dados
     *
     * @param int $limit
     * @return array
     */
    private function getRecentUploads(int $limit = 6): array
    {
        // exemplo de implementação:
        // $connection = Connection::getInstance();
        // $stmt = $connection->prepare(
        //     "SELECT * FROM media_uploads ORDER BY created_at DESC LIMIT ?"
        // );
        // $stmt->execute([$limit]);
        // return $stmt->fetchAll(\PDO::FETCH_ASSOC);

        return [];
    }
}
