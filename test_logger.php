<?php
// Script de teste para verificar se o CloudinaryService agora pode usar o Logger
require_once __DIR__ . '/vendor/autoload.php';

echo "Verificando se a classe Logger pode ser instanciada...\n";
try {
    $logger = new Config\Logger();
    echo "✅ Logger carregado com sucesso\n";
} catch (\Exception $e) {
    echo "❌ Erro ao carregar Logger: " . $e->getMessage() . "\n";
}

echo "Verificando se o CloudinaryService pode ser instanciado...\n";
try {
    $service = new Services\CloudinaryService();
    echo "✅ CloudinaryService carregado com sucesso\n";
} catch (\Exception $e) {
    echo "❌ Erro ao carregar CloudinaryService: " . $e->getMessage() . "\n";
}

echo "Verificando se o método upload pode ser chamado...\n";
try {
    // Forneça um caminho para um arquivo falso, esperamos um erro de "arquivo não encontrado"
    // mas não um erro de classe não encontrada
    $service->upload('arquivo_teste.txt');
    echo "Isto não deveria ser exibido, esperamos um erro de arquivo não encontrado\n";
} catch (\Exception $e) {
    echo "Erro esperado: " . $e->getMessage() . "\n";
    if (strpos($e->getMessage(), "Arquivo não encontrado") !== false) {
        echo "✅ O método upload foi chamado corretamente, recebendo o erro esperado\n";
    } else {
        echo "❌ Erro inesperado: " . $e->getMessage() . "\n";
    }
}

echo "Teste concluído!\n";
