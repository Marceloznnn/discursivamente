<?php
// Teste para verificar se o CloudinaryService carrega corretamente a classe Logger

// Definir um autoloader básico para teste
spl_autoload_register(function ($class) {
    // Converter namespace para caminho de arquivo
    $prefix = '';
    $base_dir = __DIR__ . '/src/';
    
    // Normalizar namespace com diretório
    $length = strlen($prefix);
    $namespace = $class;
    $file = $base_dir . str_replace('\\', '/', $namespace) . '.php';
    
    // Verificar se o arquivo existe
    if (file_exists($file)) {
        require $file;
        return true;
    }
    
    return false;
});

// Carregar manualmente o autoload do Composer
require __DIR__ . '/vendor/autoload.php';

// Tentar criar uma instância do CloudinaryService
try {
    echo "Iniciando teste do CloudinaryService e Logger...\n";
    
    echo "Verificando se a classe Logger existe...\n";
    if (class_exists('\\Config\\Logger')) {
        echo "SUCESSO: A classe Config\\Logger foi encontrada!\n";
    } else {
        echo "ERRO: A classe Config\\Logger não foi encontrada!\n";
    }
    
    echo "\nTentando criar uma instância do CloudinaryService...\n";
    $cloudinaryService = new \Services\CloudinaryService();
    echo "SUCESSO: CloudinaryService foi instanciado corretamente!\n";
    
    echo "\nTeste completo. Nenhum erro foi encontrado!\n";
} catch (\Exception $e) {
    echo "ERRO ao instanciar CloudinaryService: " . $e->getMessage() . "\n";
    echo "Trace: " . $e->getTraceAsString() . "\n";
}
