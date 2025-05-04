<?php
// src/Middleware/AuthMiddleware.php
namespace Middleware;

class AuthMiddleware
{
    public static function handle(): void
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }

        // Se não houver usuário logado, manda para /login
        if (empty($_SESSION['user'])) {
            header('Location: /login');
            exit;
        }

        // Caso contrário, deixa continuar ao controller
    }
}
