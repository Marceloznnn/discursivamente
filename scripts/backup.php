<?php
/**
 * Script de Backup para o Discursivamente 2.1
 * 
 * Este script cria backups do banco de dados e dos arquivos importantes do projeto.
 * Também inclui funcionalidade para restauração e programação automática.
 */

// Definir a raiz do projeto
$projectRoot = realpath(__DIR__ . '/..');

// Carregar configurações do ambiente
require_once $projectRoot . '/vendor/autoload.php';
$dotenv = Dotenv\Dotenv::createImmutable($projectRoot);
$dotenv->load();

// ====================================
// Configurações
// ====================================

// Informações do banco de dados (do .env)
$dbHost = $_ENV['DB_HOST'] ?? 'localhost';
$dbName = $_ENV['DB_DATABASE'] ?? 'discursivamente';
$dbUser = $_ENV['DB_USERNAME'] ?? 'root';
$dbPass = $_ENV['DB_PASSWORD'] ?? '';

// Diretório para armazenar backups
$backupDir = $projectRoot . '/backups';

// Criar diretório de backup se não existir
if (!file_exists($backupDir)) {
    mkdir($backupDir, 0755, true);
}

// Data e hora para o nome do arquivo
$date = date('Y-m-d_H-i-s');

// ====================================
// Funções de Backup
// ====================================

/**
 * Realiza backup do banco de dados
 */
function backupDatabase($host, $dbname, $user, $pass, $outputFile) {
    // Verifica se o mysqldump está disponível
    exec('which mysqldump', $output, $returnVar);
    $mysqldumpAvailable = $returnVar === 0;

    if ($mysqldumpAvailable) {
        // Usa mysqldump se disponível (mais rápido e melhor)
        $command = sprintf(
            'mysqldump -h %s -u %s %s %s > %s',
            escapeshellarg($host),
            escapeshellarg($user),
            $pass ? '-p' . escapeshellarg($pass) : '',
            escapeshellarg($dbname),
            escapeshellarg($outputFile)
        );
        
        exec($command, $output, $returnVar);
        
        return $returnVar === 0;
    } else {
        // Fallback para PHP se mysqldump não estiver disponível
        try {
            $pdo = new PDO(
                "mysql:host=$host;dbname=$dbname;charset=utf8mb4",
                $user,
                $pass,
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false,
                ]
            );

            // Obter todas as tabelas
            $tables = [];
            $result = $pdo->query('SHOW TABLES');
            while ($row = $result->fetch(PDO::FETCH_NUM)) {
                $tables[] = $row[0];
            }
            
            $output = "-- Backup do banco de dados $dbname\n";
            $output .= "-- Gerado em " . date('Y-m-d H:i:s') . "\n\n";
            
            // Para cada tabela
            foreach ($tables as $table) {
                $output .= "-- Estrutura da tabela `$table`\n";
                
                // Pegar estrutura
                $stmt = $pdo->query("SHOW CREATE TABLE `$table`");
                $createTable = $stmt->fetch(PDO::FETCH_NUM)[1];
                $output .= $createTable . ";\n\n";
                
                // Pegar dados
                $output .= "-- Dados da tabela `$table`\n";
                $result = $pdo->query("SELECT * FROM `$table`");
                while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
                    $columns = implode("`, `", array_keys($row));
                    $values = [];
                    
                    foreach ($row as $value) {
                        if ($value === null) {
                            $values[] = 'NULL';
                        } else {
                            $values[] = $pdo->quote($value);
                        }
                    }
                    
                    $valuesStr = implode(", ", $values);
                    $output .= "INSERT INTO `$table` (`$columns`) VALUES ($valuesStr);\n";
                }
                
                $output .= "\n\n";
            }
            
            // Salvar no arquivo
            file_put_contents($outputFile, $output);
            return true;
        } catch (Exception $e) {
            echo "Erro ao criar backup: " . $e->getMessage() . "\n";
            return false;
        }
    }
}

/**
 * Realiza backup de arquivos importantes
 */
function backupFiles($projectRoot, $outputFile) {
    // Diretórios a serem incluídos no backup
    $diretoriosParaBackup = [
        '/public/uploads',
        '/src',
        '/public/assets',
        '/.env',
    ];

    // Arquivos/diretórios a serem excluídos
    $exclusoes = [
        '/vendor',
        '/node_modules',
        '/public/assets/build',
        '/tmp',
        '/logs',
        '/.git',
    ];

    // Verifica se o zip está disponível
    if (!class_exists('ZipArchive')) {
        echo "Erro: Extensão ZipArchive não está instalada\n";
        return false;
    }

    $zip = new ZipArchive();
    if ($zip->open($outputFile, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
        echo "Erro ao criar arquivo ZIP\n";
        return false;
    }

    // Função recursiva para adicionar arquivos ao ZIP
    $addFilesToZip = function ($dir, $zipPath = '') use (&$addFilesToZip, $zip, $projectRoot, $exclusoes) {
        $files = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($dir),
            RecursiveIteratorIterator::LEAVES_ONLY
        );

        foreach ($files as $name => $file) {
            if (!$file->isDir()) {
                // Converte caminho absoluto para relativo ao projeto
                $filePath = $file->getRealPath();
                $relativePath = str_replace($projectRoot, '', $filePath);

                // Verifica exclusões
                $excludeFile = false;
                foreach ($exclusoes as $exclusao) {
                    if (strpos($relativePath, $exclusao) === 0) {
                        $excludeFile = true;
                        break;
                    }
                }

                if (!$excludeFile) {
                    $relativePath = ltrim($relativePath, '/\\');
                    $zipFilePath = $zipPath ? $zipPath . '/' . $relativePath : $relativePath;
                    $zip->addFile($filePath, $zipFilePath);
                }
            }
        }
    };

    // Adiciona os diretórios especificados
    foreach ($diretoriosParaBackup as $dir) {
        $fullPath = $projectRoot . $dir;
        if (file_exists($fullPath)) {
            if (is_dir($fullPath)) {
                $addFilesToZip($fullPath);
            } else {
                // Se for um arquivo, adiciona diretamente
                $relativePath = ltrim(str_replace($projectRoot, '', $fullPath), '/\\');
                $zip->addFile($fullPath, $relativePath);
            }
        }
    }

    $zip->close();
    return true;
}

/**
 * Restaura banco de dados a partir de um backup
 */
function restoreDatabase($host, $dbname, $user, $pass, $backupFile) {
    // Verifica se o arquivo existe
    if (!file_exists($backupFile)) {
        echo "Erro: Arquivo de backup não encontrado: $backupFile\n";
        return false;
    }

    // Verifica se mysql está disponível
    exec('which mysql', $output, $returnVar);
    $mysqlAvailable = $returnVar === 0;

    if ($mysqlAvailable) {
        // Usa mysql cliente se disponível
        $command = sprintf(
            'mysql -h %s -u %s %s %s < %s',
            escapeshellarg($host),
            escapeshellarg($user),
            $pass ? '-p' . escapeshellarg($pass) : '',
            escapeshellarg($dbname),
            escapeshellarg($backupFile)
        );
        
        exec($command, $output, $returnVar);
        
        return $returnVar === 0;
    } else {
        echo "Erro: Cliente mysql não está disponível. Restauração manual necessária.\n";
        return false;
    }
}

// ====================================
// Executa o backup
// ====================================

// Verifica argumentos da linha de comando
$action = $argv[1] ?? 'backup';
$backupFile = $argv[2] ?? null;

switch ($action) {
    case 'backup':
        // Cria nome de arquivo baseado na data
        $dbBackupFile = $backupDir . "/db_backup_$date.sql";
        $filesBackupFile = $backupDir . "/files_backup_$date.zip";
        
        // Executa backup do banco de dados
        echo "Iniciando backup do banco de dados...\n";
        if (backupDatabase($dbHost, $dbName, $dbUser, $dbPass, $dbBackupFile)) {
            echo "✅ Backup do banco de dados criado em: $dbBackupFile\n";
        } else {
            echo "❌ Falha ao criar backup do banco de dados\n";
        }
        
        // Executa backup de arquivos
        echo "Iniciando backup de arquivos...\n";
        if (backupFiles($projectRoot, $filesBackupFile)) {
            echo "✅ Backup de arquivos criado em: $filesBackupFile\n";
        } else {
            echo "❌ Falha ao criar backup de arquivos\n";
        }
        
        // Limpa backups antigos (mantém últimos 5)
        echo "Limpando backups antigos...\n";
        $dbBackups = glob($backupDir . '/db_backup_*.sql');
        $fileBackups = glob($backupDir . '/files_backup_*.zip');
        
        usort($dbBackups, function($a, $b) { return filemtime($b) - filemtime($a); });
        usort($fileBackups, function($a, $b) { return filemtime($b) - filemtime($a); });
        
        // Remove backups antigos, mantendo os 5 mais recentes
        for ($i = 5; $i < count($dbBackups); $i++) {
            unlink($dbBackups[$i]);
        }
        
        for ($i = 5; $i < count($fileBackups); $i++) {
            unlink($fileBackups[$i]);
        }
        
        echo "✅ Limpeza de backups antigos concluída\n";
        echo "\n✅ Processo de backup concluído com sucesso!\n";
        
        break;
        
    case 'restore':
        if (!$backupFile) {
            // Lista os backups disponíveis
            echo "Backups disponíveis:\n";
            $dbBackups = glob($backupDir . '/db_backup_*.sql');
            usort($dbBackups, function($a, $b) { return filemtime($b) - filemtime($a); });
            
            foreach ($dbBackups as $index => $file) {
                $timestamp = date('Y-m-d H:i:s', filemtime($file));
                echo "[$index] " . basename($file) . " ($timestamp)\n";
            }
            
            echo "\nEscolha o número do backup para restaurar: ";
            $handle = fopen("php://stdin", "r");
            $line = fgets($handle);
            $selected = (int)trim($line);
            fclose($handle);
            
            if (isset($dbBackups[$selected])) {
                $backupFile = $dbBackups[$selected];
            } else {
                echo "❌ Seleção inválida\n";
                exit(1);
            }
        }
        
        // Confirma a restauração
        echo "⚠️ ATENÇÃO! Isso substituirá todos os dados atuais do banco de dados.\n";
        echo "Deseja continuar? (s/n): ";
        $handle = fopen("php://stdin", "r");
        $confirmation = strtolower(trim(fgets($handle)));
        fclose($handle);
        
        if ($confirmation === 's' || $confirmation === 'sim') {
            echo "Iniciando restauração do banco de dados...\n";
            if (restoreDatabase($dbHost, $dbName, $dbUser, $dbPass, $backupFile)) {
                echo "✅ Restauração concluída com sucesso!\n";
            } else {
                echo "❌ Falha na restauração do banco de dados\n";
            }
        } else {
            echo "Operação cancelada pelo usuário\n";
        }
        
        break;
        
    default:
        echo "Uso: php backup.php [backup|restore] [caminho_do_arquivo_para_restauração]\n";
        break;
}

echo "\nScript finalizado.\n";
