<?php
// filepath: src/Infrastructure/Database/Connection.php

namespace Infrastructure\Database;

use PDO;
use PDOException;

class Connection
{
    private static ?PDO $instance = null;

    public static function getInstance(): PDO
    {
        if (self::$instance === null) {
            // Tenta obter credenciais do ambiente
            $host = $_ENV['DB_HOST']   ?? $_SERVER['DB_HOST']   ?? getenv('DB_HOST');
            $port = $_ENV['DB_PORT']   ?? $_SERVER['DB_PORT']   ?? getenv('DB_PORT');
            $db   = $_ENV['DB_NAME']   ?? $_SERVER['DB_NAME']   ?? getenv('DB_NAME');
            $user = $_ENV['DB_USER']   ?? $_SERVER['DB_USER']   ?? getenv('DB_USER');
            $pass = $_ENV['DB_PASS']   ?? $_SERVER['DB_PASS']   ?? getenv('DB_PASS');

            // Validações mínimas
            if (!$host || !$port || !$db || !$user) {
                throw new \RuntimeException(
                    'Variáveis de ambiente DB_HOST, DB_PORT, DB_NAME e DB_USER não estão definidas.'
                );
            }

            $dsn = sprintf('mysql:host=%s;port=%s;dbname=%s;charset=utf8mb4', $host, $port, $db);

            try {
                self::$instance = new PDO(
                    $dsn,
                    $user,
                    $pass,
                    [
                        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    ]
                );
            } catch (PDOException $e) {
                throw new \RuntimeException('Erro ao conectar ao banco: ' . $e->getMessage());
            }
        }

        return self::$instance;
    }
}