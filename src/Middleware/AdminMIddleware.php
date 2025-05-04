<?php

namespace Middleware;

class AdminMiddleware
{
    public static function handle()
    {
        if (empty($_SESSION['user']) || ($_SESSION['user']['type'] ?? null) !== 'admin') {
            header('Location: /login');
            exit;
        }
    }
}