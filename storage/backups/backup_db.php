<?php
// storage/backups/backup_db.php
$dbHost = 'localhost';
$dbName = 'discursivamente_db';
$dbUser = 'root';
$dbPass = 'root';
$backupDir = __DIR__ . '/';

$date = date('Y-m-d_H-i-s');
$backupFile = $backupDir . 'backup_' . $dbName . '_' . $date . '.sql';

// Comando para backup utilizando mysqldump (ajuste conforme seu ambiente)
$command = "mysqldump --host={$dbHost} --user={$dbUser} --password={$dbPass} {$dbName} > {$backupFile}";

system($command, $status);

if ($status === 0) {
    echo "Backup realizado com sucesso: {$backupFile}";
} else {
    echo "Erro ao realizar o backup.";
}
?>
