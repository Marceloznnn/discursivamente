<?php
// src/controllers/LoginController.php
require_once BASE_PATH . '/src/models/UserModel.php';

class LoginController {
    private $userModel;

    // Construtor que recebe a conexão PDO
    public function __construct($pdo) {
        $this->userModel = new UserModel($pdo);
    }

    // Exibe o formulário de login
    public function showLoginForm() {
        $title = "Login - Discursivamente";
        require_once BASE_PATH . '/src/views/login.php';
    }

    // Processa o login
    public function login() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email    = $_POST['email'] ?? '';
            $password = $_POST['senha'] ?? '';

            $user = $this->userModel->getUserByEmail($email);
            if ($user && password_verify($password, $user['password'])) {
                session_start();
                $_SESSION['user'] = $user;
                header("Location: /home");  // Redireciona para a página Home após o login
                exit;
            } else {
                $error = "Email ou senha incorretos.";
                $title = "Login - Discursivamente";
                require_once BASE_PATH . '/src/views/login.php';
            }
            
        }
    }
}
