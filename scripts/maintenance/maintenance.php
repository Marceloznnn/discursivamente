#!/usr/bin/env php
<?php
// filepath: scripts/maintenance/maintenance.php
// ----------------------------------------------------------------
// Script: maintenance.php
// Localização: scripts/maintenance/
// Atualize caminhos relativos após mover o arquivo para manter a estrutura:
//   project-root/
//   ├── vendor/
//   ├── tmp/
//   ├── var/
//   └── scripts/
//       ├── clear_cache.php
//       ├── backup_db.sh
//       └── maintenance/
//           └── maintenance.php   <-- este arquivo
// ----------------------------------------------------------------

// Ajustar require para apontar ao autoload do Composer
require __DIR__ . '/../../vendor/autoload.php';

use Infrastructure\Database\Connection;

// Diretórios do projeto (raiz)
$projectRoot   = realpath(__DIR__ . '/../../');
$tempDir       = $projectRoot . DIRECTORY_SEPARATOR . 'tmp';
$logsDir       = $projectRoot . DIRECTORY_SEPARATOR . 'logs';

// Usa o path real de sessões configurado no php.ini (ou temp como fallback)
$sessionDir    = session_save_path() ?: sys_get_temp_dir();
$sessionMaxAge = 7 * 24 * 60 * 60; // 7 dias em segundos

$pathsToCheck = [
    $tempDir => 0777,
    $logsDir => 0755,
    $projectRoot => 0755,
];

// Funções de manutenção (idem ao original, sem alteração)

function checkDiskSpace(string $path, int $warningPercent = 90): void
{
    $total = @disk_total_space($path);
    $free  = @disk_free_space($path);
    if ($total === false || $free === false) {
        echo "Não foi possível verificar disco em {$path}\n";
        return;
    }
    $used = $total - $free;
    $pct  = ($used / $total) * 100;
    echo sprintf("Uso de disco em %s: %.2f%%\n", $path, $pct);
    if ($pct > $warningPercent) {
        echo ">>> Atenção: uso acima de {$warningPercent}%!\n";
    }
}

function optimizeDatabase(): void
{
    try {
        $pdo = Connection::getInstance();
        $pdo->setAttribute(PDO::MYSQL_ATTR_USE_BUFFERED_QUERY, true);
        $stmt = $pdo->prepare("SHOW TABLES");
        $stmt->execute();
        $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
        $stmt->closeCursor();
        foreach ($tables as $table) {
            echo "Otimizando tabela `{$table}`...\n";
            $stmt2 = $pdo->query("OPTIMIZE TABLE `{$table}`");
            $stmt2?->closeCursor();
        }
    } catch (Exception $e) {
        echo "Erro ao otimizar banco: " . $e->getMessage() . "\n";
    }
}

function checkPermissions(array $paths): void
{
    if (PHP_OS_FAMILY === 'Windows') {
        echo "Pulo checagem de permissões no Windows.\n";
        return;
    }
    foreach ($paths as $path => $expected) {
        if (!file_exists($path)) {
            echo "Caminho não existe: {$path}\n";
            continue;
        }
        $actual = fileperms($path) & 0x1FF;
        if ($actual !== $expected) {
            echo sprintf(
                "Permissão em %s: atual %o, deveria %o\n",
                $path, $actual, $expected
            );
        } else {
            echo "Permissão OK em {$path}\n";
        }
    }
}

function cleanTempFiles(string $dir, int $maxAge = 86400): void
{
    if (!is_dir($dir)) {
        echo "Diretório de temporários não existe: {$dir}\n";
        return;
    }
    $now = time();
    foreach (new DirectoryIterator($dir) as $f) {
        if (!$f->isFile()) continue;
        if ($now - $f->getMTime() > $maxAge) {
            echo "Removendo temp: " . $f->getFilename() . "\n";
            @unlink($f->getPathname());
        }
    }
}

function cleanOldSessions(string $dir, int $maxAge): void
{
    if (!is_dir($dir)) {
        echo "Diretório de sessões não existe: {$dir}\n";
        return;
    }
    $now = time();
    foreach (new DirectoryIterator($dir) as $f) {
        if (!$f->isFile()) continue;
        if ($now - $f->getCTime() > $maxAge) {
            echo "Removendo sessão: " . $f->getFilename() . "\n";
            @unlink($f->getPathname());
        }
    }
}

// ===== Execução principal =====
echo "=== Início da manutenção: " . date('Y-m-d H:i:s') . " ===\n";

checkDiskSpace($projectRoot);
optimizeDatabase();
checkPermissions($pathsToCheck);
cleanTempFiles($tempDir);
cleanOldSessions($sessionDir, $sessionMaxAge);

echo "=== Fim da manutenção: " . date('Y-m-d H:i:s') . " ===\n";
