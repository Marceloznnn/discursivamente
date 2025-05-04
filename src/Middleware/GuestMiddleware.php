<?php
// src/Middleware/GuestMiddleware.php
namespace Middleware;

class GuestMiddleware
{
    public static function handle(): void
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }

        // Se já estiver logado, manda para /dashboard
        if (!empty($_SESSION['user'])) {
            header('Location: /dashboard');
            exit;
        }

        // Caso contrário, deixa continuar ao controller
    }
}
