<?php
// rotate_logs.php
$logs = ['logs/access.log', 'logs/error.log'];
$archiveDir = 'logs/archive/';

// Cria o diretório de arquivos arquivados, se não existir
if (!is_dir($archiveDir)) {
    mkdir($archiveDir, 0755, true);
}

foreach ($logs as $logFile) {
    if (file_exists($logFile)) {
        $date = date('Y-m-d_H-i-s');
        $archiveFile = $archiveDir . basename($logFile) . '.' . $date;
        rename($logFile, $archiveFile);
        // Cria um novo arquivo vazio para o log atual
        touch($logFile);
    }
}
echo "Logs rotacionados com sucesso.";
?>
