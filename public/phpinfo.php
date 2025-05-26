<?php
// Página phpinfo para diagnóstico de configuração do PHP
// AVISO: Esta página contém informações sensíveis, use apenas em ambiente de desenvolvimento!

// Validação básica de segurança
function isLocalIp() {
    $localIps = ['127.0.0.1', '::1'];
    return in_array($_SERVER['REMOTE_ADDR'], $localIps);
}

// Se não for acesso local, negar acesso
if (!isLocalIp()) {
    http_response_code(403);
    die('Acesso negado. Esta ferramenta só pode ser acessada localmente por razões de segurança.');
}

// Exibir informações do PHP
phpinfo();
?>
