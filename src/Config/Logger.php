<?php
// Sistema de log para uploads e manipulação de arquivos no Discursivamente2.1

namespace Config;

class Logger
{
    const LOG_DIR = __DIR__ . '/../../logs';
    const LOG_FILE_UPLOAD = 'uploads.log';
    const LOG_FILE_ERROR = 'errors.log';
    const LOG_FILE_DEBUG = 'debug.log';

    /**
     * Inicializa o sistema de log
     */
    public static function init(): void
    {
        if (!is_dir(self::LOG_DIR)) {
            mkdir(self::LOG_DIR, 0777, true);
        }
    }

    /**
     * Registra uma mensagem de log
     *
     * @param string $message Mensagem a ser registrada
     * @param array $data Dados adicionais para o log
     * @param string $level Nível do log (info, error, debug)
     * @param string $file Arquivo de log
     */
    public static function log(string $message, array $data = [], string $level = 'info', ?string $file = null): void
    {
        self::init();

        if (!$file) {
            $file = match ($level) {
                'error' => self::LOG_FILE_ERROR,
                'debug' => self::LOG_FILE_DEBUG,
                default => self::LOG_FILE_UPLOAD,
            };
        }

        $logEntry = [
            'timestamp' => date('Y-m-d H:i:s'),
            'level' => strtoupper($level),
            'message' => $message,
            'data' => $data,
            'ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown',
            'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? 'unknown',
            'session_id' => session_id() ?: 'no-session'
        ];

        // Formato do log: [DATA] [NÍVEL] Mensagem: dados_json
        $logText = sprintf(
            "[%s] [%s] %s: %s\n",
            $logEntry['timestamp'],
            $logEntry['level'],
            $logEntry['message'],
            json_encode($logEntry['data'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)
        );

        file_put_contents(
            self::LOG_DIR . '/' . $file,
            $logText,
            FILE_APPEND
        );

        // Registrar erros também no log de erros do PHP
        if ($level === 'error') {
            error_log($message . ' - ' . json_encode($data, JSON_UNESCAPED_UNICODE));
        }
    }

    /**
     * Registra informação
     */
    public static function info(string $message, array $data = []): void
    {
        self::log($message, $data, 'info');
    }

    /**
     * Registra erro
     */
    public static function error(string $message, array $data = []): void
    {
        self::log($message, $data, 'error');
    }

    /**
     * Registra mensagem de debug
     */
    public static function debug(string $message, array $data = []): void
    {
        self::log($message, $data, 'debug');
    }
    
    /**
     * Registra informações específicas sobre uploads
     */
    public static function upload(string $message, array $data = []): void
    {
        self::log($message, $data, 'info', 'uploads.log');
    }
    
    /**
     * Registra informações sobre configuração do sistema
     */
    public static function config(string $message, array $data = []): void
    {
        self::log($message, $data, 'info', 'config.log');
    }
}
