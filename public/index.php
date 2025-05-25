<?php
declare(strict_types=1);

// Importações de classes e funções
use Dotenv\Dotenv;
use Twig\Loader\FilesystemLoader;
use Twig\Environment;
use function FastRoute\simpleDispatcher;
use FastRoute\Dispatcher;
use FastRoute\RouteCollector;
use Infrastructure\Database\Connection;
use Infrastructure\Twig\TwigFunctions;

// Carrega o autoloader do Composer
require_once __DIR__ . '/../vendor/autoload.php';

// Carregar variáveis do .env
if (file_exists(__DIR__ . '/../.env')) {
    $dotenv = Dotenv::createImmutable(__DIR__ . '/../');
    $dotenv->load();
}

// Start session
session_start();

// Setup Twig templating
$loader = new FilesystemLoader(__DIR__ . '/../src/Views');
$twig   = new Environment($loader, [
    'cache'       => false, // ou caminho para o cache
    'auto_reload' => true,
]);

// Register custom Twig functions
TwigFunctions::addCustomFunctions($twig);
// Make session available in all views
$twig->addGlobal('session', $_SESSION);

// Load routes
$dispatcher = simpleDispatcher(function(RouteCollector $r) {
    require __DIR__ . '/../src/Routes/web.php';
});

// Instantiate the PDO connection
$pdo = Connection::getInstance();

// Fetch HTTP method and URI
$httpMethod = $_SERVER['REQUEST_METHOD'];
$uri        = $_SERVER['REQUEST_URI'];

// Strip query string
if (false !== $pos = strpos($uri, '?')) {
    $uri = substr($uri, 0, $pos);
}
$uri = rawurldecode($uri);

// Dispatch request
$routeInfo = $dispatcher->dispatch($httpMethod, $uri);
switch ($routeInfo[0]) {
    case Dispatcher::NOT_FOUND:
        http_response_code(404);
        echo $twig->render('errors/404.twig');
        break;

    case Dispatcher::METHOD_NOT_ALLOWED:
        http_response_code(405);
        echo $twig->render('errors/405.twig', [
            'allowed' => $routeInfo[1]
        ]);
        break;

    case Dispatcher::FOUND:
        [$handler, $vars] = [$routeInfo[1], $routeInfo[2]];

        if (is_callable($handler)) {
            echo $handler($twig, $pdo, ...array_values($vars));
        } else {
            [$controllerClass, $method] = $handler;
            $controller = new $controllerClass($twig, $pdo);
            echo call_user_func_array([$controller, $method], $vars);
        }
        break;
}