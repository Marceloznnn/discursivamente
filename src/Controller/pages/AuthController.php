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
    
    // Função simples para registro de logs
    private function log($message) {
        $logFile = __DIR__ . '/../../logs/auth.log';
        $timestamp = date('Y-m-d H:i:s');
        $logMessage = "[$timestamp] $message" . PHP_EOL;
        
        // Cria diretório de logs se não existir
        $logDir = dirname($logFile);
        if (!is_dir($logDir)) {
            mkdir($logDir, 0755, true);
        }
        
        file_put_contents($logFile, $logMessage, FILE_APPEND);
    }

    public function __construct($twig)
    {
        $this->twig = $twig;
        $this->userRepository = new UserRepository(Connection::getInstance());
    }

    public function login()
    {
        $this->log("Página de login acessada");
        echo $this->twig->render('auth/login.twig');
    }

    public function loginPost()
    {
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';
        
        $this->log("Tentativa de login para o email: $email");

        $user = $this->userRepository->findByEmail($email);

        if ($user) {
            $this->log("Usuário encontrado com ID: " . $user->getId() . ", Tipo: " . $user->getType());
            
            if (password_verify($password, $user->getPassword())) {
                $this->log("Senha válida para usuário " . $user->getEmail());
                
                $_SESSION['user'] = $user->toArray();
                $userType = $user->getType();
                
                $this->log("Redirecionando usuário para a página home");
                
                // Redirecionando todos os tipos de usuário para a home
                header('Location: /');
                exit;
            } else {
                $this->log("Senha inválida para usuário " . $user->getEmail());
            }
        } else {
            $this->log("Usuário não encontrado para o email: $email");
        }

        echo $this->twig->render('auth/login.twig', [
            'error' => 'E-mail ou senha inválidos.'
        ]);
    }
    
    public function register()
    {
        $this->log("Página de registro acessada");
        echo $this->twig->render('auth/register.twig');
    }

    public function registerPost()
    {
        $name = $_POST['name'] ?? '';
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';
        $type = $_POST['type'] ?? null;

        $this->log("Tentativa de registro para: $name, $email, tipo: $type");

        if (!$name || !$email || !$password) {
            $this->log("Registro falhou: campos obrigatórios não preenchidos");
            echo $this->twig->render('auth/register.twig', [
                'error' => 'Preencha todos os campos.'
            ]);
            return;
        }

        if ($this->userRepository->findByEmail($email)) {
            $this->log("Registro falhou: email já cadastrado - $email");
            echo $this->twig->render('auth/register.twig', [
                'error' => 'E-mail já cadastrado.'
            ]);
            return;
        }

        $hash = password_hash($password, PASSWORD_BCRYPT);
        $user = new User($name, $email, $hash, $type);
        $this->userRepository->save($user);
        
        $this->log("Usuário registrado com sucesso: $email, tipo: $type");

        // Após registro, redirecionar para a página home em vez de login
        // Podemos realizar login automático para melhor experiência do usuário
        $_SESSION['user'] = [
            'name' => $name,
            'email' => $email,
            'type' => $type
        ];
        
        $this->log("Redirecionando novo usuário para a página home");
        header('Location: /');
        exit;
    }
    
    public function logout()
    {
        $userId = $_SESSION['user']['id'] ?? 'desconhecido';
        $this->log("Logout para usuário ID: $userId");
        
        session_destroy();
        header('Location: /login');
        exit;
    }
    
    public function forgot()
    {
        $this->log("Página de recuperação de senha acessada");
        echo $this->twig->render('auth/forgot.twig');
    }

    public function forgotPost()
    {
        $email = $_POST['email'] ?? '';
        $this->log("Solicitação de recuperação de senha para: $email");
        
        $user = $this->userRepository->findByEmail($email);

        if (!$user) {
            $this->log("Recuperação falhou: email não encontrado - $email");
            echo $this->twig->render('auth/forgot.twig', [
                'error' => 'E-mail não encontrado.'
            ]);
            return;
        }

        // Gera código e expiração
        $code = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        $expiresAt = date('Y-m-d H:i:s', strtotime('+15 minutes'));

        $this->userRepository->setRecoveryCode($user->getId(), $code, $expiresAt);
        $this->log("Código de recuperação gerado para usuário ID: " . $user->getId());

        // Envia e-mail
        ResetPasswordMailer::send($user->getEmail(), $user->getName(), $code);
        $this->log("Email de recuperação enviado para: " . $user->getEmail());

        echo $this->twig->render('auth/forgot.twig', [
            'success' => 'Enviamos um código para seu e-mail. Verifique sua caixa de entrada.'
        ]);
    }

    public function reset()
    {
        $this->log("Página de redefinição de senha acessada");
        echo $this->twig->render('auth/reset.twig');
    }

    public function resetPost()
    {
        $email = $_POST['email'] ?? '';
        $code = $_POST['code'] ?? '';
        $password = $_POST['password'] ?? '';
        
        $this->log("Tentativa de redefinição de senha para: $email");

        $user = $this->userRepository->findByEmail($email);

        if (!$user || !$code || !$password) {
            $this->log("Redefinição falhou: dados inválidos");
            echo $this->twig->render('auth/reset.twig', [
                'error' => 'Dados inválidos.'
            ]);
            return;
        }

        // Confirma código e validade
        if (
            $user->getRecoveryCode() !== $code ||
            !$user->getRecoveryCodeExpiresAt() ||
            strtotime($user->getRecoveryCodeExpiresAt()) < time()
        ) {
            $this->log("Redefinição falhou: código inválido ou expirado para usuário ID: " . $user->getId());
            echo $this->twig->render('auth/reset.twig', [
                'error' => 'Código inválido ou expirado.'
            ]);
            return;
        }

        // Atualiza senha e limpa código
        $hash = password_hash($password, PASSWORD_BCRYPT);
        $this->userRepository->updatePassword($user->getId(), $hash);
        $this->log("Senha redefinida com sucesso para usuário ID: " . $user->getId());

        echo $this->twig->render('auth/reset.twig', [
            'success' => 'Senha redefinida com sucesso! Você já pode fazer login.'
        ]);
    }
}