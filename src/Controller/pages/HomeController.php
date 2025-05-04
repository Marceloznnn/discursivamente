<?php
// filepath: c:\xampp\htdocs\Discursivamente2.1\src\Controller\pages\HomeController.php

namespace Controller\pages;

use Services\CloudinaryService;
use Repositories\EventRepository;
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
        // a) Conexão e repositório de eventos
        $connection      = Connection::getInstance();
        $eventRepository = new EventRepository($connection);

        // b) Busca todos os eventos, filtra apenas os futuros e pega os 4 mais próximos
        $allEvents = $eventRepository->findAll();

        $now = new \DateTime();
        $upcoming = array_filter($allEvents, function($e) use ($now) {
            return new \DateTime($e->getDateTime()) >= $now;
        });

        // Ordena por data ascendente (caso findAll não faça isso)
        usort($upcoming, function($a, $b) {
            return (new \DateTime($a->getDateTime())) <=> (new \DateTime($b->getDateTime()));
        });

        // Limita a X eventos (aqui 4, mas ajuste como quiser)
        $recentEvents = array_slice($upcoming, 0, 4);

        // c) Renderiza passando também recentEvents
        echo $this->twig->render('home/index.twig', [
            'recentUploads' => $this->getRecentUploads(),
            'recentEvents'  => $recentEvents,
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
            // Pasta temporária para armazenar o arquivo
            $tmpPath = $file['tmp_name'];
            
            // Realiza o upload para o Cloudinary
            $result = $this->cloudinaryService->upload($tmpPath, $folder);
            
            // Registra o upload em banco de dados se necessário
            $this->logUpload($result, $file['name']);
            
            return $result;
        } catch (\Exception $e) {
            // Log do erro
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
                'type' => 'success', 
                'message' => 'Arquivo enviado com sucesso!'
            ];
            
            // Adiciona a URL do arquivo na sessão para ser exibida na view
            $_SESSION['uploaded_file'] = $result['url'];
        } else {
            $_SESSION['flash_message'] = [
                'type' => 'error', 
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
     * @param string $fileType Tipo do arquivo ('image' ou 'video')
     * @return bool Sucesso da operação
     */
    public function deleteMedia($publicId, $fileType = null)
    {
        if (!$publicId) {
            return false;
        }
        
        // Se o tipo de arquivo não foi especificado, tenta determinar
        if (!$fileType) {
            // Verifica a extensão do ID público para inferir o tipo
            $extension = pathinfo($publicId, PATHINFO_EXTENSION);
            $fileType = $this->cloudinaryService->determineFileType("dummy.$extension");
        }
        
        try {
            $result = $this->cloudinaryService->deleteFile($publicId, $fileType);
            
            // Remove o registro do upload do banco de dados se necessário
            if ($result['result'] === 'ok') {
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
     * 
     * @param array $uploadData Dados retornados pelo Cloudinary
     * @param string $originalFilename Nome original do arquivo
     */
    private function logUpload($uploadData, $originalFilename)
    {
        // Implementação para salvar o registro em banco de dados
        // Exemplo:
        /*
        $connection = Connection::getInstance();
        $stmt = $connection->prepare(
            "INSERT INTO media_uploads (public_id, url, resource_type, original_filename, created_at) 
             VALUES (?, ?, ?, ?, NOW())"
        );
        $stmt->execute([
            $uploadData['public_id'],
            $uploadData['url'],
            $uploadData['resource_type'],
            $originalFilename
        ]);
        */
    }
    
    /**
     * Remove o registro de upload do banco de dados
     * 
     * @param string $publicId ID público do arquivo
     */
    private function removeUploadLog($publicId)
    {
        // Implementação para remover o registro do banco de dados
        // Exemplo:
        /*
        $connection = Connection::getInstance();
        $stmt = $connection->prepare("DELETE FROM media_uploads WHERE public_id = ?");
        $stmt->execute([$publicId]);
        */
    }
    
    /**
     * Recupera uploads recentes do banco de dados
     * 
     * @param int $limit Quantidade de itens a retornar
     * @return array Lista de uploads recentes
     */
    private function getRecentUploads($limit = 6)
    {
        // Implementação para buscar uploads recentes
        // Exemplo:
        /*
        $connection = Connection::getInstance();
        $stmt = $connection->prepare(
            "SELECT * FROM media_uploads ORDER BY created_at DESC LIMIT ?"
        );
        $stmt->execute([$limit]);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        */
        
        // Retorna um array vazio como padrão
        return [];
    }
}