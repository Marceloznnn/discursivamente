<?php
namespace Discursivamente\Middleware;

class AdminMiddleware {
    public function handleApi() {
        session_start();
        // Supondo que a informação de "admin" esteja na sessão (ex.: $_SESSION['user']['role'] === 'admin')
        if (isset($_SESSION['user']) && ($_SESSION['user']['role'] ?? '') === 'admin') {
            return ['success' => true];
        }
        return ['success' => false, 'message' => 'Forbidden'];
    }
}
