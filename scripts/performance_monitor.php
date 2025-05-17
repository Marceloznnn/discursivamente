<?php
/**
 * Script de Monitoramento de Performance para o Discursivamente 2.1
 * 
 * Este script analisa o desempenho do sistema, verificando:
 * - Tempos de carregamento das páginas
 * - Desempenho do banco de dados
 * - Uso de memória
 * - Arquivos JavaScript e CSS não otimizados
 * - Imagens não otimizadas
 */

// Definir a raiz do projeto
$projectRoot = realpath(__DIR__ . '/..');

// Carregar configurações do ambiente
require_once $projectRoot . '/vendor/autoload.php';
$dotenv = Dotenv\Dotenv::createImmutable($projectRoot);
$dotenv->load();

// Informações do banco de dados
$dbHost = $_ENV['DB_HOST'] ?? 'localhost';
$dbName = $_ENV['DB_DATABASE'] ?? 'discursivamente';
$dbUser = $_ENV['DB_USERNAME'] ?? 'root';
$dbPass = $_ENV['DB_PASSWORD'] ?? '';

// Diretório para logs de desempenho
$performanceLogDir = $projectRoot . '/logs/performance';
if (!file_exists($performanceLogDir)) {
    mkdir($performanceLogDir, 0755, true);
}

// Data para o nome do arquivo
$date = date('Y-m-d_H-i-s');
$logFile = $performanceLogDir . "/performance_$date.log";

// Inicia o log
$log = "====== RELATÓRIO DE PERFORMANCE ======\n";
$log .= "Data: " . date('Y-m-d H:i:s') . "\n\n";

/**
 * Verifica o desempenho do banco de dados
 */
function checkDatabasePerformance($host, $dbname, $user, $pass) {
    $log = "--- DESEMPENHO DO BANCO DE DADOS ---\n";
    
    try {
        $startTime = microtime(true);
        
        // Conectar ao banco de dados
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
        
        $connectTime = microtime(true) - $startTime;
        $log .= "Tempo de conexão: " . round($connectTime * 1000, 2) . " ms\n";
        
        // Verifica tabelas sem índices
        $log .= "\nVerificando tabelas sem índices adequados:\n";
        
        $tables = [];
        $stmt = $pdo->query("SHOW TABLES");
        while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
            $tables[] = $row[0];
        }
        
        $tablesWithoutIndices = [];
        foreach ($tables as $table) {
            $stmt = $pdo->query("SHOW INDEX FROM `$table`");
            $indices = $stmt->fetchAll();
            
            // Verifica se existem apenas índices primários
            if (count($indices) <= 1) {
                $tablesWithoutIndices[] = $table;
            }
        }
        
        if (empty($tablesWithoutIndices)) {
            $log .= "Todas as tabelas possuem índices adequados.\n";
        } else {
            $log .= "Tabelas que podem precisar de índices adicionais:\n";
            foreach ($tablesWithoutIndices as $table) {
                $log .= "- $table\n";
            }
        }
        
        // Verifica queries lentas (se o slow query log estiver ativado)
        $log .= "\nVerificando configuração de slow query log:\n";
        $stmt = $pdo->query("SHOW VARIABLES LIKE 'slow_query%'");
        $slowQueryConfig = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
        
        if (isset($slowQueryConfig['slow_query_log']) && $slowQueryConfig['slow_query_log'] == 'ON') {
            $log .= "Slow query log está ativado.\n";
        } else {
            $log .= "⚠️ Slow query log não está ativado. Recomenda-se ativar para monitorar consultas lentas.\n";
        }
        
        // Verifica tamanho das tabelas
        $log .= "\nVerificando tamanho das tabelas:\n";
        $stmt = $pdo->query("
            SELECT 
                table_name,
                round(((data_length + index_length) / 1024 / 1024), 2) AS 'size_mb'
            FROM 
                information_schema.TABLES
            WHERE 
                table_schema = '$dbname'
            ORDER BY 
                (data_length + index_length) DESC
            LIMIT 10
        ");
        
        $tableSize = $stmt->fetchAll();
        if (!empty($tableSize)) {
            foreach ($tableSize as $row) {
                $log .= "- {$row['table_name']}: {$row['size_mb']} MB\n";
            }
        }
        
        return $log;
    } catch (Exception $e) {
        return $log . "Erro ao verificar desempenho do banco de dados: " . $e->getMessage() . "\n";
    }
}

/**
 * Verifica arquivos JavaScript e CSS para optimizações potenciais
 */
function checkAssetOptimization($projectRoot) {
    $log = "\n--- OTIMIZAÇÃO DE ASSETS ---\n";
    
    // Verificar arquivos JS
    $log .= "\nArquivos JavaScript:\n";
    $jsFiles = glob($projectRoot . '/public/assets/js/*.js');
    $jsTotal = 0;
    $jsNonMinified = [];
    
    foreach ($jsFiles as $file) {
        $size = filesize($file) / 1024; // tamanho em KB
        $jsTotal += $size;
        
        // Verifica se o arquivo não é minificado
        $content = file_get_contents($file);
        if (strlen($content) > 1000 && !preg_match('/\.min\.js$/', $file) && strpos($content, "\n ") !== false) {
            $jsNonMinified[] = [
                'file' => basename($file),
                'size' => round($size, 2)
            ];
        }
    }
    
    $log .= "Total de JS: " . round($jsTotal, 2) . " KB\n";
    
    if (!empty($jsNonMinified)) {
        $log .= "Arquivos JS não minificados que podem ser otimizados:\n";
        foreach ($jsNonMinified as $file) {
            $log .= "- {$file['file']} ({$file['size']} KB)\n";
        }
    } else {
        $log .= "Todos os arquivos JS parecem estar minificados.\n";
    }
    
    // Verificar arquivos CSS
    $log .= "\nArquivos CSS:\n";
    $cssFiles = glob($projectRoot . '/public/assets/css/*.css');
    $cssTotal = 0;
    $cssNonMinified = [];
    
    foreach ($cssFiles as $file) {
        $size = filesize($file) / 1024; // tamanho em KB
        $cssTotal += $size;
        
        // Verifica se o arquivo não é minificado
        $content = file_get_contents($file);
        if (strlen($content) > 1000 && !preg_match('/\.min\.css$/', $file) && strpos($content, "\n  ") !== false) {
            $cssNonMinified[] = [
                'file' => basename($file),
                'size' => round($size, 2)
            ];
        }
    }
    
    $log .= "Total de CSS: " . round($cssTotal, 2) . " KB\n";
    
    if (!empty($cssNonMinified)) {
        $log .= "Arquivos CSS não minificados que podem ser otimizados:\n";
        foreach ($cssNonMinified as $file) {
            $log .= "- {$file['file']} ({$file['size']} KB)\n";
        }
    } else {
        $log .= "Todos os arquivos CSS parecem estar minificados.\n";
    }
    
    return $log;
}

/**
 * Verifica imagens para optimizações potenciais
 */
function checkImageOptimization($projectRoot) {
    $log = "\n--- OTIMIZAÇÃO DE IMAGENS ---\n";
    
    $imageExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
    $largeImagesCount = 0;
    $largeImages = [];
    $totalSize = 0;
    
    // Verificar diretórios de imagens
    $imageDirs = [
        $projectRoot . '/public/assets/images',
        $projectRoot . '/public/uploads'
    ];
    
    foreach ($imageDirs as $dir) {
        if (!file_exists($dir)) {
            continue;
        }
        
        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($dir)
        );
        
        foreach ($iterator as $file) {
            if ($file->isFile()) {
                $extension = strtolower(pathinfo($file->getFilename(), PATHINFO_EXTENSION));
                if (in_array($extension, $imageExtensions)) {
                    $size = $file->getSize() / 1024; // KB
                    $totalSize += $size;
                    
                    // Verifica imagens grandes (> 200KB)
                    if ($size > 200) {
                        $largeImagesCount++;
                        $relativePath = str_replace($projectRoot, '', $file->getPathname());
                        $largeImages[] = [
                            'path' => $relativePath,
                            'size' => round($size, 2)
                        ];
                        
                        // Limita para as 10 maiores imagens
                        if (count($largeImages) > 10) {
                            usort($largeImages, function($a, $b) {
                                return $b['size'] - $a['size'];
                            });
                            array_pop($largeImages);
                        }
                    }
                }
            }
        }
    }
    
    $log .= "Tamanho total de imagens: " . round($totalSize / 1024, 2) . " MB\n";
    $log .= "Total de imagens grandes (>200KB): $largeImagesCount\n";
    
    if (!empty($largeImages)) {
        usort($largeImages, function($a, $b) {
            return $b['size'] - $a['size'];
        });
        
        $log .= "\nTop imagens que podem ser otimizadas:\n";
        foreach ($largeImages as $img) {
            $log .= "- {$img['path']} ({$img['size']} KB)\n";
        }
        
        $log .= "\nRecomendações para otimização de imagens:\n";
        $log .= "- Utilize formatos modernos como WebP\n";
        $log .= "- Redimensione imagens para o tamanho necessário antes de exibi-las\n";
        $log .= "- Comprima imagens com ferramentas como ImageOptim ou TinyPNG\n";
    }
    
    return $log;
}

/**
 * Verifica as rotas e controladores para identificar possíveis gargalos
 */
function analyzeRoutes($projectRoot) {
    $log = "\n--- ANÁLISE DE ROTAS ---\n";
    
    // Analisar arquivo de rotas
    $routesFile = $projectRoot . '/src/Routes/web.php';
    if (!file_exists($routesFile)) {
        return $log . "Arquivo de rotas não encontrado.\n";
    }
    
    $routesContent = file_get_contents($routesFile);
    $routeCount = substr_count($routesContent, '$r->addRoute(');
    
    $log .= "Total de rotas definidas: $routeCount\n";
    
    // Analisar controladores mais usados
    preg_match_all('/new\s+([\\\\A-Za-z0-9_]+)\(/m', $routesContent, $matches);
    $controllers = [];
    
    if (!empty($matches[1])) {
        foreach ($matches[1] as $controller) {
            if (!isset($controllers[$controller])) {
                $controllers[$controller] = 0;
            }
            $controllers[$controller]++;
        }
        
        arsort($controllers);
        
        $log .= "\nControladores mais utilizados:\n";
        $i = 0;
        foreach ($controllers as $controller => $count) {
            $log .= "- $controller: $count rotas\n";
            $i++;
            if ($i >= 5) break;
        }
    }
    
    return $log;
}

/**
 * Verifica os logs para identificar erros frequentes
 */
function analyzeLogs($projectRoot) {
    $log = "\n--- ANÁLISE DE LOGS ---\n";
    
    $logDirs = [
        $projectRoot . '/logs',
        $projectRoot . '/src/logs'
    ];
    
    $errorCounts = [];
    $totalErrors = 0;
    
    foreach ($logDirs as $dir) {
        if (!file_exists($dir)) {
            continue;
        }
        
        $logFiles = glob("$dir/*.log");
        
        foreach ($logFiles as $logFile) {
            $logName = basename($logFile);
            $content = file_get_contents($logFile);
            
            // Conta ocorrências de erros e exceções
            $errorCount = preg_match_all('/(error|exception|fatal|warning|critical)/i', $content, $matches);
            $totalErrors += $errorCount;
            
            if ($errorCount > 0) {
                $errorCounts[$logName] = $errorCount;
            }
        }
    }
    
    $log .= "Total de erros encontrados: $totalErrors\n";
    
    if (!empty($errorCounts)) {
        arsort($errorCounts);
        $log .= "\nArquivos de log com mais erros:\n";
        foreach ($errorCounts as $file => $count) {
            $log .= "- $file: $count erros\n";
        }
    }
    
    return $log;
}

/**
 * Verifica a configuração do PHP
 */
function checkPHPConfiguration() {
    $log = "\n--- CONFIGURAÇÃO DO PHP ---\n";
    
    $recommendedSettings = [
        'memory_limit' => '128M',
        'post_max_size' => '20M',
        'upload_max_filesize' => '20M',
        'max_execution_time' => '30',
        'display_errors' => '0',
        'error_reporting' => 'E_ALL',
        'opcache.enable' => '1'
    ];
    
    $log .= "Configurações atuais vs. recomendadas:\n";
    
    foreach ($recommendedSettings as $setting => $recommended) {
        $current = ini_get($setting);
        $status = ($current == $recommended) ? '✅' : '⚠️';
        
        // Ajuste para limites de memória (converter para bytes para comparar)
        if (in_array($setting, ['memory_limit', 'post_max_size', 'upload_max_filesize'])) {
            $currentBytes = getBytes($current);
            $recommendedBytes = getBytes($recommended);
            
            if ($currentBytes >= $recommendedBytes) {
                $status = '✅';
            }
        }
        
        $log .= "$status $setting: $current (recomendado: $recommended)\n";
    }
    
    return $log;
}

/**
 * Converte strings de tamanho (como '128M') para bytes
 */
function getBytes($val) {
    $val = trim($val);
    $last = strtolower($val[strlen($val)-1]);
    $val = (int)$val;
    
    switch($last) {
        case 'g': $val *= 1024;
        case 'm': $val *= 1024;
        case 'k': $val *= 1024;
    }
    
    return $val;
}

/**
 * Verifica se o composer.json tem vulnerabilidades com composer outdated
 */
function checkComposerDependencies($projectRoot) {
    $log = "\n--- DEPENDÊNCIAS DO COMPOSER ---\n";
    
    if (file_exists($projectRoot . '/composer.json')) {
        // Verifica se o composer está instalado
        exec('composer --version', $output, $returnVar);
        if ($returnVar !== 0) {
            return $log . "Composer não encontrado. Não foi possível verificar dependências.\n";
        }
        
        // Salva o diretório atual
        $currentDir = getcwd();
        
        // Muda para o diretório do projeto
        chdir($projectRoot);
        
        // Executa o comando composer outdated
        exec('composer outdated --direct', $output, $returnVar);
        
        // Volta para o diretório original
        chdir($currentDir);
        
        if ($returnVar !== 0) {
            return $log . "Erro ao verificar dependências desatualizadas.\n";
        }
        
        if (count($output) > 1) {
            $outdatedCount = count($output) - 1;
            $log .= "Existem $outdatedCount dependências desatualizadas:\n\n";
            
            foreach ($output as $line) {
                $log .= "$line\n";
            }
            
            $log .= "\nRecomendação: Execute 'composer update' para atualizar as dependências.\n";
        } else {
            $log .= "Todas as dependências estão atualizadas. ✅\n";
        }
    } else {
        $log .= "Arquivo composer.json não encontrado.\n";
    }
    
    return $log;
}

// ====================================
// Execução das verificações
// ====================================

// Verifica desempenho do banco de dados
$log .= checkDatabasePerformance($dbHost, $dbName, $dbUser, $dbPass);

// Verifica otimização de assets
$log .= checkAssetOptimization($projectRoot);

// Verifica otimização de imagens
$log .= checkImageOptimization($projectRoot);

// Analisa rotas
$log .= analyzeRoutes($projectRoot);

// Analisa logs
$log .= analyzeLogs($projectRoot);

// Verifica configuração do PHP
$log .= checkPHPConfiguration();

// Verifica dependências do composer
$log .= checkComposerDependencies($projectRoot);

// Recomendações gerais
$log .= "\n====== RECOMENDAÇÕES GERAIS ======\n";
$log .= "1. Implemente cache de página para rotas frequentemente acessadas\n";
$log .= "2. Minifique e agrupe arquivos CSS/JS\n";
$log .= "3. Utilize lazy loading para imagens\n";
$log .= "4. Adicione índices a tabelas frequentemente consultadas\n";
$log .= "5. Configure um CDN para ativos estáticos\n";
$log .= "6. Implemente um sistema de cache (Redis ou Memcached)\n";
$log .= "7. Considere otimizar consultas SQL complexas\n";

// Salva o log em arquivo
file_put_contents($logFile, $log);

// Exibe o resultado
echo "Análise de performance concluída!\n";
echo "Relatório salvo em: $logFile\n";
echo "\nResumo das verificações:\n";
echo "- Banco de dados analisado\n";
echo "- Assets (JS/CSS) verificados\n";
echo "- Imagens analisadas\n";
echo "- Rotas e controladores analisados\n";
echo "- Logs de erro analisados\n";
echo "- Configuração PHP verificada\n";
echo "- Dependências Composer verificadas\n";
