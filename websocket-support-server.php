<?php
// Suprimir avisos de depreciação
error_reporting(E_ALL & ~E_DEPRECATED);

require __DIR__ . '/vendor/autoload.php';

use Ratchet\Http\HttpServer;
use Ratchet\Server\IoServer;
use Ratchet\WebSocket\WsServer;
use Server\SupportServer;

// Carrega as configurações do banco de dados
$dbConfig = require __DIR__ . '/config/database.php';

try {
    // Conecta ao banco de dados
    $pdo = new PDO(
        "mysql:host={$dbConfig['host']};dbname={$dbConfig['database']};charset=utf8mb4",
        $dbConfig['username'],
        $dbConfig['password']
    );
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Inicia o servidor WebSocket de suporte
    $server = IoServer::factory(
        new HttpServer(
            new WsServer(
                new SupportServer($pdo)
            )
        ),
        8081 // Porta diferente!
    );

    echo "Servidor WebSocket de suporte iniciado na porta 8081\n";
    echo "Pressione Ctrl+C para parar\n";

    $server->run();

} catch (\Exception $e) {
    echo "Erro ao iniciar o servidor de suporte: " . $e->getMessage() . "\n";
    exit(1);
}
