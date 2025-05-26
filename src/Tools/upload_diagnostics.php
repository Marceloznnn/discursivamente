<?php
// Ferramenta de diagnóstico para verificar os limites de upload do PHP

require_once __DIR__ . '/../../vendor/autoload.php';

use Config\Logger;

// Inicializar o logger
Logger::init();

// Verifica se está sendo executado via CLI ou web
$isCli = php_sapi_name() === 'cli';

// Funções de saída
function output($message) {
    global $isCli;
    if ($isCli) {
        echo $message . PHP_EOL;
    } else {
        echo $message . "<br>";
    }
}

// Cabeçalho
output("=== DIAGNÓSTICO DE LIMITES DE UPLOAD ===");
output("Data: " . date('Y-m-d H:i:s'));

// Verificar limites configurados no PHP
output("\n--- LIMITES PHP ---");
$upload_max_filesize = ini_get('upload_max_filesize');
$post_max_size = ini_get('post_max_size');
$memory_limit = ini_get('memory_limit');
$max_execution_time = ini_get('max_execution_time');
$max_input_time = ini_get('max_input_time');

output("upload_max_filesize: " . $upload_max_filesize);
output("post_max_size: " . $post_max_size);
output("memory_limit: " . $memory_limit);
output("max_execution_time: " . $max_execution_time . " segundos");
output("max_input_time: " . $max_input_time . " segundos");

// Converter para bytes para comparações
function convert_to_bytes($value) {
    $value = trim($value);
    $last = strtolower($value[strlen($value)-1]);
    $value = (int) $value;
    
    switch($last) {
        case 'g':
            $value *= 1024;
        case 'm':
            $value *= 1024;
        case 'k':
            $value *= 1024;
    }
    
    return $value;
}

$upload_max_filesize_bytes = convert_to_bytes($upload_max_filesize);
$post_max_size_bytes = convert_to_bytes($post_max_size);
$memory_limit_bytes = convert_to_bytes($memory_limit);

output("\n--- LIMITES EM BYTES ---");
output("upload_max_filesize: " . number_format($upload_max_filesize_bytes) . " bytes");
output("post_max_size: " . number_format($post_max_size_bytes) . " bytes");
output("memory_limit: " . number_format($memory_limit_bytes) . " bytes");

// Verificar limites nas configurações da aplicação
output("\n--- LIMITES NA APLICAÇÃO ---");

// Tenta carregar as constantes do UploadConfig
try {
    $image_max = \Config\UploadConfig::MAX_IMAGE_SIZE;
    $video_max = \Config\UploadConfig::MAX_VIDEO_SIZE;
    $audio_max = \Config\UploadConfig::MAX_AUDIO_SIZE;
    $pdf_max = \Config\UploadConfig::MAX_PDF_SIZE;
    
    output("UploadConfig::MAX_IMAGE_SIZE: " . number_format($image_max) . " bytes (" . round($image_max / 1024 / 1024, 2) . " MB)");
    output("UploadConfig::MAX_VIDEO_SIZE: " . number_format($video_max) . " bytes (" . round($video_max / 1024 / 1024, 2) . " MB)");
    output("UploadConfig::MAX_AUDIO_SIZE: " . number_format($audio_max) . " bytes (" . round($audio_max / 1024 / 1024, 2) . " MB)");
    output("UploadConfig::MAX_PDF_SIZE: " . number_format($pdf_max) . " bytes (" . round($pdf_max / 1024 / 1024, 2) . " MB)");
} catch (\Throwable $e) {
    output("Erro ao carregar UploadConfig: " . $e->getMessage());
}

// Verificar arquivos de configuração
output("\n--- ARQUIVOS DE CONFIGURAÇÃO ---");

// .user.ini
$user_ini_path = __DIR__ . '/../../public/.user.ini';
if (file_exists($user_ini_path)) {
    output(".user.ini: Arquivo existe");
    $user_ini_content = file_get_contents($user_ini_path);
    preg_match('/upload_max_filesize\s*=\s*([^\s\r\n]+)/i', $user_ini_content, $matches);
    if (!empty($matches[1])) {
        output("  - upload_max_filesize definido como: " . $matches[1]);
    }
    preg_match('/post_max_size\s*=\s*([^\s\r\n]+)/i', $user_ini_content, $matches);
    if (!empty($matches[1])) {
        output("  - post_max_size definido como: " . $matches[1]);
    }
} else {
    output(".user.ini: Arquivo não existe");
}

// .htaccess
$htaccess_path = __DIR__ . '/../../public/.htaccess';
if (file_exists($htaccess_path)) {
    output(".htaccess: Arquivo existe");
    $htaccess_content = file_get_contents($htaccess_path);
    preg_match('/php_value\s+upload_max_filesize\s+([^\s\r\n]+)/i', $htaccess_content, $matches);
    if (!empty($matches[1])) {
        output("  - php_value upload_max_filesize definido como: " . $matches[1]);
    }
    preg_match('/php_value\s+post_max_size\s+([^\s\r\n]+)/i', $htaccess_content, $matches);
    if (!empty($matches[1])) {
        output("  - php_value post_max_size definido como: " . $matches[1]);
    }
} else {
    output(".htaccess: Arquivo não existe");
}

// Verificar diretórios de upload
output("\n--- DIRETÓRIOS DE UPLOAD ---");
$upload_dir = __DIR__ . '/../../public/uploads';
if (is_dir($upload_dir)) {
    output("Diretório de uploads: Existe");
    output("  - Permissões: " . substr(sprintf('%o', fileperms($upload_dir)), -4));
    output("  - Espaço livre: " . round(disk_free_space($upload_dir) / 1024 / 1024 / 1024, 2) . " GB");
    output("  - Espaço total: " . round(disk_total_space($upload_dir) / 1024 / 1024 / 1024, 2) . " GB");
} else {
    output("Diretório de uploads: Não existe");
}

// Verificar mod_php vs CGI/FastCGI
output("\n--- AMBIENTE PHP ---");
output("SAPI: " . php_sapi_name());
output("PHP Version: " . PHP_VERSION);
output("Server Software: " . ($_SERVER['SERVER_SOFTWARE'] ?? 'N/A'));
output("Modo de execução: " . (php_sapi_name() == 'apache2handler' ? 'mod_php (Apache module)' : 'CGI/FastCGI'));

// Registrar todos os dados nos logs
$all_data = [
    'php_limits' => [
        'upload_max_filesize' => $upload_max_filesize,
        'upload_max_filesize_bytes' => $upload_max_filesize_bytes,
        'post_max_size' => $post_max_size,
        'post_max_size_bytes' => $post_max_size_bytes,
        'memory_limit' => $memory_limit,
        'memory_limit_bytes' => $memory_limit_bytes,
        'max_execution_time' => $max_execution_time,
        'max_input_time' => $max_input_time
    ],
    'app_limits' => [
        'MAX_IMAGE_SIZE' => $image_max ?? 'Não definido',
        'MAX_VIDEO_SIZE' => $video_max ?? 'Não definido',
        'MAX_AUDIO_SIZE' => $audio_max ?? 'Não definido',
        'MAX_PDF_SIZE' => $pdf_max ?? 'Não definido'
    ],
    'environment' => [
        'sapi' => php_sapi_name(),
        'php_version' => PHP_VERSION,
        'server_software' => $_SERVER['SERVER_SOFTWARE'] ?? 'N/A'
    ],
    'disk_space' => [
        'free_space_gb' => round(disk_free_space($upload_dir) / 1024 / 1024 / 1024, 2),
        'total_space_gb' => round(disk_total_space($upload_dir) / 1024 / 1024 / 1024, 2)
    ]
];

Logger::config('Diagnóstico de limites de upload', $all_data);

// Verificar qual limite tem precedência
output("\n--- ANÁLISE DE LIMITE EFETIVO ---");

// Determinar qual é o limite efetivo (menor entre upload_max_filesize e post_max_size)
$effective_upload_limit = min($upload_max_filesize_bytes, $post_max_size_bytes);
output("Limite efetivo de upload (PHP): " . round($effective_upload_limit / 1024 / 1024, 2) . " MB");

// Compatibilidade com os limites da aplicação
if (isset($video_max)) {
    if ($video_max > $effective_upload_limit) {
        output("AVISO: O limite de vídeo na aplicação (" . round($video_max / 1024 / 1024, 2) . " MB) excede o limite efetivo do PHP!");
    } else {
        output("O limite de vídeo na aplicação está dentro do permitido pelo PHP");
    }
}

if (isset($image_max)) {
    if ($image_max > $effective_upload_limit) {
        output("AVISO: O limite de imagem na aplicação (" . round($image_max / 1024 / 1024, 2) . " MB) excede o limite efetivo do PHP!");
    } else {
        output("O limite de imagem na aplicação está dentro do permitido pelo PHP");
    }
}

output("\n=== FIM DO DIAGNÓSTICO ===");

// Gerar saída HTML se não for CLI
if (!$isCli) {
    echo '<hr>';
    echo '<h3>Recomendações:</h3>';
    
    if ($effective_upload_limit < 100 * 1024 * 1024) { // Menos que 100MB
        echo '<p style="color: red;">O limite atual de upload é menor que 100MB. Sugerimos aumentar os seguintes parâmetros:</p>';
        echo '<pre>
php_value upload_max_filesize 100M
php_value post_max_size 100M
php_value max_execution_time 300
php_value max_input_time 300
</pre>';
    }
    
    if (isset($video_max) && $video_max > $effective_upload_limit) {
        echo '<p style="color: red;">O limite de vídeo configurado na aplicação é maior que o permitido pelo PHP. Ajuste o valor em UploadConfig.php ou aumente o limite do PHP.</p>';
    }
}
