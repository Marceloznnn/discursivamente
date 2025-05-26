<?php
// Script para testar os limites de upload no servidor
// Este arquivo deve ser acessado diretamente no navegador para ver os resultados

require_once __DIR__ . '/../vendor/autoload.php';

use Config\UploadConfig;
use Config\Logger;

// Inicializar o logger
Logger::init();

// Função para formatar bytes em unidades legíveis
function formatBytes($bytes, $precision = 2) {
    $units = ['B', 'KB', 'MB', 'GB', 'TB'];
    $bytes = max($bytes, 0);
    $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
    $pow = min($pow, count($units) - 1);
    $bytes /= (1 << (10 * $pow));
    return round($bytes, $precision) . ' ' . $units[$pow];
}

// Função para converter limites de string para bytes
function convertToBytes($value) {
    $value = trim($value);
    $last = strtolower($value[strlen($value) - 1]);
    $value = (int) $value;
    
    switch ($last) {
        case 'g':
            $value *= 1024;
        case 'm':
            $value *= 1024;
        case 'k':
            $value *= 1024;
    }
    
    return $value;
}

// Cabeçalho HTML
header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Teste de Upload de Arquivos | Discursivamente</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
        }
        h1, h2 {
            color: #2c3e50;
        }
        table {
            border-collapse: collapse;
            width: 100%;
            margin-bottom: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
        .success {
            color: #27ae60;
        }
        .warning {
            color: #e67e22;
        }
        .error {
            color: #e74c3c;
        }
        .card {
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 16px;
            margin-bottom: 20px;
        }
        pre {
            background-color: #f9f9f9;
            border: 1px solid #ddd;
            padding: 10px;
            overflow: auto;
        }
        .progress-container {
            background-color: #f1f1f1;
            border-radius: 5px;
            margin-bottom: 10px;
        }
        .progress-bar {
            height: 30px;
            border-radius: 5px;
            background-color: #4CAF50;
            width: 0%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <h1>Teste de Upload de Arquivos - Discursivamente</h1>
    <p>Esta página realiza um diagnóstico completo das configurações de upload nesta instalação.</p>
    
    <div class="card">
        <h2>Limites de Upload Configurados</h2>
        <table>
            <tr>
                <th>Configuração</th>
                <th>Valor</th>
                <th>Valor em Bytes</th>
                <th>Status</th>
            </tr>
            <tr>
                <td>upload_max_filesize (PHP)</td>
                <td><?php echo ini_get('upload_max_filesize'); ?></td>
                <td><?php echo formatBytes(convertToBytes(ini_get('upload_max_filesize'))); ?></td>
                <td>
                    <?php
                    $upload_max = convertToBytes(ini_get('upload_max_filesize'));
                    if ($upload_max >= 209715200) { // 200MB
                        echo '<span class="success">✓ OK</span>';
                    } else {
                        echo '<span class="error">✗ Abaixo do recomendado (200MB)</span>';
                    }
                    ?>
                </td>
            </tr>
            <tr>
                <td>post_max_size (PHP)</td>
                <td><?php echo ini_get('post_max_size'); ?></td>
                <td><?php echo formatBytes(convertToBytes(ini_get('post_max_size'))); ?></td>
                <td>
                    <?php
                    $post_max = convertToBytes(ini_get('post_max_size'));
                    if ($post_max >= 209715200) { // 200MB
                        echo '<span class="success">✓ OK</span>';
                    } else {
                        echo '<span class="error">✗ Abaixo do recomendado (200MB)</span>';
                    }
                    ?>
                </td>
            </tr>
            <tr>
                <td>memory_limit (PHP)</td>
                <td><?php echo ini_get('memory_limit'); ?></td>
                <td><?php echo formatBytes(convertToBytes(ini_get('memory_limit'))); ?></td>
                <td>
                    <?php
                    $memory_limit = convertToBytes(ini_get('memory_limit'));
                    if ($memory_limit >= 536870912 || $memory_limit < 0) { // 512MB ou -1 (ilimitado)
                        echo '<span class="success">✓ OK</span>';
                    } else {
                        echo '<span class="warning">⚠ Abaixo do recomendado (512MB)</span>';
                    }
                    ?>
                </td>
            </tr>
            <tr>
                <td>max_execution_time (PHP)</td>
                <td><?php echo ini_get('max_execution_time'); ?> segundos</td>
                <td>N/A</td>
                <td>
                    <?php
                    $max_execution_time = (int)ini_get('max_execution_time');
                    if ($max_execution_time >= 600 || $max_execution_time == 0) { // 600s ou 0 (ilimitado)
                        echo '<span class="success">✓ OK</span>';
                    } else {
                        echo '<span class="warning">⚠ Abaixo do recomendado (600s)</span>';
                    }
                    ?>
                </td>
            </tr>
            <tr>
                <td>max_input_time (PHP)</td>
                <td><?php echo ini_get('max_input_time'); ?> segundos</td>
                <td>N/A</td>
                <td>
                    <?php
                    $max_input_time = (int)ini_get('max_input_time');
                    if ($max_input_time >= 600 || $max_input_time == -1) { // 600s ou -1 (ilimitado)
                        echo '<span class="success">✓ OK</span>';
                    } else {
                        echo '<span class="warning">⚠ Abaixo do recomendado (600s)</span>';
                    }
                    ?>
                </td>
            </tr>
        </table>
    </div>
    
    <div class="card">
        <h2>Limites na Aplicação (Config\UploadConfig)</h2>
        <table>
            <tr>
                <th>Tipo de arquivo</th>
                <th>Limite configurado</th>
                <th>Status</th>
            </tr>
            <tr>
                <td>Imagens (MAX_IMAGE_SIZE)</td>
                <td><?php echo formatBytes(UploadConfig::MAX_IMAGE_SIZE); ?></td>
                <td>
                    <?php
                    if (UploadConfig::MAX_IMAGE_SIZE >= 50 * 1024 * 1024) { // 50MB
                        echo '<span class="success">✓ OK</span>';
                    } else {
                        echo '<span class="error">✗ Abaixo do recomendado (50MB)</span>';
                    }
                    ?>
                </td>
            </tr>
            <tr>
                <td>Vídeos (MAX_VIDEO_SIZE)</td>
                <td><?php echo formatBytes(UploadConfig::MAX_VIDEO_SIZE); ?></td>
                <td>
                    <?php
                    if (UploadConfig::MAX_VIDEO_SIZE >= 200 * 1024 * 1024) { // 200MB
                        echo '<span class="success">✓ OK</span>';
                    } else {
                        echo '<span class="error">✗ Abaixo do recomendado (200MB)</span>';
                    }
                    ?>
                </td>
            </tr>
            <tr>
                <td>Áudios (MAX_AUDIO_SIZE)</td>
                <td><?php echo formatBytes(UploadConfig::MAX_AUDIO_SIZE); ?></td>
                <td>
                    <?php
                    if (UploadConfig::MAX_AUDIO_SIZE >= 100 * 1024 * 1024) { // 100MB
                        echo '<span class="success">✓ OK</span>';
                    } else {
                        echo '<span class="warning">⚠ Abaixo do recomendado (100MB)</span>';
                    }
                    ?>
                </td>
            </tr>
        </table>
    </div>
    
    <div class="card">
        <h2>Ambiente de Execução</h2>
        <table>
            <tr>
                <th>Configuração</th>
                <th>Valor</th>
            </tr>
            <tr>
                <td>PHP SAPI</td>
                <td><?php echo php_sapi_name(); ?></td>
            </tr>
            <tr>
                <td>PHP versão</td>
                <td><?php echo PHP_VERSION; ?></td>
            </tr>
            <tr>
                <td>Servidor Web</td>
                <td><?php echo $_SERVER['SERVER_SOFTWARE'] ?? 'Desconhecido'; ?></td>
            </tr>
            <tr>
                <td>Modo de Execução</td>
                <td><?php echo (php_sapi_name() === 'apache2handler' ? 'mod_php (módulo Apache)' : 'CGI/FastCGI'); ?></td>
            </tr>
            <tr>
                <td>Sistema Operacional</td>
                <td><?php echo PHP_OS; ?></td>
            </tr>
            <tr>
                <td>Espaço em disco livre</td>
                <td><?php echo formatBytes(disk_free_space(__DIR__)); ?></td>
            </tr>
        </table>
    </div>
    
    <div class="card">
        <h2>Teste de Upload</h2>
        <p>Use o formulário abaixo para testar o upload de arquivos grandes:</p>
        <form action="upload_test_handler.php" method="post" enctype="multipart/form-data" id="uploadForm">
            <div>
                <label for="fileToUpload">Selecionar arquivo:</label>
                <input type="file" name="fileToUpload" id="fileToUpload">
            </div>
            <br>
            <div>
                <input type="submit" value="Testar Upload" name="submit">
                <button type="button" id="fakeTestBtn">Simular Upload Grande (30MB)</button>
            </div>
            <br>
            <div class="progress-container" style="display: none;" id="progressContainer">
                <div class="progress-bar" id="progressBar">0%</div>
            </div>
            <div id="uploadResult"></div>
        </form>
    </div>
    
    <div class="card">
        <h2>Arquivos de Configuração</h2>
        <h3>.user.ini</h3>
        <pre><?php echo htmlspecialchars(file_exists(__DIR__ . '/.user.ini') ? file_get_contents(__DIR__ . '/.user.ini') : 'Arquivo não encontrado'); ?></pre>
        
        <h3>.htaccess (partes relevantes)</h3>
        <?php
        if (file_exists(__DIR__ . '/.htaccess')) {
            $htaccess = file_get_contents(__DIR__ . '/.htaccess');
            preg_match('/<IfModule mod_php\.c>(.*?)<\/IfModule>/s', $htaccess, $matches);
            echo '<pre>' . htmlspecialchars($matches[0] ?? 'Configuração PHP não encontrada') . '</pre>';
            
            preg_match('/<IfModule mod_fcgid\.c>(.*?)<\/IfModule>/s', $htaccess, $matches);
            echo '<pre>' . htmlspecialchars($matches[0] ?? 'Configuração FastCGI não encontrada') . '</pre>';
        } else {
            echo '<pre>Arquivo .htaccess não encontrado</pre>';
        }
        ?>
    </div>
    
    <script>
        document.getElementById('uploadForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const fileInput = document.getElementById('fileToUpload');
            if (!fileInput.files.length) {
                alert('Por favor, selecione um arquivo primeiro.');
                return;
            }
            
            uploadFile(fileInput.files[0]);
        });
        
        document.getElementById('fakeTestBtn').addEventListener('click', function() {
            const progressContainer = document.getElementById('progressContainer');
            const progressBar = document.getElementById('progressBar');
            const resultDiv = document.getElementById('uploadResult');
            
            progressContainer.style.display = 'block';
            resultDiv.innerHTML = '<p>Simulando upload de um arquivo de 30MB...</p>';
            
            let progress = 0;
            const interval = setInterval(function() {
                progress += 1;
                progressBar.style.width = progress + '%';
                progressBar.textContent = progress + '%';
                
                if (progress >= 100) {
                    clearInterval(interval);
                    resultDiv.innerHTML += '<p class="success">✓ Simulação concluída com sucesso!</p>';
                    resultDiv.innerHTML += '<p>Este teste simula apenas o progresso visual. Para um teste real, faça upload de um arquivo.</p>';
                }
            }, 50);
        });
        
        function uploadFile(file) {
            const progressContainer = document.getElementById('progressContainer');
            const progressBar = document.getElementById('progressBar');
            const resultDiv = document.getElementById('uploadResult');
            
            progressContainer.style.display = 'block';
            progressBar.style.width = '0%';
            progressBar.textContent = '0%';
            
            const xhr = new XMLHttpRequest();
            const formData = new FormData();
            
            formData.append('fileToUpload', file);
            
            xhr.upload.addEventListener('progress', function(e) {
                if (e.lengthComputable) {
                    const percent = Math.round((e.loaded / e.total) * 100);
                    progressBar.style.width = percent + '%';
                    progressBar.textContent = percent + '%';
                }
            });
            
            xhr.addEventListener('load', function() {
                if (xhr.status === 200) {
                    try {
                        const response = JSON.parse(xhr.responseText);
                        resultDiv.innerHTML = `
                            <h3>${response.success ? '<span class="success">✓ Upload bem-sucedido!</span>' : '<span class="error">✗ Falha no upload</span>'}</h3>
                            <table>
                                <tr><td>Nome do arquivo:</td><td>${response.fileName}</td></tr>
                                <tr><td>Tamanho:</td><td>${response.fileSize}</td></tr>
                                <tr><td>Tipo:</td><td>${response.fileType}</td></tr>
                                <tr><td>Tempo de processamento:</td><td>${response.processingTime}s</td></tr>
                            </table>
                            <div>${response.message}</div>
                        `;
                    } catch(e) {
                        resultDiv.innerHTML = '<div class="error">Erro ao processar resposta do servidor</div>';
                        resultDiv.innerHTML += '<pre>' + xhr.responseText + '</pre>';
                    }
                } else {
                    resultDiv.innerHTML = `<div class="error">Erro ${xhr.status}: ${xhr.statusText}</div>`;
                }
            });
            
            xhr.addEventListener('error', function() {
                resultDiv.innerHTML = '<div class="error">Erro de conexão</div>';
            });
            
            xhr.addEventListener('abort', function() {
                resultDiv.innerHTML = '<div class="warning">Upload cancelado</div>';
            });
            
            xhr.open('POST', 'upload_test_handler.php');
            xhr.send(formData);
        }
    </script>
</body>
</html>
