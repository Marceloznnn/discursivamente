<?php
// filepath: c:\xampp\htdocs\Discursivamente2.1\src\Infrastructure\Database\Connection.php

namespace Infrastructure\Database;

use PDO;
use PDOException;

class Connection
{
    private static ?PDO $instance = null;

    public static function getInstance(): PDO
    {
        if (self::$instance === null) {
            $host = $_ENV['DB_HOST'] ?? '127.0.0.1';
            $db   = $_ENV['DB_NAME'] ?? 'discursivamente_db';
            $user = $_ENV['DB_USER'] ?? 'root';
            $pass = $_ENV['DB_PASS'] ?? '';
            $port = $_ENV['DB_PORT'] ?? '3306';

            $dsn = "mysql:host=$host;dbname=$db;port=$port;charset=utf8mb4";

            try {
                self::$instance = new PDO($dsn, $user, $pass, [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                ]);
            } catch (PDOException $e) {
                die('Erro ao conectar ao banco de dados: ' . $e->getMessage());
            }
        }
        return self::$instance;
    }
}