<?php
// Visualizador de logs para o sistema Discursivamente2.1
// Este arquivo permite visualizar os logs de upload e erros do sistema

require_once __DIR__ . '/../vendor/autoload.php';

use Config\Logger;

// Inicializar o logger
Logger::init();

// Validação básica de segurança
function isAdmin() {
    // Aqui você pode adicionar sua lógica de verificação de admin
    // Por enquanto, permitimos acesso local apenas
    $localIps = ['127.0.0.1', '::1'];
    return in_array($_SERVER['REMOTE_ADDR'], $localIps);
}

// Se não for admin, negar acesso
if (!isAdmin()) {
    http_response_code(403);
    die('Acesso negado. Esta ferramenta só pode ser acessada por administradores.');
}

// Parâmetros
$logType = $_GET['log'] ?? 'uploads';
$lines = (int)($_GET['lines'] ?? 100);
$lines = min(max($lines, 10), 1000); // Limitar entre 10 e 1000 linhas

// Mapeamento de tipos de log para arquivos
$logFiles = [
    'uploads' => Logger::LOG_FILE_UPLOAD,
    'errors' => Logger::LOG_FILE_ERROR,
    'debug' => Logger::LOG_FILE_DEBUG
];

$logFile = $logFiles[$logType] ?? Logger::LOG_FILE_UPLOAD;
$logPath = Logger::LOG_DIR . '/' . $logFile;

// Verificar se o arquivo existe
$fileExists = file_exists($logPath);
$fileSize = $fileExists ? filesize($logPath) : 0;
$fileMTime = $fileExists ? date('d/m/Y H:i:s', filemtime($logPath)) : 'N/A';

// Função para ler as últimas N linhas de um arquivo
function tailFile($file, $lines) {
    if (!file_exists($file)) {
        return [];
    }
    
    $fileData = file($file);
    $lineCount = count($fileData);
    $start = max(0, $lineCount - $lines);
    
    return array_slice($fileData, $start);
}

// Ler o log
$logData = $fileExists ? tailFile($logPath, $lines) : [];

// Função para formatar JSON para exibição
function formatJsonLine($line) {
    $line = trim($line);
    if (empty($line)) return '';
    
    try {
        $data = json_decode($line, true);
        if (json_last_error() === JSON_ERROR_NONE) {
            // Formatar timestamp
            if (isset($data['timestamp'])) {
                $data['timestamp'] = '<span style="color:#2980b9">' . $data['timestamp'] . '</span>';
            }
            
            // Formatar nível
            if (isset($data['level'])) {
                $color = '#2c3e50';
                if ($data['level'] === 'ERROR') $color = '#e74c3c';
                if ($data['level'] === 'WARNING') $color = '#e67e22';
                if ($data['level'] === 'INFO') $color = '#27ae60';
                if ($data['level'] === 'DEBUG') $color = '#3498db';
                
                $data['level'] = '<span style="color:' . $color . ';font-weight:bold">' . $data['level'] . '</span>';
            }
            
            // Formatar mensagem
            if (isset($data['message'])) {
                $data['message'] = '<span style="color:#34495e;font-weight:bold">' . htmlspecialchars($data['message']) . '</span>';
            }
            
            // Formatar dados
            if (isset($data['data']) && is_array($data['data'])) {
                foreach ($data['data'] as $key => $value) {
                    if (is_string($value)) {
                        $data['data'][$key] = htmlspecialchars($value);
                    } elseif (is_array($value)) {
                        $data['data'][$key] = '<pre>' . htmlspecialchars(json_encode($value, JSON_PRETTY_PRINT)) . '</pre>';
                    }
                }
            }
            
            return json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        }
    } catch (\Exception $e) {
        // Se ocorrer um erro ao decodificar, retorne a linha original
    }
    
    return htmlspecialchars($line);
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Visualizador de Logs - Discursivamente</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f9f9f9;
        }
        h1, h2, h3 {
            color: #2c3e50;
        }
        .card {
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            padding: 20px;
            margin-bottom: 20px;
        }
        .log-entry {
            background-color: #f8f9fa;
            border-radius: 4px;
            padding: 10px;
            margin-bottom: 10px;
            overflow-x: auto;
            font-family: 'Consolas', 'Monaco', monospace;
            font-size: 14px;
            white-space: pre-wrap;
            border-left: 4px solid #3498db;
        }
        .log-entry.error {
            border-left-color: #e74c3c;
        }
        .log-entry.warning {
            border-left-color: #e67e22;
        }
        .log-entry.info {
            border-left-color: #27ae60;
        }
        .log-entry.debug {
            border-left-color: #3498db;
        }
        .controls {
            margin-bottom: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
        }
        .btn {
            background-color: #3498db;
            color: white;
            padding: 8px 16px;
            border-radius: 4px;
            text-decoration: none;
            margin-right: 10px;
            display: inline-block;
            border: none;
            cursor: pointer;
        }
        .btn:hover {
            background-color: #2980b9;
        }
        .btn.active {
            background-color: #2c3e50;
        }
        .btn-group {
            display: flex;
            margin-bottom: 10px;
        }
        select, input {
            padding: 8px;
            border-radius: 4px;
            border: 1px solid #ddd;
        }
        .file-info {
            font-size: 14px;
            margin-bottom: 20px;
        }
        .file-info span {
            margin-right: 20px;
        }
        .bold {
            font-weight: bold;
        }
        .empty-log {
            text-align: center;
            padding: 40px 0;
            color: #7f8c8d;
        }
        .auto-refresh {
            display: flex;
            align-items: center;
            margin-left: 20px;
        }
    </style>
</head>
<body>
    <h1>Visualizador de Logs - Discursivamente</h1>
    <p>Esta ferramenta permite visualizar os logs do sistema para diagnóstico de problemas.</p>
    
    <div class="controls">
        <div>
            <div class="btn-group">
                <a href="?log=uploads" class="btn <?php echo $logType === 'uploads' ? 'active' : ''; ?>">Logs de Upload</a>
                <a href="?log=errors" class="btn <?php echo $logType === 'errors' ? 'active' : ''; ?>">Logs de Erros</a>
                <a href="?log=debug" class="btn <?php echo $logType === 'debug' ? 'active' : ''; ?>">Logs de Debug</a>
            </div>
            
            <div class="btn-group">
                <a href="upload_diagnostics.php" class="btn">Voltar para Diagnósticos</a>
                <a href="upload_test.php" class="btn">Teste de Upload</a>
            </div>
        </div>
        
        <div>
            <form method="get" action="">
                <input type="hidden" name="log" value="<?php echo htmlspecialchars($logType); ?>">
                <label for="lines">Número de linhas:</label>
                <input type="number" name="lines" id="lines" value="<?php echo $lines; ?>" min="10" max="1000" style="width: 80px;">
                <button type="submit" class="btn">Atualizar</button>
                
                <span class="auto-refresh">
                    <input type="checkbox" id="autoRefresh"> 
                    <label for="autoRefresh">Atualização automática (10s)</label>
                </span>
            </form>
        </div>
    </div>
    
    <div class="card">
        <h2>
            <?php 
            $logTitles = [
                'uploads' => 'Logs de Upload',
                'errors' => 'Logs de Erros',
                'debug' => 'Logs de Debug'
            ];
            echo $logTitles[$logType] ?? 'Logs';
            ?>
        </h2>
        
        <div class="file-info">
            <span><span class="bold">Arquivo:</span> <?php echo htmlspecialchars($logPath); ?></span>
            <span><span class="bold">Tamanho:</span> <?php echo $fileExists ? number_format($fileSize / 1024, 2) . ' KB' : 'N/A'; ?></span>
            <span><span class="bold">Última modificação:</span> <?php echo $fileMTime; ?></span>
        </div>
        
        <?php if (empty($logData)): ?>
            <div class="empty-log">
                <h3>Não há dados de log disponíveis</h3>
                <p>O arquivo de log está vazio ou não existe.</p>
            </div>
        <?php else: ?>
            <div id="logContainer">
                <?php foreach ($logData as $line): ?>
                    <?php
                    $class = 'log-entry';
                    if (stripos($line, '"level":"ERROR"') !== false || stripos($line, '"level":"ERRO"') !== false) {
                        $class .= ' error';
                    } elseif (stripos($line, '"level":"WARNING"') !== false || stripos($line, '"level":"AVISO"') !== false) {
                        $class .= ' warning';
                    } elseif (stripos($line, '"level":"INFO"') !== false) {
                        $class .= ' info';
                    } elseif (stripos($line, '"level":"DEBUG"') !== false) {
                        $class .= ' debug';
                    }
                    ?>
                    <div class="<?php echo $class; ?>"><?php echo formatJsonLine($line); ?></div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
    
    <script>
        // Auto-refresh
        const autoRefreshCheckbox = document.getElementById('autoRefresh');
        let refreshTimer;
        
        autoRefreshCheckbox.addEventListener('change', function() {
            if (this.checked) {
                refreshTimer = setInterval(() => {
                    location.reload();
                }, 10000); // 10 segundos
            } else {
                clearInterval(refreshTimer);
            }
        });
        
        // Colorir JSON
        document.addEventListener('DOMContentLoaded', function() {
            const logEntries = document.querySelectorAll('.log-entry');
            logEntries.forEach(entry => {
                // Já está formatado no servidor
            });
        });
    </script>
</body>
</html>
