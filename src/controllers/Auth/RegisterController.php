<?php
namespace Discursivamente\Controllers\Auth;

use Discursivamente\Services\MailService;
use App\Models\User;

class RegisterController {
    public function index() {
        require_once BASE_PATH . '/src/views/auth/register.php';
    }
    
    public function registrar() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        // Validação do token CSRF
        if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
            $_SESSION['mensagem'] = "Token inválido.";
            $_SESSION['tipo_mensagem'] = "danger";
            header('Location: /register');
            exit;
        }
        
        // Recebe e valida os dados enviados
        $name             = trim($_POST['name'] ?? '');
        $email            = trim($_POST['email'] ?? '');
        $password         = $_POST['password'] ?? '';
        $confirm_password = $_POST['confirm_password'] ?? '';
        
        if (empty($name) || empty($email) || empty($password) || empty($confirm_password)) {
            $_SESSION['mensagem'] = "Todos os campos são obrigatórios.";
            $_SESSION['tipo_mensagem'] = "danger";
            header('Location: /register');
            exit;
        }
        
        if ($password !== $confirm_password) {
            $_SESSION['mensagem'] = "As senhas não coincidem.";
            $_SESSION['tipo_mensagem'] = "danger";
            header('Location: /register');
            exit;
        }
        
        // Outros filtros ou validações podem ser aplicados aqui
        
        // Armazena os dados temporariamente na sessão
        $_SESSION['temp_register_data'] = [
            'name'     => $name,
            'email'    => $email,
            'password' => $password // senha em texto puro, pois o model fará o hash
        ];
        
        // Gera um código de verificação de 6 dígitos
        $verification_code = str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);
        $_SESSION['verification_code'] = $verification_code;
        $_SESSION['verification_expires'] = time() + (5 * 60); // Expira em 5 minutos
        
        // Envia o código por email utilizando o MailService
        $mailSent = MailService::sendVerificationCode($email, $verification_code);
        if (!$mailSent) {
            $_SESSION['mensagem'] = "Falha ao enviar o email de verificação.";
            $_SESSION['tipo_mensagem'] = "danger";
            header('Location: /register');
            exit;
        }
        
        // Redireciona para a página de verificação
        header('Location: /verify-code');
        exit;
    }
}
