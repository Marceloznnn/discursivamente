<?php
// Mensagem de aviso sobre configurações PHP
// Esta página exibe um alerta importante sobre limites de upload não aplicados

?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Atenção: Configurações PHP | Discursivamente</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
        }
        .alert {
            background-color: #f8d7da;
            color: #721c24;
            padding: 20px;
            border-radius: 8px;
            border-left: 5px solid #721c24;
            margin-bottom: 20px;
        }
        .warning {
            background-color: #fff3cd;
            color: #856404;
            padding: 20px;
            border-radius: 8px;
            border-left: 5px solid #856404;
            margin-bottom: 20px;
        }
        h1, h2 {
            color: #2c3e50;
        }
        pre {
            background-color: #f8f9fa;
            padding: 15px;
            border-radius: 4px;
            overflow: auto;
        }
        .btn {
            background-color: #3498db;
            color: white;
            padding: 8px 16px;
            border-radius: 4px;
            text-decoration: none;
            display: inline-block;
            margin: 5px 0;
        }
        .btn:hover {
            background-color: #2980b9;
        }
        .code {
            font-family: monospace;
            background-color: #f1f1f1;
            padding: 2px 4px;
            border-radius: 3px;
        }
    </style>
</head>
<body>
    <h1>Atenção: Configurações PHP</h1>
    
    <div class="alert">
        <h2>⚠️ Configurações de Upload Não Aplicadas</h2>
        <p>Foi detectado que as configurações de limite de upload no PHP não estão sendo aplicadas corretamente.</p>
        <p>Valor atual de <span class="code">upload_max_filesize</span>: <strong><?php echo ini_get('upload_max_filesize'); ?></strong></p>
        <p>Valor esperado: <strong>200M</strong></p>
    </div>
    
    <h2>Como Resolver</h2>
    
    <div class="warning">
        <p>As configurações em <span class="code">.user.ini</span> ou <span class="code">.htaccess</span> não estão sendo reconhecidas pelo PHP.</p>
        <p>É necessário editar o arquivo <span class="code">php.ini</span> principal do seu servidor.</p>
    </div>
    
    <h3>Instruções para Correção:</h3>
    
    <ol>
        <li>
            <strong>Localize o arquivo php.ini principal:</strong>
            <ul>
                <li>Em instalações XAMPP: <span class="code">C:\xampp\php\php.ini</span></li>
                <li>No Linux: normalmente em <span class="code">/etc/php/VERSION/apache2/php.ini</span> ou <span class="code">/etc/php.ini</span></li>
            </ul>
        </li>
        <li>
            <strong>Edite o arquivo php.ini e altere as seguintes configurações:</strong>
            <pre>; Maximum allowed size for uploaded files.
upload_max_filesize = 200M

; Must be greater than or equal to upload_max_filesize
post_max_size = 200M

; Maximum execution time of each script, in seconds
max_execution_time = 600

; Maximum amount of time each script may spend parsing request data
max_input_time = 600

; Maximum amount of memory a script may consume
memory_limit = 512M</pre>
        </li>
        <li><strong>Salve o arquivo e reinicie o servidor Apache</strong></li>
    </ol>
    
    <h3>Após Fazer as Alterações:</h3>
    
    <p>Após editar o php.ini e reiniciar o servidor, execute os seguintes passos:</p>
    
    <ol>
        <li>Volte para a <a href="upload_diagnostics.php" class="btn">Página de Diagnósticos</a> para verificar se os limites foram atualizados</li>
        <li>Use a <a href="upload_test.php" class="btn">Página de Teste de Upload</a> para testar o upload de arquivos grandes</li>
    </ol>
    
    <h3>Documentação Completa</h3>
    
    <p>Instruções detalhadas sobre a configuração dos limites de upload estão disponíveis em:</p>
    <ul>
        <li><a href="PHP_LIMITES_SOLUCAO.md" class="btn">Guia Detalhado de Configuração</a></li>
    </ul>
    
    <hr>
    
    <p><small>Esta mensagem é exibida apenas para administradores do sistema.</small></p>
</body>
</html>
