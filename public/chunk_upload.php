<?php
// Controlador de upload em chunks (partes) para lidar com arquivos muito grandes
// Este arquivo é usado pelo advanced-uploader.js para uploads em partes

require_once __DIR__ . '/../vendor/autoload.php';

use Config\UploadConfig;
use Config\Logger;

// Inicializar o logger de forma segura
if (class_exists('\\Config\\Logger')) {
    try {
        Logger::init();
    } catch (\Exception $e) {
        error_log("Erro ao inicializar o logger: " . $e->getMessage());
    }
}

// Verificar o método da requisição
$method = $_SERVER['REQUEST_METHOD'];

// Responder com cabeçalhos JSON
header('Content-Type: application/json');

// Definir caminho para salvar os chunks temporários
$chunksDir = __DIR__ . '/uploads/chunks';
if (!is_dir($chunksDir)) {
    mkdir($chunksDir, 0777, true);
}

// Definir funções auxiliares
function generateUniqueId() {
    return md5(uniqid(mt_rand(), true));
}

function cleanOldChunks($directory, $maxAge = 86400) { // 24 horas por padrão
    if (is_dir($directory)) {
        $files = scandir($directory);
        $now = time();
        foreach ($files as $file) {
            if ($file === '.' || $file === '..') continue;
            
            $filePath = $directory . '/' . $file;
            $fileAge = $now - filemtime($filePath);
            
            if ($fileAge > $maxAge) {
                @unlink($filePath);
                if (class_exists('\\Config\\Logger')) {
                    try {
                        Logger::log("Removido chunk antigo", [
                            "arquivo" => $file,
                            "idade" => round($fileAge / 3600, 2) . " horas"
                        ], "debug");
                    } catch (\Exception $e) {
                        error_log("Erro ao registrar log: " . $e->getMessage());
                    }
                }
            }
        }
    }
}

// Roteamento básico para os endpoints de chunks
$endpoint = isset($_GET['action']) ? $_GET['action'] : 'upload';

// Limpar chunks antigos
cleanOldChunks($chunksDir);

// Roteamento das ações
switch ($endpoint) {
    case 'chunk':
        handleChunkUpload();
        break;
        
    case 'finalize':
        finalizeChunks();
        break;
        
    case 'status':
        getUploadStatus();
        break;
    
    default:
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => 'Endpoint inválido'
        ]);
        exit;
}

/**
 * Manipula o upload de um chunk
 */
function handleChunkUpload() {
    global $chunksDir;
    
    // Validar dados necessários
    if (!isset($_FILES['chunk']) || !isset($_POST['chunkIndex']) || 
        !isset($_POST['totalChunks']) || !isset($_POST['fileName'])) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => 'Parâmetros ausentes'
        ]);
        exit;
    }
    
    // Obter dados do chunk
    $chunkFile = $_FILES['chunk'];
    $chunkIndex = (int)$_POST['chunkIndex'];
    $totalChunks = (int)$_POST['totalChunks'];
    $fileName = $_POST['fileName'];
    $fileSize = $_POST['fileSize'] ?? 0;
    $fileType = $_POST['fileType'] ?? '';
    
    // Validar possíveis erros no upload
    if ($chunkFile['error'] !== UPLOAD_ERR_OK) {
        $errorMessages = [
            UPLOAD_ERR_INI_SIZE => 'O arquivo excede o limite definido no php.ini',
            UPLOAD_ERR_FORM_SIZE => 'O arquivo excede o limite definido no formulário',
            UPLOAD_ERR_PARTIAL => 'O arquivo foi apenas parcialmente carregado',
            UPLOAD_ERR_NO_FILE => 'Nenhum arquivo foi carregado',
            UPLOAD_ERR_NO_TMP_DIR => 'Pasta temporária ausente',
            UPLOAD_ERR_CANT_WRITE => 'Falha ao escrever arquivo em disco',
            UPLOAD_ERR_EXTENSION => 'Uma extensão PHP interrompeu o upload'
        ];
        
        $errorMessage = $errorMessages[$chunkFile['error']] ?? 'Erro desconhecido';
        
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => 'Erro no upload: ' . $errorMessage,
            'errorCode' => $chunkFile['error']
        ]);
          if (class_exists('\\Config\\Logger')) {
            try {
                Logger::error("Erro no upload de chunk", [
                    'erro' => $errorMessage,
                    'codigo' => $chunkFile['error'],
                    'chunk' => $chunkIndex,
                    'total' => $totalChunks,
                    'arquivo' => $fileName
                ]);
            } catch (\Exception $e) {
                error_log("Erro ao registrar log: " . $e->getMessage());
            }
        } else {
            error_log("Erro no upload de chunk: " . $errorMessage);
        }
        
        exit;
    }
    
    // Criar uma pasta específica para este arquivo
    $fileId = '';
    
    // Se for o primeiro chunk, gerar um ID para o arquivo
    if ($chunkIndex === 0) {
        $fileId = generateUniqueId();
        $fileDir = $chunksDir . '/' . $fileId;
        
        if (!is_dir($fileDir)) {
            mkdir($fileDir, 0777, true);
        }
        
        // Salvar metadados do arquivo
        file_put_contents($fileDir . '/metadata.json', json_encode([
            'fileName' => $fileName,
            'fileSize' => $fileSize,
            'fileType' => $fileType,
            'totalChunks' => $totalChunks,
            'timestamp' => time(),
            'chunksReceived' => []
        ]));
    } else {
        // Para chunks subsequentes, o ID deve ser fornecido
        if (!isset($_POST['fileId'])) {
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'message' => 'ID do arquivo não fornecido para o chunk'
            ]);
            exit;
        }
        
        $fileId = $_POST['fileId'];
        $fileDir = $chunksDir . '/' . $fileId;
        
        if (!is_dir($fileDir)) {
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'message' => 'ID do arquivo inválido'
            ]);
            exit;
        }
        
        // Carregar metadados
        $metadataFile = $fileDir . '/metadata.json';
        if (!file_exists($metadataFile)) {
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'message' => 'Metadados do arquivo não encontrados'
            ]);
            exit;
        }
        
        $metadata = json_decode(file_get_contents($metadataFile), true);
    }
    
    // Salvar o chunk
    $chunkPath = $fileDir . '/chunk_' . $chunkIndex;
    if (!move_uploaded_file($chunkFile['tmp_name'], $chunkPath)) {
        http_response_code(500);
        echo json_encode([
            'success' => false,
            'message' => 'Falha ao salvar o chunk'
        ]);
        
        Logger::error("Falha ao salvar chunk", [
            'chunk' => $chunkIndex,
            'total' => $totalChunks,
            'arquivo' => $fileName,
            'caminho' => $chunkPath
        ]);
        
        exit;
    }
    
    // Atualizar metadados
    $metadata = json_decode(file_get_contents($fileDir . '/metadata.json'), true);
    $metadata['chunksReceived'][] = $chunkIndex;
    file_put_contents($fileDir . '/metadata.json', json_encode($metadata));
    
    // Registrar o chunk
    Logger::log("Chunk recebido com sucesso", [
        'chunk' => $chunkIndex + 1, // +1 para exibir base 1 ao invés de 0
        'total' => $totalChunks,
        'arquivo' => $fileName,
        'tamanho' => formatBytes($chunkFile['size']),
        'progresso' => round((count($metadata['chunksReceived']) / $totalChunks) * 100) . '%'
    ], "debug");
    
    // Responder ao cliente
    echo json_encode([
        'success' => true,
        'fileId' => $fileId,
        'message' => "Chunk {$chunkIndex} recebido com sucesso",
        'chunkIndex' => $chunkIndex,
        'received' => count($metadata['chunksReceived']),
        'total' => $totalChunks
    ]);
}

/**
 * Finaliza o upload combinando todos os chunks
 */
function finalizeChunks() {
    global $chunksDir;
    
    // Validar parâmetros
    if (!isset($_POST['fileId'])) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => 'ID do arquivo não fornecido'
        ]);
        exit;
    }
    
    $fileId = $_POST['fileId'];
    $fileDir = $chunksDir . '/' . $fileId;
    
    if (!is_dir($fileDir)) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => 'ID do arquivo inválido'
        ]);
        exit;
    }
    
    // Carregar metadados
    $metadataFile = $fileDir . '/metadata.json';
    if (!file_exists($metadataFile)) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => 'Metadados do arquivo não encontrados'
        ]);
        exit;
    }
    
    $metadata = json_decode(file_get_contents($metadataFile), true);
    $fileName = $metadata['fileName'];
    $fileType = $metadata['fileType'];
    $totalChunks = $metadata['totalChunks'];
    
    // Verificar se todos os chunks foram recebidos
    $receivedChunks = count($metadata['chunksReceived']);
    if ($receivedChunks < $totalChunks) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => "Faltam chunks: recebidos {$receivedChunks} de {$totalChunks}"
        ]);
        exit;
    }
    
    // Criar arquivo final
    $uploadsDir = __DIR__ . '/uploads/temp';
    if (!is_dir($uploadsDir)) {
        mkdir($uploadsDir, 0777, true);
    }
    
    // Criar nome de arquivo temporário único
    $targetFilename = uniqid('upload_') . '_' . preg_replace('/[^a-zA-Z0-9_.-]/', '', $fileName);
    $targetPath = $uploadsDir . '/' . $targetFilename;
    
    // Combinar os chunks
    $out = fopen($targetPath, 'wb');
    
    if ($out) {
        try {
            // Combinar chunks em ordem
            for ($i = 0; $i < $totalChunks; $i++) {
                $chunkPath = $fileDir . '/chunk_' . $i;
                
                if (!file_exists($chunkPath)) {
                    throw new Exception("Chunk {$i} não encontrado");
                }
                
                $in = fopen($chunkPath, 'rb');
                stream_copy_to_stream($in, $out);
                fclose($in);
            }
            
            fclose($out);
            
            // Verificar o tamanho final
            $finalSize = filesize($targetPath);
            $expectedSize = (int) $metadata['fileSize'];
            
            if ($finalSize != $expectedSize && $expectedSize > 0) {
                Logger::log("Tamanho do arquivo final é diferente do esperado", [
                    'arquivo' => $fileName,
                    'esperado' => formatBytes($expectedSize),
                    'recebido' => formatBytes($finalSize),
                    'diferenca' => formatBytes(abs($finalSize - $expectedSize))
                ], 'warning');
            }
            
            // Registrar sucesso
            Logger::log("Upload em chunks finalizado com sucesso", [
                'arquivo' => $fileName,
                'tamanho' => formatBytes($finalSize),
                'tipo' => $fileType,
                'caminho' => $targetPath,
                'chunks' => $totalChunks
            ]);
            
            // Processar o arquivo - aqui você poderia usar o CloudinaryService ou outro serviço
            $uploadResult = [
                'success' => true,
                'message' => 'Upload concluído com sucesso',
                'fileName' => $fileName,
                'fileType' => $fileType,
                'fileSize' => formatBytes($finalSize),
                'filePath' => $targetPath,
                'url' => '/uploads/temp/' . $targetFilename
            ];
            
            // Limpar os chunks
            // Descomentado em produção: recursiveRmdir($fileDir);
            
            echo json_encode($uploadResult);
            
        } catch (Exception $e) {
            fclose($out);
            @unlink($targetPath); // Remover arquivo parcial
            
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'message' => 'Erro ao combinar chunks: ' . $e->getMessage()
            ]);
            
            Logger::error("Erro ao combinar chunks", [
                'arquivo' => $fileName,
                'erro' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    } else {
        http_response_code(500);
        echo json_encode([
            'success' => false,
            'message' => 'Erro ao criar arquivo final'
        ]);
        
        Logger::error("Erro ao criar arquivo final", [
            'arquivo' => $fileName,
            'caminho' => $targetPath,
            'erro' => error_get_last()
        ]);
    }
}

/**
 * Verifica o status de um upload em progresso
 */
function getUploadStatus() {
    global $chunksDir;
    
    if (!isset($_GET['fileId'])) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => 'ID do arquivo não fornecido'
        ]);
        exit;
    }
    
    $fileId = $_GET['fileId'];
    $fileDir = $chunksDir . '/' . $fileId;
    
    if (!is_dir($fileDir)) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => 'ID do arquivo inválido'
        ]);
        exit;
    }
    
    $metadataFile = $fileDir . '/metadata.json';
    if (!file_exists($metadataFile)) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => 'Metadados do arquivo não encontrados'
        ]);
        exit;
    }
    
    $metadata = json_decode(file_get_contents($metadataFile), true);
    $receivedChunks = count($metadata['chunksReceived']);
    
    echo json_encode([
        'success' => true,
        'fileName' => $metadata['fileName'],
        'fileSize' => $metadata['fileSize'],
        'fileType' => $metadata['fileType'],
        'totalChunks' => $metadata['totalChunks'],
        'receivedChunks' => $receivedChunks,
        'progress' => round(($receivedChunks / $metadata['totalChunks']) * 100),
        'timestamp' => $metadata['timestamp'],
        'elapsedTime' => time() - $metadata['timestamp'] . 's'
    ]);
}

/**
 * Remove recursivamente um diretório e seu conteúdo
 */
function recursiveRmdir($dir) {
    if (!is_dir($dir)) {
        return;
    }
    
    $files = scandir($dir);
    foreach ($files as $file) {
        if ($file === '.' || $file === '..') continue;
        
        $path = $dir . '/' . $file;
        if (is_dir($path)) {
            recursiveRmdir($path);
        } else {
            unlink($path);
        }
    }
    
    return rmdir($dir);
}

/**
 * Formata bytes para unidades legíveis
 */
function formatBytes($bytes, $precision = 2) {
    $units = ['B', 'KB', 'MB', 'GB', 'TB'];
    $bytes = max($bytes, 0);
    $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
    $pow = min($pow, count($units) - 1);
    $bytes /= (1 << (10 * $pow));
    
    return round($bytes, $precision) . ' ' . $units[$pow];
}
?>
