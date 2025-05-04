<?php
// filepath: c:\xampp\htdocs\Discursivamente2.1\src\Services\CloudinaryService.php

namespace Services;

use Cloudinary\Cloudinary;
use Cloudinary\Configuration\Configuration;

class CloudinaryService {
    private $cloudinaryImages;
    private $cloudinaryVideos;

    public function __construct() {
        // Configuração para a primeira conta (imagens)
        $this->cloudinaryImages = new Cloudinary(
            Configuration::instance([
                'cloud' => [
                    'cloud_name' => $_ENV['CLOUDINARY_CLOUD_NAME'],
                    'api_key' => $_ENV['CLOUDINARY_API_KEY'],
                    'api_secret' => $_ENV['CLOUDINARY_API_SECRET']
                ],
                'url' => [
                    'secure' => true
                ]
            ])
        );

        // Configuração para a segunda conta (vídeos)
        $this->cloudinaryVideos = new Cloudinary(
            Configuration::instance([
                'cloud' => [
                    'cloud_name' => $_ENV['CLOUDINARY2_CLOUD_NAME'],
                    'api_key' => $_ENV['CLOUDINARY2_API_KEY'],
                    'api_secret' => $_ENV['CLOUDINARY2_API_SECRET']
                ],
                'url' => [
                    'secure' => true
                ]
            ])
        );
    }

    /**
     * Faz upload de um arquivo para o Cloudinary
     * 
     * @param string $filePath Caminho do arquivo
     * @param string $fileType Tipo do arquivo ('image' ou 'video')
     * @param string $folder Pasta onde o arquivo será armazenado no Cloudinary
     * @return array Resposta do Cloudinary contendo a URL e outros dados
     */
    public function uploadFile($filePath, $fileType, $folder = null) {
        // Determina qual instância do Cloudinary usar com base no tipo de arquivo
        $cloudinary = ($fileType === 'video') ? $this->cloudinaryVideos : $this->cloudinaryImages;
        
        $options = [
            'resource_type' => $fileType
        ];
        
        if ($folder) {
            $options['folder'] = $folder;
        }
        
        // Realiza o upload
        $result = $cloudinary->uploadApi()->upload($filePath, $options);

        // Agora, extrai os dados relevantes e retorna como array
        return [
            'url' => $result['secure_url'],  // URL segura
            'public_id' => $result['public_id'],  // ID público do arquivo
            'resource_type' => $result['resource_type'],  // Tipo de recurso (imagem ou vídeo)
            'format' => $result['format'],  // Formato do arquivo (por exemplo, jpg, mp4)
            'created_at' => $result['created_at']  // Data de criação
        ];
    }
    
    /**
     * Determina automaticamente o tipo de arquivo baseado na extensão
     * 
     * @param string $filePath Caminho do arquivo
     * @return string Tipo do arquivo ('image' ou 'video')
     */
    public function determineFileType($filePath) {
        $extension = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
        
        $videoExtensions = ['mp4', 'avi', 'mov', 'wmv', 'flv', 'mkv', 'webm'];
        
        if (in_array($extension, $videoExtensions)) {
            return 'video';
        }
        
        return 'image';
    }
    
    /**
     * Faz upload de um arquivo automaticamente detectando seu tipo
     * 
     * @param string $filePath Caminho do arquivo
     * @param string $folder Pasta onde o arquivo será armazenado no Cloudinary
     * @return array Resposta do Cloudinary contendo a URL e outros dados
     */
    public function upload($filePath, $folder = null) {
        $fileType = $this->determineFileType($filePath);
        return $this->uploadFile($filePath, $fileType, $folder);
    }
    
    /**
     * Deleta um arquivo do Cloudinary
     * 
     * @param string $publicId ID público do arquivo no Cloudinary
     * @param string $fileType Tipo do arquivo ('image' ou 'video')
     * @return array Resposta do Cloudinary
     */
    public function deleteFile($publicId, $fileType = 'image') {
        $cloudinary = ($fileType === 'video') ? $this->cloudinaryVideos : $this->cloudinaryImages;
        
        // Realiza a exclusão
        $result = $cloudinary->uploadApi()->destroy($publicId, [
            'resource_type' => $fileType
        ]);

        // Extrai a resposta e retorna como array
        return [
            'result' => $result['result'],  // Retorna 'ok' ou 'not found'
            'public_id' => $result['public_id'],  // ID do arquivo excluído
        ];
    }
}
