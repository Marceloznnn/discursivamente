<?php

namespace Discursivamente\Controllers\Community;

class ForumController
{
    public function index()
    {
        require_once __DIR__ . '/../../views/community/forum/index.php';
    }

    public function thread($id = null)
    {
        $id = filter_var($id, FILTER_VALIDATE_INT);
        if (!$id) {
            header('Location: /forum');
            exit;
        }
        
        require_once __DIR__ . '/../../views/community/forum/thread.php';
    }

    public function createView()
    {
        if (!isset($_SESSION['usuario_id'])) {
            $_SESSION['mensagem'] = 'Você precisa estar logado para criar uma thread';
            header('Location: /login');
            exit;
        }
        
        require_once __DIR__ . '/../../views/community/forum/create.php';
    }

    public function create()
    {
        if (!isset($_SESSION['usuario_id'])) {
            $_SESSION['mensagem'] = 'Você precisa estar logado para criar uma thread';
            header('Location: /login');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $titulo = filter_input(INPUT_POST, 'titulo', FILTER_SANITIZE_SPECIAL_CHARS);
            $conteudo = filter_input(INPUT_POST, 'conteudo', FILTER_SANITIZE_SPECIAL_CHARS);

            if (empty($titulo) || empty($conteudo)) {
                $_SESSION['mensagem'] = 'Preencha todos os campos obrigatórios';
                header('Location: /forum/create');
                exit;
            }
            
            header('Location: /forum');
            exit;
        }

        header('Location: /forum/create');
        exit;
    }
}