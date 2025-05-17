<?php
/**
 * Script de Otimização de Banco de Dados para o Discursivamente 2.1
 * 
 * Este script:
 * - Analisa e otimiza tabelas do banco de dados
 * - Identifica consultas lentas
 * - Sugere índices para melhorar performance
 * - Verifica a integridade dos dados
 */

// Definir a raiz do projeto
$projectRoot = realpath(__DIR__ . '/..');

// Carregar configurações do ambiente
require_once $projectRoot . '/vendor/autoload.php';
$dotenv = Dotenv\Dotenv::createImmutable($projectRoot);
$dotenv->load();

// Informações do banco de dados
$dbHost = $_ENV['DB_HOST'] ?? 'localhost';
$dbName = $_ENV['DB_DATABASE'] ?? 'discursivamente_db';
$dbUser = $_ENV['DB_USERNAME'] ?? 'root';
$dbPass = $_ENV['DB_PASSWORD'] ?? '';

// Cores para saída de console
define('COLOR_RED', "\033[31m");
define('COLOR_GREEN', "\033[32m");
define('COLOR_YELLOW', "\033[33m");
define('COLOR_BLUE', "\033[34m");
define('COLOR_RESET', "\033[0m");

// Conexão com o banco de dados
try {
    $pdo = new PDO(
        "mysql:host=$dbHost;dbname=$dbName;charset=utf8mb4",
        $dbUser,
        $dbPass,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ]
    );
    
    echo COLOR_GREEN . "✅ Conexão com o banco de dados estabelecida com sucesso.\n" . COLOR_RESET;
} catch (PDOException $e) {
    echo COLOR_RED . "❌ Erro ao conectar ao banco de dados: " . $e->getMessage() . "\n" . COLOR_RESET;
    exit(1);
}

// ====================================
// Funções de Otimização do Banco
// ====================================

/**
 * Lista todas as tabelas do banco de dados
 */
function listTables($pdo) {
    try {
        $stmt = $pdo->query("SHOW TABLES");
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    } catch (PDOException $e) {
        echo COLOR_RED . "❌ Erro ao listar tabelas: " . $e->getMessage() . "\n" . COLOR_RESET;
        return [];
    }
}

/**
 * Analisa uma tabela
 */
function analyzeTable($pdo, $table, $dbName) {
    try {
        echo COLOR_BLUE . "Analisando tabela $table...\n" . COLOR_RESET;
        
        // Verifica a estrutura da tabela
        $stmt = $pdo->query("DESCRIBE `$table`");
        $columns = $stmt->fetchAll();
        
        echo "  Estrutura da tabela:\n";
        foreach ($columns as $column) {
            echo "  - {$column['Field']} ({$column['Type']})";
            if ($column['Key'] === 'PRI') {
                echo " [PRIMARY KEY]";
            } elseif ($column['Key'] === 'MUL') {
                echo " [INDEX]";
            }
            echo "\n";
        }
        
        // Verifica índices existentes
        $stmt = $pdo->query("SHOW INDEX FROM `$table`");
        $indices = $stmt->fetchAll();
        $indexNames = [];
        
        echo "  Índices existentes:\n";
        if (count($indices) > 0) {
            foreach ($indices as $index) {
                $name = $index['Key_name'];
                if (!in_array($name, $indexNames)) {
                    $indexNames[] = $name;
                    $type = ($name === 'PRIMARY') ? 'Primary Key' : (($index['Non_unique'] == 0) ? 'Unique Index' : 'Index');
                    echo "  - $name ($type)\n";
                }
            }
        } else {
            echo "  - Nenhum índice encontrado além da chave primária\n";
        }
        
        // Verifica o número de registros
        $stmt = $pdo->query("SELECT COUNT(*) as total FROM `$table`");
        $count = $stmt->fetch()['total'];
        echo "  Registros: " . number_format($count) . "\n";
        
        // Verifica o tamanho da tabela
        $stmt = $pdo->query("
            SELECT 
                data_length, 
                index_length,
                data_free
            FROM 
                information_schema.TABLES
            WHERE 
                table_schema = '$dbName'
                AND table_name = '$table'
        ");
        
        $size = $stmt->fetch();
        
        if ($size) {
            $dataSize = round($size['data_length'] / 1024 / 1024, 2);
            $indexSize = round($size['index_length'] / 1024 / 1024, 2);
            $freeSpace = round($size['data_free'] / 1024 / 1024, 2);
            $totalSize = $dataSize + $indexSize;
            
            echo "  Tamanho: $totalSize MB (Dados: $dataSize MB, Índices: $indexSize MB)\n";
            
            // Verifica se há espaço fragmentado para recomendar otimização
            if ($freeSpace > 1) { // mais de 1 MB de espaço fragmentado
                echo COLOR_YELLOW . "  ⚠️ Há $freeSpace MB de espaço fragmentado. Recomenda-se otimizar esta tabela.\n" . COLOR_RESET;
                return true;
            }
        }
        
        return false;
    } catch (PDOException $e) {
        echo COLOR_RED . "❌ Erro ao analisar tabela $table: " . $e->getMessage() . "\n" . COLOR_RESET;
        return false;
    }
}

/**
 * Otimiza uma tabela
 */
function optimizeTable($pdo, $table) {
    try {
        echo COLOR_BLUE . "Otimizando tabela $table...\n" . COLOR_RESET;
        
        // Primeiro executa "ANALYZE TABLE" para atualizar estatísticas
        $pdo->exec("ANALYZE TABLE `$table`");
        
        // Em seguida executa "OPTIMIZE TABLE"
        $startTime = microtime(true);
        $pdo->exec("OPTIMIZE TABLE `$table`");
        $endTime = microtime(true);
        $duration = round($endTime - $startTime, 2);
        
        echo COLOR_GREEN . "✅ Tabela $table otimizada com sucesso em $duration segundos.\n" . COLOR_RESET;
        return true;
    } catch (PDOException $e) {
        echo COLOR_RED . "❌ Erro ao otimizar tabela $table: " . $e->getMessage() . "\n" . COLOR_RESET;
        return false;
    }
}

/**
 * Verifica se existem índices recomendados
 */
function suggestIndices($pdo, $table) {
    try {
        // Verifica colunas que provavelmente precisam de índices
        $commonIndexColumns = [
            'user_id', 'email', 'username', 'slug', 'status', 'created_at', 'updated_at',
            'category_id', 'type', 'course_id', 'classroom_id', 'material_id', 'assignment_id'
        ];
        
        // Obtém todas as colunas da tabela
        $stmt = $pdo->query("DESCRIBE `$table`");
        $columns = $stmt->fetchAll(PDO::FETCH_COLUMN, 0);
        
        // Obtém índices existentes
        $stmt = $pdo->query("SHOW INDEX FROM `$table`");
        $indices = $stmt->fetchAll();
        $indexedColumns = [];
        
        foreach ($indices as $index) {
            $indexedColumns[] = $index['Column_name'];
        }
        
        $suggestedIndices = [];
        
        // Verifica colunas comuns que devem ter índices
        foreach ($commonIndexColumns as $commonColumn) {
            if (in_array($commonColumn, $columns) && !in_array($commonColumn, $indexedColumns)) {
                $suggestedIndices[] = $commonColumn;
            }
        }
        
        // Verifica colunas que terminam com _id (possíveis chaves estrangeiras)
        foreach ($columns as $column) {
            if (substr($column, -3) === '_id' && !in_array($column, $indexedColumns) && !in_array($column, $suggestedIndices)) {
                $suggestedIndices[] = $column;
            }
        }
        
        if (!empty($suggestedIndices)) {
            echo COLOR_YELLOW . "  ⚠️ Colunas recomendadas para adicionar índices na tabela $table:\n" . COLOR_RESET;
            foreach ($suggestedIndices as $column) {
                echo "  - $column\n";
                echo "    CREATE INDEX idx_{$table}_{$column} ON `$table` (`$column`);\n";
            }
            return $suggestedIndices;
        }
        
        return [];
    } catch (PDOException $e) {
        echo COLOR_RED . "❌ Erro ao analisar índices para tabela $table: " . $e->getMessage() . "\n" . COLOR_RESET;
        return [];
    }
}

/**
 * Verifica se há joins sem índices adequados
 */
function checkForUnindexedJoins($pdo, $dbName) {
    echo COLOR_BLUE . "\nVerificando joins sem índices adequados...\n" . COLOR_RESET;
    
    // Busca por triggers e procedures para potencialmente encontrar queries
    $stmt = $pdo->query("
        SELECT routine_name, routine_type, routine_definition
        FROM information_schema.routines
        WHERE routine_schema = '$dbName'
    ");
    
    $routines = $stmt->fetchAll();
    $potentialJoins = [];
    
    // Analisa procedures e triggers para encontrar JOINs
    foreach ($routines as $routine) {
        if (preg_match_all('/JOIN\s+`?(\w+)`?\s+(?:AS\s+`?(\w+)`?)?\s+ON\s+`?(\w+)`?\.`?(\w+)`?\s*=\s*`?(\w+)`?\.`?(\w+)`?/i', 
                          $routine['routine_definition'], $matches, PREG_SET_ORDER)) {
            foreach ($matches as $match) {
                $table1 = $match[1];
                $table1Alias = isset($match[2]) && !empty($match[2]) ? $match[2] : $match[1];
                $table1Col = $match[4];
                $table2 = isset($match[5]) ? $match[5] : '';
                $table2Col = isset($match[6]) ? $match[6] : '';
                
                $potentialJoins[] = [
                    'table1' => $table1,
                    'column1' => $table1Col,
                    'table2' => $table2,
                    'column2' => $table2Col
                ];
            }
        }
    }
    
    if (empty($potentialJoins)) {
        echo "  Nenhum join encontrado para análise.\n";
        return;
    }
    
    echo "  Joins encontrados que podem precisar de índices:\n";
    
    foreach ($potentialJoins as $join) {
        // Verifica índice na primeira tabela
        $stmt = $pdo->query("SHOW INDEX FROM `{$join['table1']}` WHERE Column_name = '{$join['column1']}'");
        $hasIndex1 = $stmt->rowCount() > 0;
        
        // Verifica índice na segunda tabela
        $stmt = $pdo->query("SHOW INDEX FROM `{$join['table2']}` WHERE Column_name = '{$join['column2']}'");
        $hasIndex2 = $stmt->rowCount() > 0;
        
        if (!$hasIndex1 || !$hasIndex2) {
            echo "  - Join entre {$join['table1']}.{$join['column1']} e {$join['table2']}.{$join['column2']}\n";
            
            if (!$hasIndex1) {
                echo COLOR_YELLOW . "    ⚠️ Coluna {$join['column1']} na tabela {$join['table1']} não possui índice\n" . COLOR_RESET;
                echo "      CREATE INDEX idx_{$join['table1']}_{$join['column1']} ON `{$join['table1']}` (`{$join['column1']}`);\n";
            }
            
            if (!$hasIndex2) {
                echo COLOR_YELLOW . "    ⚠️ Coluna {$join['column2']} na tabela {$join['table2']} não possui índice\n" . COLOR_RESET;
                echo "      CREATE INDEX idx_{$join['table2']}_{$join['column2']} ON `{$join['table2']}` (`{$join['column2']}`);\n";
            }
        }
    }
}

/**
 * Verifica a consistência dos dados
 */
function checkDataConsistency($pdo, $dbName) {
    echo COLOR_BLUE . "\nVerificando consistência dos dados...\n" . COLOR_RESET;
    
    // Busca relacionamentos por convenção de nome (_id)
    $tables = listTables($pdo);
    $relationships = [];
    
    foreach ($tables as $table) {
        $stmt = $pdo->query("DESCRIBE `$table`");
        $columns = $stmt->fetchAll();
        
        foreach ($columns as $column) {
            // Procura colunas que podem ser chaves estrangeiras
            if (substr($column['Field'], -3) === '_id') {
                $targetTable = substr($column['Field'], 0, -3) . 's'; // Convenção: user_id referencia tabela users
                
                if (in_array($targetTable, $tables)) {
                    $relationships[] = [
                        'source_table' => $table,
                        'source_column' => $column['Field'],
                        'target_table' => $targetTable,
                        'target_column' => 'id'
                    ];
                }
            }
        }
    }
    
    echo "  Verificando " . count($relationships) . " potenciais relacionamentos...\n";
    
    $inconsistencies = [];
    
    // Verifica cada relacionamento
    foreach ($relationships as $relation) {
        $sourceTable = $relation['source_table'];
        $sourceColumn = $relation['source_column'];
        $targetTable = $relation['target_table'];
        $targetColumn = $relation['target_column'];
        
        // Busca valores na tabela de origem que não existem na tabela destino
        $query = "
            SELECT s.`$sourceColumn`, COUNT(*) as count
            FROM `$sourceTable` s
            LEFT JOIN `$targetTable` t ON s.`$sourceColumn` = t.`$targetColumn`
            WHERE s.`$sourceColumn` IS NOT NULL
            AND t.`$targetColumn` IS NULL
            GROUP BY s.`$sourceColumn`
        ";
        
        try {
            $stmt = $pdo->query($query);
            $orphans = $stmt->fetchAll();
            
            if (count($orphans) > 0) {
                $totalOrphans = 0;
                foreach ($orphans as $orphan) {
                    $totalOrphans += $orphan['count'];
                }
                
                $inconsistencies[] = [
                    'relation' => "$sourceTable.$sourceColumn → $targetTable.$targetColumn",
                    'orphan_values' => $orphans,
                    'count' => $totalOrphans
                ];
                
                echo COLOR_YELLOW . "  ⚠️ Inconsistência encontrada: " . count($orphans) . 
                     " valores em $sourceTable.$sourceColumn sem correspondência em $targetTable ($totalOrphans registros afetados)\n" . COLOR_RESET;
            }
        } catch (PDOException $e) {
            echo COLOR_RED . "  ❌ Erro ao verificar relação $sourceTable.$sourceColumn → $targetTable.$targetColumn: " . 
                 $e->getMessage() . "\n" . COLOR_RESET;
        }
    }
    
    if (count($inconsistencies) === 0) {
        echo COLOR_GREEN . "  ✅ Nenhuma inconsistência de dados encontrada!\n" . COLOR_RESET;
    } else {
        echo "\n  Total de " . count($inconsistencies) . " inconsistências encontradas.\n";
        echo "  Sugestão: Considere adicionar restrições de chave estrangeira às tabelas.\n";
    }
    
    return $inconsistencies;
}

/**
 * Aplica índices sugeridos se o usuário confirmar
 */
function applyIndices($pdo, $suggested, $table) {
    if (empty($suggested)) {
        return;
    }
    
    echo COLOR_BLUE . "\nDeseja adicionar os índices sugeridos para a tabela $table? (s/n): " . COLOR_RESET;
    $handle = fopen("php://stdin", "r");
    $line = strtolower(trim(fgets($handle)));
    
    if ($line === 's' || $line === 'sim') {
        foreach ($suggested as $column) {
            try {
                echo "Adicionando índice em $table.$column... ";
                $pdo->exec("CREATE INDEX idx_{$table}_{$column} ON `$table` (`$column`)");
                echo COLOR_GREEN . "✅ Concluído!\n" . COLOR_RESET;
            } catch (PDOException $e) {
                echo COLOR_RED . "❌ Erro: " . $e->getMessage() . "\n" . COLOR_RESET;
            }
        }
    }
}

// ====================================
// Execução da otimização
// ====================================

echo COLOR_BLUE . "===== OTIMIZAÇÃO DO BANCO DE DADOS =====\n" . COLOR_RESET;
echo "Data: " . date('Y-m-d H:i:s') . "\n";
echo "Banco de dados: $dbName\n\n";

// Listar tabelas
$tables = listTables($pdo);
echo "Foram encontradas " . count($tables) . " tabelas no banco de dados.\n\n";

// Processar cada tabela
$tablesToOptimize = [];
$allSuggestedIndices = [];

foreach ($tables as $table) {
    echo COLOR_BLUE . "===== TABELA: $table =====\n" . COLOR_RESET;
    
    // Analisa a tabela
    $needsOptimizing = analyzeTable($pdo, $table, $dbName);
    
    if ($needsOptimizing) {
        $tablesToOptimize[] = $table;
    }
    
    // Sugere índices
    $suggestedIndices = suggestIndices($pdo, $table);
    if (!empty($suggestedIndices)) {
        $allSuggestedIndices[$table] = $suggestedIndices;
    }
    
    echo "\n";
}

// Verifica joins sem índices
checkForUnindexedJoins($pdo, $dbName);

// Verifica consistência dos dados
$inconsistencies = checkDataConsistency($pdo, $dbName);

// Otimiza tabelas se necessário
if (!empty($tablesToOptimize)) {
    echo COLOR_BLUE . "\n===== OTIMIZAÇÃO DE TABELAS =====\n" . COLOR_RESET;
    echo "As seguintes tabelas precisam de otimização:\n";
    foreach ($tablesToOptimize as $table) {
        echo "- $table\n";
    }
    
    echo COLOR_YELLOW . "\nDeseja otimizar estas " . count($tablesToOptimize) . " tabelas agora? (s/n): " . COLOR_RESET;
    $handle = fopen("php://stdin", "r");
    $line = strtolower(trim(fgets($handle)));
    
    if ($line === 's' || $line === 'sim') {
        foreach ($tablesToOptimize as $table) {
            optimizeTable($pdo, $table);
        }
        echo COLOR_GREEN . "✅ Otimização de tabelas concluída!\n" . COLOR_RESET;
    } else {
        echo "Otimização cancelada pelo usuário.\n";
    }
}

// Aplica índices sugeridos
if (!empty($allSuggestedIndices)) {
    echo COLOR_BLUE . "\n===== ÍNDICES SUGERIDOS =====\n" . COLOR_RESET;
    echo "Foram encontradas oportunidades para adicionar índices em " . count($allSuggestedIndices) . " tabelas.\n";
    
    foreach ($allSuggestedIndices as $table => $indices) {
        applyIndices($pdo, $indices, $table);
    }
}

// Resumo final
echo COLOR_BLUE . "\n===== RESUMO DA OTIMIZAÇÃO =====\n" . COLOR_RESET;
echo "- Total de tabelas analisadas: " . count($tables) . "\n";
echo "- Tabelas que precisam otimização: " . count($tablesToOptimize) . "\n";
echo "- Tabelas com sugestões de índices: " . count($allSuggestedIndices) . "\n";
echo "- Inconsistências de dados encontradas: " . count($inconsistencies) . "\n";

echo COLOR_GREEN . "\n✅ Análise e otimização do banco de dados concluída!\n" . COLOR_RESET;

// Dicas finais
echo COLOR_BLUE . "\n===== DICAS ADICIONAIS =====\n" . COLOR_RESET;
echo "1. Considere aumentar o buffer do InnoDB para melhor desempenho\n";
echo "2. Implemente consultas com paginação para tabelas grandes\n";
echo "3. Utilize cache de consultas frequentes (Redis/Memcached)\n";
echo "4. Monitore consultas lentas ativando o slow query log\n";
echo "5. Considere a partição de tabelas muito grandes\n";
echo "6. Use prepared statements para todas as consultas dinâmicas\n";
