<?php
/**
 * DISCURSIVAMENTE - Ponto de entrada principal da aplicação
 */

define('ROOT_PATH', dirname(__DIR__));

require_once ROOT_PATH . '/vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(ROOT_PATH);
$dotenv->load();

session_start();

error_reporting(E_ALL);
ini_set('display_errors', $_ENV['APP_DEBUG'] ? 1 : 0);

// Se a URL começar com "/api", utiliza o roteador da API
$requestPath = $_SERVER['REQUEST_URI'] ?? '/';
if (strpos($requestPath, '/api') === 0) {
    require_once ROOT_PATH . '/config/ApiRouter.php';
    exit;
}

// Inclui o arquivo de rotas web (que já trata a resolução das rotas)
require_once ROOT_PATH . '/src/Routes/web.php';
?>
