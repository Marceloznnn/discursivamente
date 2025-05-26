<?php
// Diagnóstico do sistema de upload - página de informações técnicas

require_once __DIR__ . '/../vendor/autoload.php';

use Config\UploadConfig;
use Config\Logger;

// Inicializar o logger
Logger::init();

// Converter para bytes
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

// Verificar se estamos dentro de um XAMPP
$isXampp = stripos($_SERVER['SERVER_SOFTWARE'] ?? '', 'xampp') !== false;

// Verificar o modo de execução do PHP
$phpMode = php_sapi_name(); 
$isModPhp = $phpMode === 'apache2handler';
$isFastCgi = stripos($phpMode, 'cgi') !== false || stripos($phpMode, 'fpm') !== false;

// Detectar problemas comuns
$problems = [];
$warnings = [];
$recommendations = [];

// Verificar limites PHP
if (convertToBytes(ini_get('upload_max_filesize')) < 209715200) { // 200MB
    $problems[] = 'O limite upload_max_filesize está abaixo do requerido (200MB)';
}

if (convertToBytes(ini_get('post_max_size')) < 209715200) { // 200MB
    $problems[] = 'O limite post_max_size está abaixo do requerido (200MB)';
}

if (convertToBytes(ini_get('memory_limit')) < 536870912 && convertToBytes(ini_get('memory_limit')) > 0) { // 512MB
    $warnings[] = 'O limite memory_limit pode ser insuficiente para arquivos grandes';
}

if ((int)ini_get('max_execution_time') < 600 && (int)ini_get('max_execution_time') > 0) {
    $warnings[] = 'O limite max_execution_time pode ser insuficiente para uploads de vídeo';
}

// Verificar limites da aplicação
if (UploadConfig::MAX_VIDEO_SIZE < 209715200) { // 200MB
    $problems[] = 'O limite MAX_VIDEO_SIZE na aplicação está abaixo do requerido (200MB)';
}

if (UploadConfig::MAX_IMAGE_SIZE < 52428800) { // 50MB
    $problems[] = 'O limite MAX_IMAGE_SIZE na aplicação está abaixo do requerido (50MB)';
}

// Verificar arquivos de configuração
if (!file_exists(__DIR__ . '/.user.ini')) {
    $warnings[] = 'Arquivo .user.ini não encontrado na pasta public';
    $recommendations[] = 'Crie o arquivo .user.ini na pasta public com os limites adequados';
}

if ($isFastCgi && !preg_match('/FcgidMaxRequestLen\s+209715200/i', file_get_contents(__DIR__ . '/.htaccess'))) {
    $warnings[] = 'FcgidMaxRequestLen pode não estar configurado corretamente para FastCGI';
    $recommendations[] = 'No arquivo .htaccess, adicione FcgidMaxRequestLen 209715200 na seção mod_fcgid.c';
}

// Verificar diretório de uploads temporário
$tmpdir = sys_get_temp_dir();
$tmpIsFree = @is_writable($tmpdir);
$tmpFreeSpace = @disk_free_space($tmpdir);

if (!$tmpIsFree) {
    $problems[] = 'Diretório temporário não tem permissão de escrita: ' . $tmpdir;
}

if ($tmpFreeSpace && $tmpFreeSpace < 1073741824) { // 1GB
    $warnings[] = 'Pouco espaço livre no diretório temporário (menos de 1GB)';
}

// Registrar o diagnóstico
Logger::log('Diagnóstico de upload executado', [
    'problemas' => $problems,
    'avisos' => $warnings,
    'recomendacoes' => $recommendations,
    'php_mode' => $phpMode,
    'limites' => [
        'upload_max_filesize' => ini_get('upload_max_filesize'),
        'post_max_size' => ini_get('post_max_size'),
        'memory_limit' => ini_get('memory_limit'),
        'max_execution_time' => ini_get('max_execution_time'),
    ]
]);
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Diagnóstico do Sistema de Upload | Discursivamente</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 1000px;
            margin: 0 auto;
            padding: 20px;
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
        .problem {
            color: #e74c3c;
            background-color: #fde8e7;
            padding: 8px 12px;
            border-radius: 4px;
            margin: 5px 0;
            border-left: 4px solid #e74c3c;
        }
        .warning {
            color: #e67e22;
            background-color: #fef5e9;
            padding: 8px 12px;
            border-radius: 4px;
            margin: 5px 0;
            border-left: 4px solid #e67e22;
        }
        .good {
            color: #27ae60;
            background-color: #e7faf0;
            padding: 8px 12px;
            border-radius: 4px;
            margin: 5px 0;
            border-left: 4px solid #27ae60;
        }
        .recommendation {
            color: #3498db;
            background-color: #ebf5fb;
            padding: 8px 12px;
            border-radius: 4px;
            margin: 5px 0;
            border-left: 4px solid #3498db;
        }
        pre {
            background-color: #f8f9fa;
            padding: 15px;
            border-radius: 4px;
            overflow: auto;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            text-align: left;
            padding: 8px 12px;
            border-bottom: 1px solid #e9e9e9;
        }
        th {
            background-color: #f2f2f2;
        }
        .btn {
            background-color: #3498db;
            color: white;
            padding: 8px 16px;
            text-decoration: none;
            border-radius: 4px;
            display: inline-block;
            margin: 5px 0;
            border: none;
            cursor: pointer;
        }
        .btn:hover {
            background-color: #2980b9;
        }
    </style>
</head>
<body>
    <h1>Diagnóstico do Sistema de Upload - Discursivamente</h1>
    <p>Esta página fornece um diagnóstico completo das configurações de upload e identifica possíveis problemas.</p>
    
    <div class="card">
        <h2>Resumo do Diagnóstico</h2>
        
        <?php if (empty($problems) && empty($warnings)): ?>
            <div class="good">
                ✅ O sistema de upload parece estar configurado corretamente para arquivos grandes.
            </div>
        <?php else: ?>
            <?php if (!empty($problems)): ?>
                <h3>Problemas Detectados</h3>
                <?php foreach ($problems as $problem): ?>
                    <div class="problem">❌ <?php echo htmlspecialchars($problem); ?></div>
                <?php endforeach; ?>
            <?php endif; ?>
            
            <?php if (!empty($warnings)): ?>
                <h3>Avisos</h3>
                <?php foreach ($warnings as $warning): ?>
                    <div class="warning">⚠️ <?php echo htmlspecialchars($warning); ?></div>
                <?php endforeach; ?>
            <?php endif; ?>
        <?php endif; ?>
        
        <?php if (!empty($recommendations)): ?>
            <h3>Recomendações</h3>
            <?php foreach ($recommendations as $rec): ?>
                <div class="recommendation">💡 <?php echo htmlspecialchars($rec); ?></div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
    
    <div class="card">
        <h2>Ambiente PHP</h2>
        <table>
            <tr>
                <th>Configuração</th>
                <th>Valor</th>
                <th>Status</th>
            </tr>
            <tr>
                <td>Versão do PHP</td>
                <td><?php echo PHP_VERSION; ?></td>
                <td>
                    <?php if (version_compare(PHP_VERSION, '7.4.0') >= 0): ?>
                        <span class="good">✅ OK</span>
                    <?php else: ?>
                        <span class="warning">⚠️ Versão antiga</span>
                    <?php endif; ?>
                </td>
            </tr>
            <tr>
                <td>Modo de execução PHP</td>
                <td><?php echo $phpMode; ?></td>
                <td>
                    <?php if ($isModPhp): ?>
                        <span class="good">✅ mod_php (mais simples de configurar)</span>
                    <?php elseif ($isFastCgi): ?>
                        <span class="warning">⚠️ FastCGI (requer configuração adicional)</span>
                    <?php else: ?>
                        <span class="warning">⚠️ Outro modo (<?php echo $phpMode; ?>)</span>
                    <?php endif; ?>
                </td>
            </tr>
            <tr>
                <td>Servidor Web</td>
                <td><?php echo $_SERVER['SERVER_SOFTWARE'] ?? 'Desconhecido'; ?></td>
                <td>
                    <?php if ($isXampp): ?>
                        <span class="good">✅ XAMPP (ambiente de desenvolvimento)</span>
                    <?php else: ?>
                        <span class="good">✅ <?php echo htmlspecialchars($_SERVER['SERVER_SOFTWARE'] ?? 'Desconhecido'); ?></span>
                    <?php endif; ?>
                </td>
            </tr>
            <tr>
                <td>Sistema Operacional</td>
                <td><?php echo PHP_OS; ?></td>
                <td><span class="good">✅ OK</span></td>
            </tr>
            <tr>
                <td>Diretório Temporário</td>
                <td><?php echo $tmpdir; ?></td>
                <td>
                    <?php if ($tmpIsFree): ?>
                        <span class="good">✅ Gravável</span>
                    <?php else: ?>
                        <span class="problem">❌ Não gravável</span>
                    <?php endif; ?>
                </td>
            </tr>
            <tr>
                <td>Espaço livre no diretório temporário</td>
                <td><?php echo $tmpFreeSpace ? round($tmpFreeSpace / 1024 / 1024 / 1024, 2) . ' GB' : 'Desconhecido'; ?></td>
                <td>
                    <?php if ($tmpFreeSpace && $tmpFreeSpace < 1073741824): ?>
                        <span class="warning">⚠️ Menos de 1GB disponível</span>
                    <?php elseif ($tmpFreeSpace): ?>
                        <span class="good">✅ Suficiente</span>
                    <?php else: ?>
                        <span class="warning">⚠️ Não foi possível determinar</span>
                    <?php endif; ?>
                </td>
            </tr>
        </table>
    </div>
    
    <div class="card">
        <h2>Limites de Upload</h2>
        <table>
            <tr>
                <th>Configuração</th>
                <th>Valor atual</th>
                <th>Valor em bytes</th>
                <th>Valor recomendado</th>
                <th>Status</th>
            </tr>
            <tr>
                <td>upload_max_filesize</td>
                <td><?php echo ini_get('upload_max_filesize'); ?></td>
                <td><?php echo number_format(convertToBytes(ini_get('upload_max_filesize'))); ?></td>
                <td>200M</td>
                <td>
                    <?php if (convertToBytes(ini_get('upload_max_filesize')) >= 209715200): ?>
                        <span class="good">✅ OK</span>
                    <?php else: ?>
                        <span class="problem">❌ Insuficiente</span>
                    <?php endif; ?>
                </td>
            </tr>
            <tr>
                <td>post_max_size</td>
                <td><?php echo ini_get('post_max_size'); ?></td>
                <td><?php echo number_format(convertToBytes(ini_get('post_max_size'))); ?></td>
                <td>200M</td>
                <td>
                    <?php if (convertToBytes(ini_get('post_max_size')) >= 209715200): ?>
                        <span class="good">✅ OK</span>
                    <?php else: ?>
                        <span class="problem">❌ Insuficiente</span>
                    <?php endif; ?>
                </td>
            </tr>
            <tr>
                <td>memory_limit</td>
                <td><?php echo ini_get('memory_limit'); ?></td>
                <td><?php echo number_format(convertToBytes(ini_get('memory_limit'))); ?></td>
                <td>512M</td>
                <td>
                    <?php if (convertToBytes(ini_get('memory_limit')) >= 536870912 || convertToBytes(ini_get('memory_limit')) < 0): ?>
                        <span class="good">✅ OK</span>
                    <?php else: ?>
                        <span class="warning">⚠️ Pode ser insuficiente</span>
                    <?php endif; ?>
                </td>
            </tr>
            <tr>
                <td>max_execution_time</td>
                <td><?php echo ini_get('max_execution_time'); ?> segundos</td>
                <td>N/A</td>
                <td>600 segundos</td>
                <td>
                    <?php if ((int)ini_get('max_execution_time') >= 600 || (int)ini_get('max_execution_time') == 0): ?>
                        <span class="good">✅ OK</span>
                    <?php else: ?>
                        <span class="warning">⚠️ Pode ser insuficiente</span>
                    <?php endif; ?>
                </td>
            </tr>
            <tr>
                <td>max_input_time</td>
                <td><?php echo ini_get('max_input_time'); ?> segundos</td>
                <td>N/A</td>
                <td>600 segundos</td>
                <td>
                    <?php if ((int)ini_get('max_input_time') >= 600 || (int)ini_get('max_input_time') == -1): ?>
                        <span class="good">✅ OK</span>
                    <?php else: ?>
                        <span class="warning">⚠️ Pode ser insuficiente</span>
                    <?php endif; ?>
                </td>
            </tr>
        </table>
        
        <h3>Limites da Aplicação</h3>
        <table>
            <tr>
                <th>Tipo de arquivo</th>
                <th>Limite atual</th>
                <th>Bytes</th>
                <th>Recomendado</th>
                <th>Status</th>
            </tr>
            <tr>
                <td>Imagens (MAX_IMAGE_SIZE)</td>
                <td><?php echo round(UploadConfig::MAX_IMAGE_SIZE / 1024 / 1024, 2); ?> MB</td>
                <td><?php echo number_format(UploadConfig::MAX_IMAGE_SIZE); ?></td>
                <td>50 MB</td>
                <td>
                    <?php if (UploadConfig::MAX_IMAGE_SIZE >= 52428800): ?>
                        <span class="good">✅ OK</span>
                    <?php else: ?>
                        <span class="problem">❌ Insuficiente</span>
                    <?php endif; ?>
                </td>
            </tr>
            <tr>
                <td>Vídeos (MAX_VIDEO_SIZE)</td>
                <td><?php echo round(UploadConfig::MAX_VIDEO_SIZE / 1024 / 1024, 2); ?> MB</td>
                <td><?php echo number_format(UploadConfig::MAX_VIDEO_SIZE); ?></td>
                <td>200 MB</td>
                <td>
                    <?php if (UploadConfig::MAX_VIDEO_SIZE >= 209715200): ?>
                        <span class="good">✅ OK</span>
                    <?php else: ?>
                        <span class="problem">❌ Insuficiente</span>
                    <?php endif; ?>
                </td>
            </tr>
            <tr>
                <td>Áudios (MAX_AUDIO_SIZE)</td>
                <td><?php echo round(UploadConfig::MAX_AUDIO_SIZE / 1024 / 1024, 2); ?> MB</td>
                <td><?php echo number_format(UploadConfig::MAX_AUDIO_SIZE); ?></td>
                <td>100 MB</td>
                <td>
                    <?php if (UploadConfig::MAX_AUDIO_SIZE >= 104857600): ?>
                        <span class="good">✅ OK</span>
                    <?php else: ?>
                        <span class="warning">⚠️ Pode ser insuficiente</span>
                    <?php endif; ?>
                </td>
            </tr>
        </table>
    </div>
    
    <div class="card">
        <h2>Cloudinary - Serviço de Upload na Nuvem</h2>
        <?php
        $cloudinaryConfigured = isset($_ENV['CLOUDINARY_CLOUD_NAME']) && isset($_ENV['CLOUDINARY_API_KEY']) && 
                               isset($_ENV['CLOUDINARY_API_SECRET']) && isset($_ENV['CLOUDINARY2_CLOUD_NAME']);
        ?>
        
        <?php if ($cloudinaryConfigured): ?>
            <div class="good">✅ O serviço Cloudinary está configurado.</div>
            <table>
                <tr>
                    <th>Configuração</th>
                    <th>Status</th>
                </tr>
                <tr>
                    <td>Cloud Name (Imagens)</td>
                    <td>
                        <?php if (!empty($_ENV['CLOUDINARY_CLOUD_NAME'])): ?>
                            <span class="good">✅ Configurado (<?php echo htmlspecialchars($_ENV['CLOUDINARY_CLOUD_NAME']); ?>)</span>
                        <?php else: ?>
                            <span class="problem">❌ Não configurado</span>
                        <?php endif; ?>
                    </td>
                </tr>
                <tr>
                    <td>Cloud Name (Vídeos)</td>
                    <td>
                        <?php if (!empty($_ENV['CLOUDINARY2_CLOUD_NAME'])): ?>
                            <span class="good">✅ Configurado (<?php echo htmlspecialchars($_ENV['CLOUDINARY2_CLOUD_NAME']); ?>)</span>
                        <?php else: ?>
                            <span class="problem">❌ Não configurado</span>
                        <?php endif; ?>
                    </td>
                </tr>
            </table>
            
            <h3>Limites Cloudinary</h3>
            <p>O Cloudinary tem seus próprios limites de upload que podem variar de acordo com seu plano:</p>
            <ul>
                <li>Plano Free: Limite de 10MB por upload de imagem</li>
                <li>Plano Free: Limite de 100MB por upload de vídeo</li>
                <li>Planos pagos: Limites maiores dependendo do plano</li>
            </ul>
            <p>Verifique seu plano atual no <a href="https://cloudinary.com/console" target="_blank">Console do Cloudinary</a>.</p>
        <?php else: ?>
            <div class="warning">⚠️ O serviço Cloudinary não está completamente configurado.</div>
            <p>As variáveis de ambiente do Cloudinary não estão definidas. 
               Para usar o Cloudinary, você precisa definir as seguintes variáveis:</p>
            <ul>
                <li>CLOUDINARY_CLOUD_NAME</li>
                <li>CLOUDINARY_API_KEY</li>
                <li>CLOUDINARY_API_SECRET</li>
                <li>CLOUDINARY2_CLOUD_NAME</li>
                <li>CLOUDINARY2_API_KEY</li>
                <li>CLOUDINARY2_API_SECRET</li>
            </ul>
        <?php endif; ?>
    </div>
    
    <div class="card">
        <h2>Ações</h2>
        <a href="upload_test.php" class="btn">Ir para a página de teste de upload</a>
        <a href="phpinfo.php" class="btn">Ver phpinfo()</a>
        
        <h3>Logs</h3>
        <p>Os logs de upload estão disponíveis em:</p>
        <pre><?php echo realpath(__DIR__ . '/../logs'); ?></pre>
        
        <?php if (file_exists(__DIR__ . '/../logs/uploads.log')): ?>
            <p>Log de uploads: <a href="viewlog.php?log=uploads" class="btn">Ver log de uploads</a></p>
        <?php endif; ?>
        
        <?php if (file_exists(__DIR__ . '/../logs/errors.log')): ?>
            <p>Log de erros: <a href="viewlog.php?log=errors" class="btn">Ver log de erros</a></p>
        <?php endif; ?>
    </div>
    
    <footer>
        <p><small>Relatório gerado em: <?php echo date('d/m/Y H:i:s'); ?></small></p>
    </footer>
</body>
</html>
