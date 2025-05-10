<?php

namespace Controller\pages;

use Infrastructure\Database\Connection;
use Infrastructure\Mail\ResetPasswordMailer;
use Repositories\UserRepository;
use App\Models\User;

class AuthController
{
    private $twig;
    private $userRepository;
    
    private function log($message) {
        $logFile   = __DIR__ . '/../../logs/auth.log';
        $timestamp = date('Y-m-d H:i:s');
        $msg       = "[$timestamp] $message\n";
        $dir       = dirname($logFile);
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }
        file_put_contents($logFile, $msg, FILE_APPEND);
    }

    public function __construct($twig)
    {
        $this->twig           = $twig;
        $this->userRepository = new UserRepository(Connection::getInstance());
    }

    // === LOGIN ===

    public function login()
    {
        $this->log("Página de login acessada");
        echo $this->twig->render('auth/login.twig');
    }

    public function loginPost()
    {
        $email    = $_POST['email']    ?? '';
        $password = $_POST['password'] ?? '';
        $this->log("Tentativa de login para: $email");

        $user = $this->userRepository->findByEmail($email);
        if ($user && password_verify($password, $user->getPassword())) {
            $_SESSION['user'] = $user->toArray();
            $type = $user->getType();
            $this->log("Login OK: ID {$user->getId()}, tipo {$type}");

            switch ($type) {
                case 'admin':
                    $dest = '/admin'; break;
                default:
                    $dest = '/'; break;
            }

            header("Location: {$dest}");
            exit;
        }

        $this->log("Falha no login para: $email");
        echo $this->twig->render('auth/login.twig', [
            'error' => 'E-mail ou senha inválidos.'
        ]);
    }

    // === REGISTRO ===


    public function register()
    {
        $this->log("Página de registro acessada");
        $prefillEmail = isset($_GET['email']) ? $_GET['email'] : '';
        echo $this->twig->render('auth/register.twig', [
            'prefillEmail' => $prefillEmail
        ]);
    }

    public function registerPost()
    {
        $name     = $_POST['name']     ?? '';
        $email    = $_POST['email']    ?? '';
        $password = $_POST['password'] ?? '';
        $type     = $_POST['type']     ?? null;

        $this->log("Tentativa de registro: $name, $email, tipo $type");

        if (!$name || !$email || !$password) {
            $this->log("Registro falhou: campos vazios");
            echo $this->twig->render('auth/register.twig', [
                'error' => 'Preencha todos os campos.',
                'prefillEmail' => $email
            ]);
            return;
        }

        if ($this->userRepository->findByEmail($email)) {
            $this->log("Registro falhou: e-mail já existe");
            echo $this->twig->render('auth/register.twig', [
                'error' => 'E-mail já cadastrado.',
                'prefillEmail' => $email
            ]);
            return;
        }

        $hash = password_hash($password, PASSWORD_BCRYPT);
        $user = new User($name, $email, $hash, $type);
        $this->userRepository->save($user);
        $_SESSION['user'] = $user->toArray();
        $this->log("Registro OK: ID {$user->getId()}, tipo {$type}");

        switch ($type) {
            case 'admin':
                $dest = '/admin'; break;
            default:
                $dest = '/'; break;
        }

        header("Location: {$dest}");
        exit;
    }

    // === LOGOUT ===

    public function logout()
    {
        $id = $_SESSION['user']['id'] ?? 'desconhecido';
        $this->log("Logout: usuário {$id}");
        session_destroy();
        header('Location: /login');
        exit;
    }

    // === ESQUECI A SENHA ===

    // 1) Exibe formulário de envio de e-mail
    public function forgot()
    {
        $this->log("Página 'Esqueci a senha' acessada");
        echo $this->twig->render('auth/forgot.twig');
    }

    // 2) Processa envio do e-mail e redireciona direto para a página de reset
    public function forgotPost()
    {
        $email = $_POST['email'] ?? '';
        $this->log("Solicitação de recovery para: $email");

        $user = $this->userRepository->findByEmail($email);
        if (!$user) {
            $this->log("E-mail não encontrado: $email");
            echo $this->twig->render('auth/forgot.twig', [
                'error' => 'E-mail não encontrado.'
            ]);
            return;
        }

        $code      = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        $expiresAt = date('Y-m-d H:i:s', strtotime('+15 minutes'));
        $this->userRepository->setRecoveryCode($user->getId(), $code, $expiresAt);
        ResetPasswordMailer::send($user->getEmail(), $user->getName(), $code);
        $this->log("Código enviado para {$email}");

        // redireciona imediatamente para página de reset
        header('Location: /reset-password');
        exit;
    }

    // === REDEFINIR SENHA ===

    // 3) Exibe formulário de reset (campo e-mail, código, nova senha)
    public function reset()
    {
        $this->log("Página 'Redefinir senha' acessada");
        echo $this->twig->render('auth/reset.twig');
    }

    // Dentro de Controller\pages\AuthController

    public function checkEmail()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            exit;
        }

        $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
        header('Content-Type: application/json; charset=utf-8');

        if (!$email) {
            http_response_code(400);
            echo json_encode(['error' => 'E-mail inválido']);
            exit;
        }

        $exists = $this->userRepository->findByEmail($email) !== null;
        echo json_encode(['exists' => $exists]);
        exit;
    }


    // 4) Processa reset e, em caso de sucesso, redireciona para /
    public function resetPost()
{
    $email    = $_POST['email']    ?? '';
    $code     = $_POST['code']     ?? '';
    $password = $_POST['password'] ?? '';
    $this->log("Tentativa de reset para: $email");

    $user = $this->userRepository->findByEmail($email);
    if (
        !$user ||
        $user->getRecoveryCode() !== $code ||
        !$user->getRecoveryCodeExpiresAt() ||
        strtotime($user->getRecoveryCodeExpiresAt()) < time()
    ) {
        $this->log("Reset falhou para: $email (código inválido ou expirado)");
        echo $this->twig->render('auth/reset.twig', [
            'error' => 'Código inválido ou expirado.'
        ]);
        return;
    }

    // Atualiza senha
    $hash = password_hash($password, PASSWORD_BCRYPT);
    $this->userRepository->updatePassword($user->getId(), $hash);
    $this->log("Senha resetada para: ID {$user->getId()}");

    // --- Novo: auto-login após reset ---
    // Recarrega o usuário (para garantir que veio o hash novo, e todos os campos)
    $user = $this->userRepository->findByEmail($email);
    $_SESSION['user'] = $user->toArray();
    $type = $user->getType();
    $this->log("Auto-login após reset: ID {$user->getId()}, tipo {$type}");

    // Redireciona conforme tipo
    switch ($type) {
        case 'admin':
            $dest = '/admin'; break;
        case 'instituicao':
        case 'teacher':
        case 'student':
        default:
            $dest = '/'; break;
    }

    header("Location: {$dest}");
    exit;
}
}