<?php
// src/controllers/RegisterController.php
require_once BASE_PATH . '/src/models/UserModel.php';

class RegisterController {
    private $userModel;

    // Construtor que recebe a conexão PDO
    public function __construct($pdo) {
        $this->userModel = new UserModel($pdo);
    }

    // Exibe o formulário de registro
    public function showRegisterForm() {
        $title = "Registro - Discursivamente";
        require_once BASE_PATH . '/src/views/register.php';
    }

    // Processa o registro do usuário
    public function register() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name             = $_POST['nome'] ?? '';
            $email            = $_POST['email'] ?? '';
            $password         = $_POST['senha'] ?? '';
            $confirm_password = $_POST['confirm_senha'] ?? '';

            if ($password !== $confirm_password) {
                $error = "As senhas não conferem.";
                $title = "Registro - Discursivamente";
                require_once BASE_PATH . '/src/views/register.php';
                return;
            }

            if ($this->userModel->getUserByEmail($email)) {
                $error = "Email já registrado.";
                $title = "Registro - Discursivamente";
                require_once BASE_PATH . '/src/views/register.php';
                return;
            }

            $success = $this->userModel->register($name, $email, $password);
            if ($success) {
                header("Location: /home");  // Redireciona para a página Home após o registro
                exit;
            } else {
                $error = "Erro ao registrar usuário.";
                $title = "Registro - Discursivamente";
                require_once BASE_PATH . '/src/views/register.php';
            }
            
        }
    }
}
