<?php 
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
require_once BASE_PATH . '/src/controllers/SearchController.php';              // Pesquisa de Livros
require_once BASE_PATH . '/src/controllers/CommunitySearchController.php';     // Pesquisa de Comunidades

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
    // Nova rota para pesquisa de livros (caminho: /inicio/buscar)
    case '/inicio/buscar':
        $searchController = new SearchController($pdo);
        $searchController->index();
        break;
    // Nova rota para pesquisa de comunidades (caminho: /comunidades/buscar)
    case '/comunidades/buscar':
        $communitySearchController = new CommunitySearchController($pdo);
        $communitySearchController->index();
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
                // Rota para editar informações (anteriormente /personal)
                case '/editar':
                    $perfilController->editarPerfil();
                    break;
                // Rota para redefinir senha (anteriormente /security)
                case '/redefinir-senha':
                    $perfilController->redefinirSenha();
                    break;
                // Outras rotas de perfil podem ser adicionadas aqui:
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
