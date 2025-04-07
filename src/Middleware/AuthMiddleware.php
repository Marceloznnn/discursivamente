<?php
namespace Discursivamente\Middleware;

class AuthMiddleware {
    public function handleApi() {
        session_start();
        if (isset($_SESSION['user'])) {
            return ['success' => true];
        }
        return ['success' => false, 'message' => 'Unauthorized'];
    }
}
