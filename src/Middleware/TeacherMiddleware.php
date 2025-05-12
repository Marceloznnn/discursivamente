<?php
namespace Middleware;

class TeacherMiddleware
{
    public static function handle()
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }

        if (empty($_SESSION['user']) || ($_SESSION['user']['type'] ?? null) !== 'teacher') {
            header('Location: /login');
            exit;
        }
    }
}
