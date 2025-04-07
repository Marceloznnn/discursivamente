<?php

namespace Discursivamente\Controllers\Auth;

class LogoutController {
    public function logout()
    {
        // Inicia a sessão se ainda não estiver iniciada
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        
        // Destroi todas as variáveis de sessão
        $_SESSION = array();
        
        // Destroi a sessão
        session_destroy();
        
        // Redireciona para a página inicial
        header('Location: /');
        exit;
    }
}