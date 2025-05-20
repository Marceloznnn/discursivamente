<?php
// filepath: c:\xampp\htdocs\Discursivamente2.1\src\Services\CloudinaryService.php

namespace Services;

use Cloudinary\Cloudinary;
use Cloudinary\Configuration\Configuration;

class CloudinaryService {
    private Cloudinary $cloudinaryImages;
    private Cloudinary $cloudinaryVideos;

    public function __construct() {
        // Configuração para a primeira conta (imagens e raw)
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
     * @param string $fileType Tipo do arquivo ('image', 'video' ou 'raw')
     * @param string|null $folder Pasta onde o arquivo será armazenado
     * @return array Resposta do Cloudinary contendo URL, etc.
     * @throws \Exception em caso de erro
     */
    public function uploadFile(string $filePath, string $fileType, ?string $folder = null): array {
        $cloudinary = match($fileType) {
            'video' => $this->cloudinaryVideos,
            default => $this->cloudinaryImages,
        };

        $options = ['resource_type' => $fileType];
        if ($folder) {
            $options['folder'] = $folder;
        }

        $result = $cloudinary->uploadApi()->upload($filePath, $options);

        // Sempre usa a secure_url para download
        $url = $result['secure_url'];

        // LOG para depuração
        $logDir = __DIR__ . '/../../../logs';
        if (!is_dir($logDir)) { mkdir($logDir, 0777, true); }
        $logFile = $logDir . '/cloudinary_upload.log';
        $logMsg = date('Y-m-d H:i:s') . "\n" .
            "Tipo: $fileType\n" .
            "Public ID: " . ($result['public_id'] ?? 'N/A') . "\n" .
            "Format: " . ($result['format'] ?? 'N/A') . "\n" .
            "Secure URL: " . ($result['secure_url'] ?? 'N/A') . "\n" .
            "URL Inline: (não usada)\n" .
            str_repeat('-', 40) . "\n";
        file_put_contents($logFile, $logMsg, FILE_APPEND);

        return [
            'url'           => $url,
            'public_id'     => $result['public_id'],
            'resource_type' => $result['resource_type'],
            'format'        => $result['format'] ?? 'pdf',
            'created_at'    => $result['created_at'],
        ];
    }

    /**
     * Determina o tipo de arquivo baseado no MIME
     *
     * @param string $filePath Caminho do arquivo temporário
     * @return string 'image', 'video' ou 'raw'
     */
    public function determineFileType(string $filePath): string {
        $mimeType = mime_content_type($filePath);

        if (str_starts_with($mimeType, 'image/')) {
            return 'image';
        }
        if (str_starts_with($mimeType, 'video/')) {
            return 'video';
        }
        return 'raw'; // PDFs e outros tipos
    }

    /**
     * Faz upload detectando tipo automaticamente se não informado
     *
     * @param string $filePath Caminho do arquivo temporário
     * @param string|null $folder Pasta de destino
     * @param string|null $fileType Se já souber ('image', 'video' ou 'raw')
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
     * @param string $resourceType ('image', 'video' ou 'raw')
     * @return array Resultado da operação
     */
    public function deleteFile(string $publicId, string $resourceType = 'image'): array {
        $api = $this->cloudinaryImages->uploadApi();
        if ($resourceType === 'video') {
            $api = $this->cloudinaryVideos->uploadApi();
        }

        $result = $api->destroy($publicId, ['resource_type' => $resourceType]);
        return ['result' => $result['result']];
    }
}
