<?php
if (!defined('BASE_PATH')) {
    define('BASE_PATH', dirname(__DIR__, 2));
}

require_once BASE_PATH . '/vendor/autoload.php';
require_once BASE_PATH . '/src/config/database.php';

use Discursivamente\Controllers\{
    Auth\LoginController,
    Auth\RegisterController,
    Auth\LogoutController,
    Auth\VerifyCodeController,
    Core\HomeController,
    Community\BibliotecaController,
    Core\QuemSomosController,
    Profile\ProfileController,
    Community\CommunityController,
    Community\CompromissoController,
    Core\SearchController,
    Community\CommunitySearchController
};

$requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

switch ($requestUri) {
    case '/':
    case '/home':
        (new HomeController())->index();
        break;
        
    case '/login':
        $controller = new LoginController();
        $_SERVER['REQUEST_METHOD'] === 'POST' ? $controller->autenticar() : $controller->index();
        break;
        
    case '/register':
        $controller = new RegisterController();
        $_SERVER['REQUEST_METHOD'] === 'POST' ? $controller->registrar() : $controller->index();
        break;
        
    case '/verify-code':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            (new VerifyCodeController())->verify();
        } else {
            (new VerifyCodeController())->index();
        }
        break;
        
    case '/resend-code':
         (new VerifyCodeController())->resend();
         break;
         
    case '/logout':
        (new LogoutController())->logout();
        break;
        
    case '/biblioteca':
        (new BibliotecaController($pdo))->index();
        break;
        
    case '/quem-somos':
        (new QuemSomosController())->index();
        break;
        
    case '/comunidade/comunicacao':
        (new CommunityController())->index();
        break;
        
    case '/comunidade/foruns':
        (new CommunityController())->foruns();
        break;
        
    case '/comunidade/clube-livros':
        (new CommunityController())->clubeLivros();
        break;
        
    case '/comunidade/grupo':
        (new CommunityController())->grupo();
        break;
        
    case '/compromissos':
        (new CompromissoController())->index();
        break;
        
    case '/inicio/buscar':
        (new SearchController($pdo))->index();
        break;
        
    case '/comunidades/buscar':
        (new CommunitySearchController($pdo))->index();
        break;
        
    default:
        if (strpos($requestUri, '/perfil') === 0) {
            $profileController = new ProfileController();
            $subRoute = str_replace('/perfil', '', $requestUri);
            
            switch ($subRoute) {
                case '':
                case '/':
                    $profileController->index();
                    break;
                case '/editar':
                    $profileController->editarPerfil();
                    break;
                case '/redefinir-senha':
                    $profileController->redefinirSenha();
                    break;
                case '/configuracao':
                    $profileController->configuracao();
                    break;
                case '/salvos':
                    $profileController->salvos();
                    break;
                case '/favoritos':
                    $profileController->favoritos();
                    break;
                case '/gerenciar-livros':
                    $profileController->gerenciarLivros();
                    break;
                case '/historico':
                    $profileController->historico();
                    break;
                default:
                    http_response_code(404);
                    require_once BASE_PATH . '/src/views/errors/404.php';
                    break;
            }
        } else {
            http_response_code(404);
            require_once BASE_PATH . '/src/views/errors/404.php';
        }
        break;
}
