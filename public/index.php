<?php
/** @var \FastRoute\RouteCollector $r */

declare(strict_types=1);

// Public front controller
// Load Composer autoloader
require __DIR__ . '/../vendor/autoload.php';

// Start session
session_start();

// Load environment variables
use Dotenv\Dotenv;
use Twig\Loader\FilesystemLoader;
use Twig\Environment;
use function FastRoute\simpleDispatcher;
use FastRoute\Dispatcher;
use FastRoute\RouteCollector;
use Infrastructure\Database\Connection;

// Inclui a classe que contém as funções do Twig
use Infrastructure\Twig\TwigFunctions;

$dotenv = Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

// Setup Twig templating
$loader = new FilesystemLoader(__DIR__ . '/../src/Views');
$twig = new Environment($loader, [
    'cache' => false, // ou caminho para o cache
    'auto_reload' => true,
]);

// Registra as funções personalizadas no Twig
TwigFunctions::addCustomFunctions($twig);

// Torna a sessão disponível em todas as views
$twig->addGlobal('session', $_SESSION);

// Load routes
$dispatcher = simpleDispatcher(function(RouteCollector $r) {
    // Passa o $r explicitamente para web.php
    require __DIR__ . '/../src/Routes/web.php';
});

$pdo = Connection::getInstance();

// Fetch method and URI
$httpMethod = $_SERVER['REQUEST_METHOD'];
$uri = $_SERVER['REQUEST_URI'];

// Strip query string
if (false !== $pos = strpos($uri, '?')) {
    $uri = substr($uri, 0, $pos);
}
$uri = rawurldecode($uri);

// Dispatch
$routeInfo = $dispatcher->dispatch($httpMethod, $uri);
switch ($routeInfo[0]) {
    case Dispatcher::NOT_FOUND:
        http_response_code(404);
        echo $twig->render('errors/404.twig');
        break;

    case Dispatcher::METHOD_NOT_ALLOWED:
        http_response_code(405);
        echo $twig->render('errors/405.twig', ['allowed' => $routeInfo[1]]);
        break;

    case Dispatcher::FOUND:
        [$handler, $vars] = [$routeInfo[1], $routeInfo[2]];

        // Se for closure, injeta $twig e $pdo
        if (is_callable($handler)) {
            echo $handler($twig, $pdo, ...array_values($vars));
        } else {
            // Handler padrão [ControllerClass, method]
            [$controllerClass, $method] = $handler;
            $controller = new $controllerClass($twig, $pdo);
            echo call_user_func_array([$controller, $method], $vars);
        }
        break;
}
