<?php
// Sistema de configuração global para o Discursivamente2.1
// Todos os limites e configurações relacionadas a uploads devem ser centralizados aqui

namespace Config;

class UploadConfig
{    // Limites de tamanho para uploads (em bytes)
    const MAX_IMAGE_SIZE = 50 * 1024 * 1024; // 50MB
    const MAX_VIDEO_SIZE = 200 * 1024 * 1024; // 200MB
    const MAX_AUDIO_SIZE = 100 * 1024 * 1024; // 100MB
    const MAX_PDF_SIZE = 50 * 1024 * 1024; // 50MB
    const MAX_OTHER_SIZE = 30 * 1024 * 1024; // 30MB

    // Tipos de arquivos permitidos
    const ALLOWED_IMAGE_TYPES = ['image/jpeg', 'image/png', 'image/webp', 'image/gif', 'image/svg+xml'];
    const ALLOWED_VIDEO_TYPES = ['video/mp4', 'video/webm', 'video/ogg'];
    const ALLOWED_AUDIO_TYPES = ['audio/mpeg', 'audio/ogg', 'audio/wav', 'audio/x-wav'];
    const ALLOWED_DOCUMENT_TYPES = [
        'application/pdf', 
        'application/msword', 
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        'application/vnd.ms-excel',
        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        'application/vnd.ms-powerpoint',
        'application/vnd.openxmlformats-officedocument.presentationml.presentation',
        'text/plain'
    ];

    /**
     * Retorna o tamanho máximo em bytes para o tipo de arquivo especificado
     */
    public static function getMaxSizeForType(string $mimeType): int
    {
        if (in_array($mimeType, self::ALLOWED_IMAGE_TYPES)) {
            return self::MAX_IMAGE_SIZE;
        } elseif (in_array($mimeType, self::ALLOWED_VIDEO_TYPES)) {
            return self::MAX_VIDEO_SIZE;
        } elseif (in_array($mimeType, self::ALLOWED_AUDIO_TYPES)) {
            return self::MAX_AUDIO_SIZE;
        } elseif (in_array($mimeType, self::ALLOWED_DOCUMENT_TYPES)) {
            return self::MAX_PDF_SIZE;
        } else {
            return self::MAX_OTHER_SIZE;
        }
    }

    /**
     * Verifica se o arquivo está dentro dos limites de tamanho permitidos
     */
    public static function validateFileSize(string $tempFilePath, string $mimeType): bool
    {
        $fileSize = filesize($tempFilePath);
        $maxSize = self::getMaxSizeForType($mimeType);
        return $fileSize <= $maxSize;
    }

    /**
     * Retorna o tamanho máximo formatado para exibição
     */
    public static function getFormattedMaxSize(string $mimeType): string
    {
        $maxSize = self::getMaxSizeForType($mimeType);
        return self::formatBytes($maxSize);
    }

    /**
     * Formata bytes para unidades legíveis
     */
    public static function formatBytes(int $bytes, int $precision = 2): string
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= pow(1024, $pow);
        return round($bytes, $precision) . ' ' . $units[$pow];
    }
}
