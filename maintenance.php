#!/usr/bin/env php
<?php
// maintenance.php

require __DIR__ . '/vendor/autoload.php';

use Infrastructure\Database\Connection;

// Configurações gerais
$pathsToCheck = [
    __DIR__ . '/tmp'        => 0777,
    __DIR__ . '/logs'       => 0755,
    __DIR__                  => 0755,
];

$tempDir       = __DIR__ . '/tmp';
$sessionDir    = sys_get_temp_dir() . '/sessions'; // adapte se usar pasta custom
$sessionMaxAge = 7 * 24 * 60 * 60; // 7 dias em segundos

// 1. Verificar espaço em disco
function checkDiskSpace(string $path = '/', int $warningPercent = 90): void
{
    $total = disk_total_space($path);
    $free  = disk_free_space($path);
    $used  = $total - $free;
    $percentUsed = ($used / $total) * 100;

    echo sprintf("Uso de disco em %s: %.2f%%\n", $path, $percentUsed);

    if ($percentUsed > $warningPercent) {
        echo ">>> Atenção: uso de disco acima de {$warningPercent}%!\n";
    }
}

// 2. Otimizar banco de dados usando Connection singleton
function optimizeDatabase(): void
{
    try {
        $pdo = Connection::getInstance();
        $stmt = $pdo->query("SHOW TABLES");
        $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);

        foreach ($tables as $table) {
            echo "Otimizando tabela `{$table}`...\n";
            $pdo->exec("OPTIMIZE TABLE `{$table}`");
        }
    } catch (\Exception $e) {
        echo "Erro ao otimizar banco: " . $e->getMessage() . "\n";
    }
}

// 3. Verificar permissões
function checkPermissions(array $paths): void
{
    foreach ($paths as $path => $expectedPerm) {
        if (!file_exists($path)) {
            echo "Caminho não encontrado: {$path}\n";
            continue;
        }
        $actualPerm = fileperms($path) & 0x1FF;
        if ($actualPerm !== $expectedPerm) {
            echo sprintf(
                "Permissão incorreta em %s: atual %o, esperado %o\n",
                $path, $actualPerm, $expectedPerm
            );
            // chmod($path, $expectedPerm);
        } else {
            echo "Permissão OK em {$path}\n";
        }
    }
}

// 4. Limpar arquivos temporários antigos
function cleanTempFiles(string $dir, int $maxAgeSeconds = 86400): void
{
    $now = time();
    foreach (new DirectoryIterator($dir) as $fileinfo) {
        if ($fileinfo->isDot() || !$fileinfo->isFile()) continue;
        if (($now - $fileinfo->getMTime()) > $maxAgeSeconds) {
            echo "Removendo temporário: " . $fileinfo->getFilename() . "\n";
            @unlink($fileinfo->getPathname());
        }
    }
}

// 5. Limpar sessões antigas
function cleanOldSessions(string $dir, int $maxAgeSeconds): void
{
    $now = time();
    if (!is_dir($dir)) {
        echo "Diretório de sessões não encontrado: {$dir}\n";
        return;
    }
    foreach (new DirectoryIterator($dir) as $fileinfo) {
        if ($fileinfo->isDot() || !$fileinfo->isFile()) continue;
        if (($now - $fileinfo->getCTime()) > $maxAgeSeconds) {
            echo "Removendo sessão antiga: " . $fileinfo->getFilename() . "\n";
            @unlink($fileinfo->getPathname());
        }
    }
}

// === Execução das tarefas ===
echo "=== Início da manutenção: " . date('Y-m-d H:i:s') . " ===\n";

checkDiskSpace(__DIR__);
optimizeDatabase();
checkPermissions($pathsToCheck);
cleanTempFiles($tempDir);
cleanOldSessions($sessionDir, $sessionMaxAge);

echo "=== Fim da manutenção: " . date('Y-m-d H:i:s') . " ===\n";
