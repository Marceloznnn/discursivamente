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
                    'api_key'    => $_ENV['CLOUDINARY_API_KEY'],
                    'api_secret' => $_ENV['CLOUDINARY_API_SECRET'],
                ],
                'url' => ['secure' => true]
            ])
        );

        // Configuração para a segunda conta (vídeos)
        $this->cloudinaryVideos = new Cloudinary(
            Configuration::instance([
                'cloud' => [
                    'cloud_name' => $_ENV['CLOUDINARY2_CLOUD_NAME'],
                    'api_key'    => $_ENV['CLOUDINARY2_API_KEY'],
                    'api_secret' => $_ENV['CLOUDINARY2_API_SECRET'],
                ],
                'url' => ['secure' => true]
            ])
        );
    }

    /**
     * Faz upload de um arquivo para o Cloudinary
     *
     * @param string $filePath Caminho do arquivo
     * @param string $fileType Tipo do arquivo ('image' ou 'video')
     * @param string|null $folder Pasta onde o arquivo será armazenado
     * @return array Resposta do Cloudinary contendo URL, etc.
     * @throws \Exception em caso de erro
     */
    public function uploadFile(string $filePath, string $fileType, ?string $folder = null): array {
        error_log("[CloudinaryService] uploadFile called: filePath={$filePath}, fileType={$fileType}, folder={$folder}");

        // Escolhe instância apropriada
        $cloudinary = ($fileType === 'video')
            ? $this->cloudinaryVideos
            : $this->cloudinaryImages;

        $options = ['resource_type' => $fileType];
        if ($folder) {
            $options['folder'] = $folder;
        }

        try {
            $result = $cloudinary->uploadApi()->upload($filePath, $options);
            error_log("[CloudinaryService] upload successful: " . print_r($result, true));

            return [
                'url'           => $result['secure_url'],
                'public_id'     => $result['public_id'],
                'resource_type' => $result['resource_type'],
                'format'        => $result['format'],
                'created_at'    => $result['created_at'],
            ];
        } catch (\Exception $e) {
            error_log("[CloudinaryService] upload failed: " . $e->getMessage() . " | options: " . print_r($options, true));
            throw $e;
        }
    }

    /**
     * Determina o tipo de arquivo baseado no MIME (mais confiável que extensão tmp)
     *
     * @param string $filePath Caminho do arquivo temporário
     * @return string 'image' ou 'video'
     */
    public function determineFileType(string $filePath): string {
        $mimeType = mime_content_type($filePath);
        error_log("[CloudinaryService] determineFileType: mimeType={$mimeType}");

        if (str_starts_with($mimeType, 'video/')) {
            return 'video';
        }

        // padrão: trata tudo como imagem
        return 'image';
    }

    /**
     * Faz upload detectando tipo automaticamente se não informado
     *
     * @param string $filePath Caminho do arquivo temporário
     * @param string|null $folder Pasta de destino
     * @param string|null $fileType Se já souber ('image' ou 'video')
     * @return array
     */
    public function upload(string $filePath, ?string $folder = null, ?string $fileType = null): array {
        if (!$fileType) {
            $fileType = $this->determineFileType($filePath);
        }
        return $this->uploadFile($filePath, $fileType, $folder);
    }

    /**
     * Deleta um arquivo do Cloudinary
     *
     * @param string $publicId ID público
     * @return array Resultado da operação
     */
    public function deleteFile(string $publicId): array {
        try {
            $result = $this->cloudinaryImages->uploadApi()->destroy($publicId);
            return ['result' => $result['result']];
        } catch (\Exception $e) {
            error_log("[CloudinaryService] deleteFile failed: " . $e->getMessage());
            return [
                'result'  => 'error',
                'message' => $e->getMessage(),
            ];
        }
    }
}
