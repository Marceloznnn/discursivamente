<?php
// filepath: src/Controller/pages/HomeController.php

namespace Controller\pages;

use Middleware\AuthMiddleware;
use Services\CloudinaryService;
use Repositories\EventRepository;
use Repositories\CourseRepository;
use Repositories\CourseParticipationRepository;
use Repositories\SupportChatRepository;
use Infrastructure\Database\Connection;
use Twig\Environment;

class HomeController
{
    private Environment $twig;
    private CloudinaryService $cloudinaryService;
    private SupportChatRepository $supportRepo;

    public function __construct(Environment $twig)
    {
        $this->twig             = $twig;
        $this->cloudinaryService = new CloudinaryService();

        // Inicializa o repositório de chat de suporte
        $pdo = Connection::getInstance();
        $this->supportRepo = new SupportChatRepository($pdo);
    }

    /**
     * Exibe a página inicial com eventos, cursos e chat de suporte
     */
    public function index(): void
    {
        $currentUser = $_SESSION['user'] ?? null;

        $connection = Connection::getInstance();

        // 1) Eventos em destaque
        $eventRepository = new EventRepository($connection);
        $allEvents       = $eventRepository->findAll();
        $featured        = array_filter($allEvents, fn($e) => $e->getIsFeatured());
        usort($featured, fn($a, $b) => $b->getFeaturePriority() <=> $a->getFeaturePriority());
        $topFeatured     = array_slice($featured, 0, 4);

        // 2) Cursos mais populares
        $courseRepo        = new CourseRepository($connection);
        $participationRepo = new CourseParticipationRepository($connection);

        $allCourses = $courseRepo->findAll();
        $coursesWithCount = array_map(function($course) use ($participationRepo) {
            $course->setActiveCount($participationRepo->countActiveByCourse($course->getId()));
            return $course;
        }, $allCourses);
        usort($coursesWithCount, fn($a, $b) => $b->getActiveCount() <=> $a->getActiveCount());
        $popularCourses = array_slice($coursesWithCount, 0, 3);

        // 3) Chat de suporte — histórico do usuário
        $supportChatId   = null;
        $supportMessages = [];
        if ($currentUser) {
            $userId         = (int) $currentUser['id'];
            $supportChatId   = 'support_user_' . $userId;
            // Busca o histórico completo do chat (usuário + admin)
            $supportMessages = $this->supportRepo->getMessagesByChatId($supportChatId);
        }

        // Renderiza a view
        echo $this->twig->render('home/index.twig', [
            'featuredEvents'  => $topFeatured,
            'recentUploads'   => $this->getRecentUploads(),
            'popularCourses'  => $popularCourses,
            'currentUser'     => $currentUser,
            'supportChatId'   => $supportChatId,
            'supportMessages' => $supportMessages,
            'supportWsUrl'    => 'ws://localhost:8081'
        ]);
    }
  
    /** 
     * Faz upload de mídia para o Cloudinary
     *
     * @param array $file $_FILES['media']
     * @param string $folder
     * @return array|false
     */
    public function uploadMedia(array $file, string $folder = 'home')
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
     * Processa o POST de upload de mídia
     */
    public function processUpload(): void
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
     * @param string $publicId
     * @param string|null $fileType
     * @return bool
     */
    public function deleteMedia(string $publicId, ?string $fileType = null): bool
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
     * Registra em banco os dados de upload
     */
    private function logUpload(array $uploadData, string $originalFilename): void
    {
        // implementação opcional para gravar em tabela media_uploads
    }

    /**
     * Remove registro de upload do banco
     */
    private function removeUploadLog(string $publicId): void
    {
        // implementação opcional para remover de media_uploads
    }

    /**
     * Retorna uploads recentes (ex.: galeria da home)
     *
     * @param int $limit
     * @return array
     */
    private function getRecentUploads(int $limit = 6): array
    {
        // exemplo de implementação:
        // $pdo = Connection::getInstance();
        // $stmt = $pdo->prepare("SELECT * FROM media_uploads ORDER BY created_at DESC LIMIT ?");
        // $stmt->execute([$limit]);
        // return $stmt->fetchAll(\PDO::FETCH_ASSOC);

        return [];
    }
}
