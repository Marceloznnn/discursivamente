<?php
// filepath: c:/xampp/htdocs/Discursivamente2.1/src/Services/CloudinaryService.php

namespace Services;

use Cloudinary\Cloudinary;
use Cloudinary\Configuration\Configuration;

class CloudinaryService
{
    private Cloudinary $cloudinaryImages;
    private Cloudinary $cloudinaryVideos;

    public function __construct()
    {
        // Conta para imagens e raw
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

        // Conta dedicada a vídeos
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
     * Retorna o nome da nuvem (cloud_name) para vídeos.
     *
     * @return string
     * @throws \Exception se não definido
     */
    public function getCloudName(): string
    {
        $cloudName = $_ENV['CLOUDINARY2_CLOUD_NAME'] ?? null;
        if (!$cloudName) {
            throw new \Exception("Variável de ambiente CLOUDINARY2_CLOUD_NAME não definida.");
        }
        return $cloudName;
    }

    /**
     * Detecta o tipo de arquivo pelo MIME
     *
     * @param string $filePath
     * @return string 'image', 'video' ou 'raw'
     */
    public function determineFileType(string $filePath): string
    {
        $mimeType = mime_content_type($filePath);
        if (str_starts_with($mimeType, 'image/')) {
            return 'image';
        }
        if (str_starts_with($mimeType, 'video/')) {
            return 'video';
        }
        return 'raw';
    }

    /**
     * Faz upload de um arquivo para o Cloudinary
     *
     * @param string      $filePath caminho local do arquivo
     * @param string      $fileType 'image', 'video' ou 'raw'
     * @param string|null $folder    pasta no Cloudinary
     * @return array
     * @throws \Exception em falha de upload
     */
    public function uploadFile(string $filePath, string $fileType, ?string $folder = null): array
    {
        $cloudinary = $fileType === 'video'
            ? $this->cloudinaryVideos
            : $this->cloudinaryImages;

        $options = ['resource_type' => $fileType];
        if ($folder) {
            $options['folder'] = $folder;
        }

        $result = $cloudinary->uploadApi()->upload($filePath, $options);

        // Log básico
        $logDir  = __DIR__ . '/../../../logs';
        if (!is_dir($logDir)) {
            mkdir($logDir, 0777, true);
        }
        file_put_contents(
            "$logDir/cloudinary_upload.log",
            sprintf("[%s] Uploaded %s public_id=%s\n", date('Y-m-d H:i:s'), $fileType, $result['public_id'] ?? 'N/A'),
            FILE_APPEND
        );

        return [
            'url'           => $result['secure_url'],
            'public_id'     => $result['public_id'],
            'resource_type' => $result['resource_type'],
            'format'        => $result['format'] ?? '',
            'created_at'    => $result['created_at'],
        ];
    }

    /**
     * Faz upload detectando tipo automaticamente
     *
     * @param string      $filePath
     * @param string|null $folder
     * @param string|null $fileType
     * @return array
     */
    public function upload(string $filePath, ?string $folder = null, ?string $fileType = null): array
    {
        if (!$fileType) {
            $fileType = $this->determineFileType($filePath);
        }
        return $this->uploadFile($filePath, $fileType, $folder);
    }

    /**
     * Exclui um arquivo do Cloudinary
     *
     * @param string $publicId
     * @param string $resourceType
     * @return bool
     */
    public function deleteFile(string $publicId, string $resourceType = 'image'): bool
    {
        $api    = $resourceType === 'video'
            ? $this->cloudinaryVideos->uploadApi()
            : $this->cloudinaryImages->uploadApi();
        $result = $api->destroy($publicId, ['resource_type' => $resourceType]);
        return isset($result['result']) && $result['result'] === 'ok';
    }
}
