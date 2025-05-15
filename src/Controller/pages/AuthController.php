<?php

namespace Controller\pages;

use Infrastructure\Database\Connection;
use Infrastructure\Mail\ResetPasswordMailer;
use Repositories\UserRepository;
use App\Models\User;
use Google\Client as GoogleClient;
use Google\Service\Oauth2;

class AuthController
{
    private $twig;
    private $userRepository;
    private $googleClientId = '37968290526-8dg2egv1v4n821vj4fp4oqaele7l8cbk.apps.googleusercontent.com';
    private $googleClientSecret = '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';
    private $googleRedirectUri = 'http://localhost:8000/auth/google/callback';
    
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
            // Iniciar sessão se não estiver ativa
            if (session_status() !== PHP_SESSION_ACTIVE) {
                session_start();
            }
            
            $_SESSION['user'] = $user->toArray();
            $type = $user->getType();
            $this->log("Login OK: ID {$user->getId()}, tipo " . ($type ?: 'null'));

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

    /**
     * Configure and return a Google Client instance
     */
    private function getGoogleClient()
    {
        $client = new GoogleClient();
        $client->setClientId($this->googleClientId);
        $client->setClientSecret($this->googleClientSecret);
        $client->setRedirectUri($this->googleRedirectUri);
        $client->addScope('email');
        $client->addScope('profile');
        
        return $client;
    }


/**
 * Redirect to Google OAuth login page
 */
public function googleRedirect()
{
    $this->log("**[GOOGLE REDIRECT]** Iniciando redirecionamento");
    $client  = $this->getGoogleClient();
    $authUrl = $client->createAuthUrl();
    $this->log("**[GOOGLE REDIRECT]** Auth URL gerada: {$authUrl}");
    
    header('Location: ' . $authUrl);
    exit;
}

/**
 * Handle Google OAuth callback
 */
public function googleCallback()
{
    $this->log("**[GOOGLE CALLBACK]** Início do callback");
    $this->log("**[GOOGLE CALLBACK]** \$_GET = " . json_encode($_GET));

    if (!isset($_GET['code'])) {
        $this->log("**[GOOGLE CALLBACK]** Erro: código de autorização não recebido");
        header('Location: /login?error=google_auth_denied');
        exit;
    }

    try {
        // 1) FETCH TOKEN
        $client = $this->getGoogleClient();
        $token  = $client->fetchAccessTokenWithAuthCode($_GET['code']);
        $this->log("**[GOOGLE CALLBACK]** Token retornado: " . json_encode($token));

        if (isset($token['error'])) {
            $this->log("**[GOOGLE CALLBACK]** Erro ao obter token: " . $token['error']);
            header('Location: /login?error=google_auth_failed');
            exit;
        }
        if (empty($token['access_token'])) {
            $this->log("**[GOOGLE CALLBACK]** Access token vazio!");
            header('Location: /login?error=google_no_token');
            exit;
        }

        // 2) GET USER INFO
        $client->setAccessToken($token['access_token']);
        $oauth2     = new Oauth2($client);
        $googleUser = $oauth2->userinfo->get();
        $this->log("**[GOOGLE CALLBACK]** Google user data: " . json_encode([
            'email' => $googleUser->email,
            'name'  => $googleUser->name,
            'id'    => $googleUser->id,
        ]));

        // 3) DATABASE: envolva em try/catch separado
        try {
            $this->log("**[GOOGLE CALLBACK]** Procurando usuário no BD: {$googleUser->email}");
            $user = $this->userRepository->findByEmail($googleUser->email);

            if (!$user) {
                $this->log("**[GOOGLE CALLBACK]** Usuário não existe. Criando novo.");
                // Alteração: Passar null ao invés de 'user' como tipo
                $user = new User($googleUser->name, $googleUser->email, '', null);
                
                $this->log("**[GOOGLE CALLBACK]** Salvando novo usuário...");
                $this->userRepository->save($user);
                $this->log("**[GOOGLE CALLBACK]** Salvamento concluído.");

                $user = $this->userRepository->findByEmail($googleUser->email);
                $this->log("**[GOOGLE CALLBACK]** Novo usuário criado com ID: {$user->getId()}");
            } else {
                $this->log("**[GOOGLE CALLBACK]** Usuário já existe com ID: {$user->getId()}");
            }
        } catch (\Throwable $e) {
            $this->log("**[GOOGLE CALLBACK]** ERRO no BD: " . $e->getMessage());
            throw $e;
        }

        // 4) SESSÃO, COOKIE E REDIRECT  
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }
        $_SESSION['user']       = $user->toArray();
        $_SESSION['google_auth']= true;
        $this->log("**[GOOGLE CALLBACK]** Sessão iniciada para ID: {$user->getId()}");

        // (opcional) cookie de lembrete
        $cookieName   = 'auth_remember';
        $cookieValue  = hash('sha256', $user->getId() . $user->getEmail() . time());
        $cookieExpiry = time() + 30*24*60*60;
        setcookie($cookieName, $cookieValue, $cookieExpiry, '/', '', true, true);

        $type = $user->getType();
        $dest = ($type==='admin') ? '/admin' : '/';
        $this->log("**[GOOGLE CALLBACK]** Redirecionando para: {$dest}");

        header("Location: {$dest}");
        exit;

    } catch (\Exception $e) {
        $this->log("**[GOOGLE CALLBACK]** Exceção geral: " . $e->getMessage());
        header('Location: /login?error=google_error');
        exit;
    }
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
        // Alteração: Tipo padrão é null ao invés de 'user'
        $type     = $_POST['type']     ?? null;

        $this->log("Tentativa de registro: $name, $email, tipo " . ($type ?: 'null'));

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
        
        // Garantir que a sessão esteja iniciada
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }
        
        $_SESSION['user'] = $user->toArray();
        $this->log("Registro OK: ID {$user->getId()}, tipo " . ($type ?: 'null'));

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
        
        // Limpar cookies de autenticação, se houver
        if (isset($_COOKIE['auth_remember'])) {
            setcookie('auth_remember', '', time() - 3600, '/');
        }
        
        // Destruir sessão
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

        // Verifica se o usuário não tem senha (login via Google)
        if (empty($user->getPassword())) {
            $this->log("Tentativa de reset para conta Google: $email");
            echo $this->twig->render('auth/forgot.twig', [
                'error' => 'Esta conta utiliza login via Google. Por favor, faça login com Google.'
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

        // Garantir que a sessão esteja iniciada
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }
        
        // Recarrega o usuário (para garantir que veio o hash novo, e todos os campos)
        $user = $this->userRepository->findByEmail($email);
        $_SESSION['user'] = $user->toArray();
        $type = $user->getType();
        $this->log("Auto-login após reset: ID {$user->getId()}, tipo " . ($type ?: 'null'));

        // Redireciona conforme tipo
        switch ($type) {
            case 'admin':
                $dest = '/admin'; break;
            default:
                $dest = '/'; break;
        }

        header("Location: {$dest}");
        exit;
    }

    /**
     * Verifica se o usuário está autenticado
     * Pode ser usado em middleware ou dentro de outras rotas
     */
    public function checkAuth()
    {
        // Garantir que a sessão esteja iniciada
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }
        
        // Verificar se há usuário na sessão
        if (isset($_SESSION['user']) && !empty($_SESSION['user']['id'])) {
            return true;
        }
        
        // Implementação opcional: verificar cookie de autenticação
        if (isset($_COOKIE['auth_remember'])) {
            $cookieValue = $_COOKIE['auth_remember'];
            // Implementar verificação do cookie no banco de dados
            // $user = $this->userRepository->findByAuthCookie($cookieValue);
            // if ($user) {
            //    $_SESSION['user'] = $user->toArray();
            //    return true;
            // }
        }
        
        return false;
    }
}