<?php
namespace Discursivamente\Controllers\Auth;

use App\Models\User;

class VerifyCodeController {
    public function index() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        // Se não houver dados temporários de registro, redireciona para a página de registro
        if (!isset($_SESSION['temp_register_data'])) {
            header('Location: /register');
            exit;
        }
        require_once BASE_PATH . '/src/views/auth/verificacao.php';
    }
    
    public function verify() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        // Verifica o token CSRF
        if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
            $_SESSION['mensagem'] = "Token inválido.";
            $_SESSION['tipo_mensagem'] = "danger";
            header('Location: /verify-code');
            exit;
        }
        
        // Recebe o código digitado (inputs com name="code[]")
        $codeArray   = $_POST['code'] ?? [];
        $enteredCode = implode('', $codeArray);
        
        // Verifica se o código expirou
        if (time() > ($_SESSION['verification_expires'] ?? 0)) {
            $_SESSION['mensagem'] = "O código de verificação expirou.";
            $_SESSION['tipo_mensagem'] = "danger";
            header('Location: /resend-code');
            exit;
        }
        
        if ($enteredCode !== $_SESSION['verification_code']) {
            $_SESSION['mensagem'] = "Código de verificação incorreto.";
            $_SESSION['tipo_mensagem'] = "danger";
            header('Location: /verify-code');
            exit;
        }
        
        // Validação dos dados temporários
        $tempData = $_SESSION['temp_register_data'];
        if (!isset($tempData['name'], $tempData['email'], $tempData['password'])) {
            $_SESSION['mensagem'] = "Dados incompletos para criação do usuário.";
            $_SESSION['tipo_mensagem'] = "danger";
            header('Location: /register');
            exit;
        }
        
        // Supondo que a conexão PDO esteja disponível na variável global $pdo
        $user = new User($GLOBALS['pdo']);
        
        // Verifica se o usuário já existe
        if ($user->findByEmail($tempData['email'])) {
            $_SESSION['mensagem'] = "O email informado já está em uso.";
            $_SESSION['tipo_mensagem'] = "danger";
            header('Location: /register');
            exit;
        }
        
        // Cria o usuário na base de dados utilizando os dados temporários
        $userCreated = $user->create($tempData);
        
        if (!$userCreated) {
            $_SESSION['mensagem'] = "Falha ao criar usuário.";
            $_SESSION['tipo_mensagem'] = "danger";
            // Em ambiente de desenvolvimento, você pode debugar os erros através dos logs
            header('Location: /register');
            exit;
        }
        
        // Limpa os dados temporários da sessão
        unset($_SESSION['temp_register_data'], $_SESSION['verification_code'], $_SESSION['verification_expires']);
        
        $_SESSION['mensagem'] = "Conta criada com sucesso. Você já pode fazer login.";
        $_SESSION['tipo_mensagem'] = "success";
        header('Location: /login');
        exit;
    }
    
    // Método para reenviar o código
    public function resend() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        if (!isset($_SESSION['temp_register_data'])) {
            header('Location: /register');
            exit;
        }
        
        $email = $_SESSION['temp_register_data']['email'];
        // Gera um novo código de verificação
        $verification_code = str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);
        $_SESSION['verification_code'] = $verification_code;
        $_SESSION['verification_expires'] = time() + (5 * 60);
        
        // Envia o novo código
        $mailSent = \Discursivamente\Services\MailService::sendVerificationCode($email, $verification_code);
        if ($mailSent) {
            $_SESSION['mensagem'] = "Código reenviado com sucesso.";
            $_SESSION['tipo_mensagem'] = "success";
        } else {
            $_SESSION['mensagem'] = "Falha ao reenviar o código.";
            $_SESSION['tipo_mensagem'] = "danger";
        }
        header('Location: /verify-code');
        exit;
    }
}
