<?php
// Se estivermos usando o servidor embutido do PHP, deixe que ele sirva os arquivos existentes diretamente
if (php_sapi_name() === 'cli-server') {
    $file = __DIR__ . parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH);
    if (is_file($file)) {
        return false;
    }
}

// Verificação para arquivos estáticos (favicon, manifest, etc.)
$requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$staticFiles = [
    '/favicon.ico',
    '/apple-touch-icon.png',
    '/favicon-32x32.png',
    '/favicon-16x16.png',
    '/site.webmanifest'
];
if (in_array($requestUri, $staticFiles)) {
    return false; // Permite que o servidor sirva o arquivo estático
}

session_start(); // Inicia a sessão

// Define o caminho base absoluto (raiz do projeto)
define('BASE_PATH', dirname(__DIR__));

// Inclui o autoloader do Composer, se disponível
if (file_exists(BASE_PATH . '/vendor/autoload.php')) {
    require_once BASE_PATH . '/vendor/autoload.php';
}

// Inclui a configuração do banco de dados
require_once BASE_PATH . '/src/config/database.php';

// Remove a parte "/public" da URL, se presente
$requestUri = str_replace('/public', '', $requestUri);

// Agora, inclua as definições de rotas
require_once BASE_PATH . '/src/routes/web.php';

// Incluir os controladores necessários
require_once BASE_PATH . '/src/controllers/LoginController.php';
require_once BASE_PATH . '/src/controllers/RegisterController.php';
require_once BASE_PATH . '/src/controllers/HomeController.php';
require_once BASE_PATH . '/src/controllers/LogoutController.php';
require_once BASE_PATH . '/src/controllers/BibliotecaController.php';
require_once BASE_PATH . '/src/controllers/QuemSomosController.php';
require_once BASE_PATH . '/src/controllers/PerfilController.php';
require_once BASE_PATH . '/src/controllers/CommunicationController.php';
require_once BASE_PATH . '/src/controllers/CompromissosController.php';
require_once BASE_PATH . '/src/controllers/OfflineController.php'; // Novo controlador para offline

// Utilize a variável $requestUri definida no index.php
switch ($requestUri) {
    case '/':
    case '/home':
        $homeController = new HomeController();
        $homeController->index();
        break;
    case '/login':
        $loginController = new LoginController($pdo);
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $loginController->login();
        } else {
            $loginController->showLoginForm();
        }
        break;
    case '/register':
        $registerController = new RegisterController($pdo);
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $registerController->register();
        } else {
            $registerController->showRegisterForm();
        }
        break;
    case '/logout':
        $logoutController = new LogoutController();
        $logoutController->logout();
        break;
    case '/biblioteca':
        $bibliotecaController = new BibliotecaController($pdo);
        $bibliotecaController->index();
        break;
    case '/quem-somos':
        $quemSomosController = new QuemSomosController();
        $quemSomosController->index();
        break;
    // Rotas para a comunidade/ comunicação
    case '/comunidade/comunicacao':
        $communicationController = new CommunicationController();
        $communicationController->index();
        break;
    case '/comunidade/foruns':
        $communicationController = new CommunicationController();
        $communicationController->foruns();
        break;
    case '/comunidade/clube-livros':
        $communicationController = new CommunicationController();
        $communicationController->clubeLivros();
        break;
    case '/comunidade/grupo':
        $communicationController = new CommunicationController();
        $communicationController->grupo();
        break;
    case '/compromissos':
        $compromissosController = new CompromissosController();
        $compromissosController->index();
        break;
    case '/offline': // Nova rota para a página offline
        $offlineController = new OfflineController();
        $offlineController->index();
        break;
    default:
        // Rotas agrupadas para perfil
        if (strpos($requestUri, '/perfil') === 0) {
            $perfilController = new PerfilController();
            $subRoute = str_replace('/perfil', '', $requestUri);
            
            switch ($subRoute) {
                case '':
                case '/':
                    $perfilController->index();
                    break;
                case '/editar':
                    $perfilController->editarPerfil();
                    break;
                case '/redefinir-senha':
                    $perfilController->redefinirSenha();
                    break;
                case '/configuracao':
                    $perfilController->configuracao();
                    break;
                case '/salvos':
                    $perfilController->salvos();
                    break;
                case '/favoritos':
                    $perfilController->favoritos();
                    break;
                case '/gerenciar-livros':
                    $perfilController->gerenciarLivros();
                    break;
                case '/historico':
                    $perfilController->historico();
                    break;
                default:
                    http_response_code(404);
                    echo "Página não encontrada.";
                    break;
            }
        } else {
            http_response_code(404);
            echo "Página não encontrada.";
        }
        break;
}
?>
