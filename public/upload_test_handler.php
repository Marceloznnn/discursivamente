<?php
// Manipulador de testes de upload - usado pelo upload_test.php

require_once __DIR__ . '/../vendor/autoload.php';

use Config\UploadConfig;
use Config\Logger;

// Inicializar o logger
Logger::init();

// Headers para resposta JSON
header('Content-Type: application/json');

$response = [
    'success' => false,
    'message' => '',
    'fileName' => '',
    'fileSize' => '',
    'fileType' => '',
    'processingTime' => 0,
    'errors' => []
];

// Marcar tempo de início para medir performance
$start_time = microtime(true);

try {
    // Verificar se o arquivo foi enviado
    if (!isset($_FILES['fileToUpload']) || !is_uploaded_file($_FILES['fileToUpload']['tmp_name'])) {
        throw new Exception('Nenhum arquivo foi enviado ou o upload falhou.');
    }

    // Obter informações do arquivo
    $file = $_FILES['fileToUpload'];
    $fileName = $file['name'];
    $fileSize = $file['size'];
    $fileType = $file['type'];
    $tmpName = $file['tmp_name'];
    $error = $file['error'];

    // Formatar tamanho
    function formatBytes($bytes, $precision = 2) {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= (1 << (10 * $pow));
        return round($bytes, $precision) . ' ' . $units[$pow];
    }

    // Processar erro de upload se existir
    if ($error !== UPLOAD_ERR_OK) {
        $errorMessages = [
            UPLOAD_ERR_INI_SIZE => 'O arquivo excede o limite definido em upload_max_filesize no php.ini',
            UPLOAD_ERR_FORM_SIZE => 'O arquivo excede o limite definido em MAX_FILE_SIZE no formulário HTML',
            UPLOAD_ERR_PARTIAL => 'O arquivo foi apenas parcialmente carregado',
            UPLOAD_ERR_NO_FILE => 'Nenhum arquivo foi carregado',
            UPLOAD_ERR_NO_TMP_DIR => 'Pasta temporária ausente',
            UPLOAD_ERR_CANT_WRITE => 'Falha ao escrever arquivo em disco',
            UPLOAD_ERR_EXTENSION => 'Uma extensão PHP interrompeu o upload',
        ];
        
        $errorMessage = $errorMessages[$error] ?? 'Erro desconhecido ao fazer upload';
        throw new Exception($errorMessage);
    }

    // Registrar o upload bem-sucedido
    Logger::log("Teste de upload bem-sucedido", [
        'arquivo' => $fileName,
        'tamanho' => formatBytes($fileSize),
        'tipo' => $fileType,
        'tamanho_bytes' => $fileSize
    ]);

    // Verificar limites do arquivo conforme o tipo
    $mimeType = mime_content_type($tmpName);
    $maxSize = UploadConfig::getMaxSizeForType($mimeType);
    
    $isWithinAppLimit = $fileSize <= $maxSize;
    $phpMaxUpload = min(
        convertToBytes(ini_get('upload_max_filesize')),
        convertToBytes(ini_get('post_max_size'))
    );
    $isWithinPhpLimit = $fileSize <= $phpMaxUpload;
    
    // Mostrar avisos sobre limites
    $warnings = [];
    if (!$isWithinAppLimit) {
        $warnings[] = "Aviso: O arquivo excede o limite da aplicação (" . formatBytes($maxSize) . ") para este tipo de arquivo.";
    }
    if (!$isWithinPhpLimit) {
        $warnings[] = "Aviso: O arquivo excede o limite do PHP (" . formatBytes($phpMaxUpload) . ").";
    }

    // Função para converter string de tamanho para bytes
    function convertToBytes($value) {
        $value = trim($value);
        $last = strtolower($value[strlen($value) - 1]);
        $value = (int) $value;
        
        switch ($last) {
            case 'g':
                $value *= 1024;
            case 'm':
                $value *= 1024;
            case 'k':
                $value *= 1024;
        }
        
        return $value;
    }

    // Não vamos salvar o arquivo permanentemente, apenas testar o upload
    // Aqui você poderia usar o CloudinaryService::upload() se quisesse testar o upload remoto
    
    // Sucesso!
    $response['success'] = true;
    $response['message'] = 'Arquivo carregado e processado com sucesso!' . 
                           (!empty($warnings) ? '<br><br>' . implode('<br>', $warnings) : '');
    $response['fileName'] = htmlspecialchars($fileName);
    $response['fileSize'] = formatBytes($fileSize);
    $response['fileType'] = $fileType;
    $response['processingTime'] = round(microtime(true) - $start_time, 3);

} catch (Exception $e) {
    $response['success'] = false;
    $response['message'] = 'Erro: ' . $e->getMessage();
    $response['processingTime'] = round(microtime(true) - $start_time, 3);
    
    // Registrar erro
    Logger::error("Erro no teste de upload", [
        'mensagem' => $e->getMessage(),
        'trace' => $e->getTraceAsString()
    ]);
}

// Enviar resposta JSON
echo json_encode($response);
