<?php

namespace Discursivamente\Controllers\Auth;

use App\Models\User;

class LoginController {
    public function index()
    {
        // Inicia a sessão se ainda não estiver iniciada
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        
        // Exibe a página de login
        require_once __DIR__ . '/../../views/auth/login.php';
    }
    
    // Método renomeado para autenticar para compatibilidade com o router
    public function autenticar()
    {
        // Inicia a sessão se ainda não estiver iniciada
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        
        // Verifica se o método é POST
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Coleta e sanitiza os dados do formulário
            $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
            $senha = $_POST['senha'] ?? '';
            
            // Valida os campos
            if (empty($email) || empty($senha)) {
                $_SESSION['mensagem'] = 'Preencha todos os campos';
                header('Location: /login');
                exit;
            }
            
            // Usa a variável global $pdo que já está definida
            global $pdo;
            
            // Instancia o model User
            $userModel = new User($pdo);
            
            // Procura o usuário pelo email
            if ($userModel->findByEmail($email)) {
                // Verifica se a senha está correta
                if ($userModel->checkPassword($senha)) {
                    // Login bem-sucedido: cria uma sessão para o usuário
                    $_SESSION['user'] = [
                        'id'           => $userModel->id,
                        'name'         => $userModel->name,
                        'email'        => $userModel->email,
                        'profileImage' => !empty($userModel->profileImage) ? $userModel->profileImage : '/assets/images/default.png'
                    ];
                    $_SESSION['mensagem'] = 'Login realizado com sucesso!';
                    
                    // Redireciona para a página principal
                    header('Location: /');
                    exit;
                } else {
                    // Senha incorreta
                    $_SESSION['mensagem'] = 'Email ou senha incorretos';
                    header('Location: /login');
                    exit;
                }
            } else {
                // Usuário não encontrado
                $_SESSION['mensagem'] = 'Email ou senha incorretos';
                header('Location: /login');
                exit;
            }
        }
        
        // Se não for método POST, redireciona para o formulário de login
        header('Location: /login');
        exit;
    }
    
    // Métodos para API
    public function apiLogin()
    {
        $data = json_decode(file_get_contents('php://input'), true);
        
        if (empty($data['email']) || empty($data['password'])) {
            return [
                'success' => false,
                'message' => 'Email e senha são obrigatórios',
                'status'  => 400
            ];
        }
        
        global $pdo;
        $userModel = new User($pdo);
        
        if ($userModel->findByEmail($data['email'])) {
            if ($userModel->checkPassword($data['password'])) {
                // Gera um token de API simples (em produção, use um sistema mais seguro)
                $token = bin2hex(random_bytes(32));
                
                // Na prática, você armazenaria este token em um banco de dados
                // associado ao usuário e com uma data de expiração
                
                return [
                    'success' => true,
                    'message' => 'Login realizado com sucesso',
                    'data'    => [
                        'user_id' => $userModel->id,
                        'name'    => $userModel->name,
                        'email'   => $userModel->email,
                        'token'   => $token
                    ],
                    'status'  => 200
                ];
            }
        }
        
        return [
            'success' => false,
            'message' => 'Credenciais inválidas',
            'status'  => 401
        ];
    }
    
    public function apiLogout()
    {
        // Em uma implementação real, você invalidaria o token
        return [
            'success' => true,
            'message' => 'Logout realizado com sucesso',
            'status'  => 200
        ];
    }
}
