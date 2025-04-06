<?php
// src/controllers/LogoutController.php

class LogoutController {
    public function logout() {
        session_start();
        
        // Limpa todas as variáveis de sessão
        $_SESSION = [];
        
        // Opcional: destrói também o cookie da sessão
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params["path"],
                $params["domain"],
                $params["secure"],
                $params["httponly"]
            );
        }
        
        // Destrói a sessão
        session_destroy();
        
        // Redireciona para a página de login
        header("Location: /login");
        exit;
    }
}
?>
