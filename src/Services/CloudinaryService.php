<?php
// filepath: c:/xampp/htdocs/Discursivamente2.1/src/Services/CloudinaryService.php

namespace Services;

use Cloudinary\Cloudinary;
use Cloudinary\Configuration\Configuration;
use Config\UploadConfig;
use Config\Logger;

class CloudinaryService
{ 
    private Cloudinary $cloudinaryImages;
    private Cloudinary $cloudinaryVideos;
    
    public function __construct()
    {
        // Inicializar o sistema de log
        if (class_exists('\\Config\\Logger')) {
            try {
                Logger::init();
            } catch (\Exception $e) {
                // Silenciar erro de inicialização do logger
                error_log("Erro ao inicializar o logger: " . $e->getMessage());
            }
        }
        
        // Conta para imagens e raw
        $this->cloudinaryImages = new Cloudinary(
            Configuration::instance([
                'cloud' => [
                    'cloud_name' => $_ENV['CLOUDINARY_CLOUD_NAME'] ?? '',
                    'api_key'    => $_ENV['CLOUDINARY_API_KEY'] ?? '',
                    'api_secret' => $_ENV['CLOUDINARY_API_SECRET'] ?? '',
                ],
                'url' => ['secure' => true]
            ])
        );

        // Conta dedicada a vídeos
        $this->cloudinaryVideos = new Cloudinary(
            Configuration::instance([
                'cloud' => [
                    'cloud_name' => $_ENV['CLOUDINARY2_CLOUD_NAME'] ?? '',
                    'api_key'    => $_ENV['CLOUDINARY2_API_KEY'] ?? '',
                    'api_secret' => $_ENV['CLOUDINARY2_API_SECRET'] ?? '',
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
        // PDFs e outros arquivos devem usar resource_type 'raw'
        return 'raw';
    }    /**
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
        
        // Adicionar opções específicas para aumentar limites
        $options['chunk_size'] = 20000000; // 20MB por chunk para arquivos grandes
        if ($fileType === 'video') {
            // Removido: opções de streaming_profile/eager que causam erro para vídeos comuns
            $options['timeout'] = 120000; // 120 segundos de timeout
        }        // Registrar a tentativa de upload no sistema de log
        if (class_exists('\\Config\\Logger')) {
            try {
                Logger::upload('Iniciando upload para Cloudinary', [
                    'arquivo' => basename($filePath),
                    'tipo' => $fileType,
                    'tamanho' => UploadConfig::formatBytes(filesize($filePath)),
                    'opções' => $options
                ]);
            } catch (\Exception $e) {
                error_log("Erro ao registrar log: " . $e->getMessage());
            }
        }
        
        // Iniciar contador de tempo
        $startTime = microtime(true);

        try {
            // Adicionar log de progresso para uploads grandes
            if (filesize($filePath) > 5 * 1024 * 1024) { // 5MB
                if (class_exists('\\Config\\Logger')) {
                    try {
                        Logger::upload('Upload grande detectado, usando configurações otimizadas', [
                            'tamanho' => UploadConfig::formatBytes(filesize($filePath))
                        ]);
                    } catch (\Exception $e) {
                        error_log("Erro ao registrar log: " . $e->getMessage());
                    }
                }
            }
            
            $result = $cloudinary->uploadApi()->upload($filePath, $options);
            $uploadTime = microtime(true) - $startTime;

            // Log do resultado do upload
            if (class_exists('\\Config\\Logger')) {
                try {
                    Logger::upload('Upload concluído com sucesso', [
                'arquivo' => basename($filePath),
                        'public_id' => $result['public_id'] ?? 'N/A',
                        'url' => $result['secure_url'] ?? 'N/A',
                        'tempo' => round($uploadTime, 2) . 's',
                        'tamanho' => UploadConfig::formatBytes(filesize($filePath)),
                        'formato' => $result['format'] ?? 'N/A'
                    ]);
                } catch (\Exception $e) {
                    error_log("Erro ao registrar log: " . $e->getMessage());
                }
            }

            return [
                'url' => $result['secure_url'],
                'public_id' => $result['public_id'],
                'resource_type' => $result['resource_type'],
                'format' => $result['format'] ?? '',
                'created_at' => $result['created_at'],
                'bytes' => $result['bytes'] ?? 0,
                'duration' => $uploadTime
            ];
        } catch (\Exception $e) {            $errorTime = microtime(true) - $startTime;
            
            // Log detalhado do erro
            if (class_exists('\\Config\\Logger')) {
                try {
                    Logger::error('Erro no upload para Cloudinary', [
                        'arquivo' => basename($filePath),
                        'erro' => $e->getMessage(),
                        'codigo' => $e->getCode(),
                        'tempo_decorrido' => round($errorTime, 2) . 's',
                        'tamanho_arquivo' => UploadConfig::formatBytes(filesize($filePath)),
                        'tipo' => $fileType,
                        'stack_trace' => $e->getTraceAsString()
                    ]);
                    
                    // Adicionar informações mais detalhadas sobre o erro para debug
                    if (strpos($e->getMessage(), 'timeout') !== false) {
                        Logger::error('Erro de timeout no upload', [
                            'sugestão' => 'Aumente os valores de max_execution_time e timeout no PHP'
                        ]);
                    }
                    
                    if (strpos($e->getMessage(), 'exceeds the limit') !== false) {
                        Logger::error('Arquivo excede o limite permitido', [
                            'sugestão' => 'Verifique os limites do Cloudinary e aumente-os se necessário'
                        ]);
                    }
                } catch (\Exception $loggerEx) {
                    error_log("Erro ao registrar log: " . $loggerEx->getMessage());
                }
            }
            
            throw $e;
        }
    }    /**
     * Faz upload detectando tipo automaticamente
     *
     * @param string      $filePath
     * @param string|null $folder
     * @param string|null $fileType
     * @return array
     */
    public function upload(string $filePath, ?string $folder = null, ?string $fileType = null): array
    {
        // Inicializar o logger
        if (class_exists('\\Config\\Logger')) {
            try {
                Logger::init();
            } catch (\Exception $e) {
                error_log("Erro ao inicializar o logger: " . $e->getMessage());
            }
        }
        
        try {
            if (!file_exists($filePath)) {
                $error = "Arquivo não encontrado: {$filePath}";
                if (class_exists('\\Config\\Logger')) {
                    try {
                        Logger::error($error);
                    } catch (\Exception $e) {
                        error_log("Erro ao registrar log: " . $e->getMessage());
                    }
                }
                throw new \Exception($error);
            }
            
            if (!$fileType) {
                $fileType = $this->determineFileType($filePath);
            }
            
            // Verificar tamanho do arquivo usando nosso sistema de configuração
            $mimeType = mime_content_type($filePath);
            $fileSize = filesize($filePath);
            $maxSize = UploadConfig::getMaxSizeForType($mimeType);            $phpMaxUpload = min(
                $this->convertToBytes(ini_get('upload_max_filesize')),
                $this->convertToBytes(ini_get('post_max_size'))
            );
            
            // Registrar informações sobre o upload
            if (class_exists('\\Config\\Logger')) {
                try {
                    Logger::upload('Tentativa de upload de arquivo', [
                        'arquivo' => basename($filePath),
                        'caminho' => $filePath,
                        'tipo_mime' => $mimeType,
                        'tipo_detectado' => $fileType,
                        'tamanho' => $fileSize,
                        'tamanho_formatado' => UploadConfig::formatBytes($fileSize),
                        'limite_app' => UploadConfig::formatBytes($maxSize),
                        'limite_php' => UploadConfig::formatBytes($phpMaxUpload)
                    ]);
                } catch (\Exception $e) {
                    error_log("Erro ao registrar log: " . $e->getMessage());
                }
            }
              // Verificar se o arquivo está dentro do limite do PHP
            if ($fileSize > $phpMaxUpload) {
                $error = sprintf(
                    "O arquivo excede o limite de upload do PHP (%s). Configure PHP upload_max_filesize e post_max_size para pelo menos %s.",
                    UploadConfig::formatBytes($phpMaxUpload),
                    UploadConfig::formatBytes($fileSize)
                );
                if (class_exists('\\Config\\Logger')) {
                    try {
                        Logger::error($error, [
                            'arquivo' => basename($filePath),
                            'tamanho' => UploadConfig::formatBytes($fileSize),
                            'limite_php' => UploadConfig::formatBytes($phpMaxUpload)
                        ]);
                    } catch (\Exception $e) {
                        error_log("Erro ao registrar log: " . $e->getMessage());
                    }
                }
                throw new \Exception($error);
            }
              // Verificar se o arquivo está dentro do limite da aplicação
            if ($fileSize > $maxSize) {
                $error = sprintf(
                    "O arquivo excede o tamanho máximo permitido de %s para o tipo %s.",
                    UploadConfig::formatBytes($maxSize),
                    $mimeType
                );
                if (class_exists('\\Config\\Logger')) {
                    try {
                        Logger::error($error, [
                            'arquivo' => basename($filePath),
                            'tamanho' => UploadConfig::formatBytes($fileSize),
                            'limite_app' => UploadConfig::formatBytes($maxSize)
                        ]);
                    } catch (\Exception $e) {
                        error_log("Erro ao registrar log: " . $e->getMessage());
                    }
                }                throw new \Exception($error);
            }
            
            // Se chegou aqui, o arquivo está dentro dos limites
            if (class_exists('\\Config\\Logger')) {
                try {
                    Logger::info('Arquivo aprovado para upload', [
                        'arquivo' => basename($filePath),
                        'tamanho' => UploadConfig::formatBytes($fileSize)
                    ]);
                } catch (\Exception $e) {
                    error_log("Erro ao registrar log: " . $e->getMessage());
                }
            }
            
            return $this->uploadFile($filePath, $fileType, $folder);
        } catch (\Exception $e) {
            if (class_exists('\\Config\\Logger')) {
                try {
                    Logger::error('Erro no processo de upload', [
                        'arquivo' => basename($filePath),
                        'erro' => $e->getMessage(),
                        'stack' => $e->getTraceAsString()
                    ]);
                } catch (\Exception $loggerEx) {
                    error_log("Erro ao registrar log: " . $loggerEx->getMessage());
                }
            }
            throw $e;
        }
    }
    
    /**
     * Converte string de tamanho (2M, 8M, 100M) para bytes
     * 
     * @param string $value
     * @return int
     */
    private function convertToBytes(string $value): int
    {
        $value = trim($value);
        $last = strtolower($value[strlen($value) - 1]);
        $value = (int) $value;
        
        switch ($last) {
            case 'g':
                $value *= 1024;
            case 'm':
                $value *= 1024;
            case 'k':
                $value *= 1024;
        }
        
        return $value;
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
