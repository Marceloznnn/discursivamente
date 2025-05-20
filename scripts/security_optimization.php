<?php
/**
 * Script de Segurança e Otimização para o Discursivamente 2.1 (Aprimorado)
 * 
 * Funcionalidades:
 * - Cabeçalhos de segurança em .htaccess
 * - Criação do robots.txt
 * - Verificação e correção de permissões
 * - Minificação de CSS/JS (real)
 * - Atualização do .gitignore
 * - Criação do .env.example com dados genéricos
 */

require_once __DIR__ . '/../vendor/autoload.php';

$projectRoot = realpath(__DIR__ . '/..');

// ===================================================
// Utilitários
// ===================================================

function escrever($msg) {
    echo "✅ {$msg}\n";
}

function avisar($msg) {
    echo "ℹ️ {$msg}\n";
}

function alerta($msg) {
    echo "⚠️ {$msg}\n";
}

// ===================================================
// .htaccess com segurança
// ===================================================

$htaccessPath = $projectRoot . '/public/.htaccess';
$htaccessContent = file_exists($htaccessPath) ? file_get_contents($htaccessPath) : '';

if (strpos($htaccessContent, 'Header always set X-Frame-Options') === false) {
    $seguranca = <<<HTACCESS

# Segurança
<IfModule mod_headers.c>
    Header always set X-Frame-Options "SAMEORIGIN"
    Header always set X-XSS-Protection "1; mode=block"
    Header always set X-Content-Type-Options "nosniff"
    Header always set Referrer-Policy "strict-origin-when-cross-origin"
    Header always set Access-Control-Allow-Origin "*"

    # CSP básica - ajuste conforme necessidade
    Header set Content-Security-Policy "default-src 'self'; script-src 'self' 'unsafe-inline'; style-src 'self' 'unsafe-inline'; img-src 'self' data:; font-src 'self';"
</IfModule>

Options -Indexes

<FilesMatch "^(\.|\.env|composer\.(json|lock)|package(-lock)?\.json|\.gitignore|phpunit\.xml)$">
    <IfModule mod_authz_core.c>
        Require all denied
    </IfModule>
    <IfModule !mod_authz_core.c>
        Order deny,allow
        Deny from all
    </IfModule>
</FilesMatch>

<IfModule mod_deflate.c>
    AddOutputFilterByType DEFLATE text/html text/plain text/xml text/css text/javascript application/javascript application/json
</IfModule>

<IfModule mod_expires.c>
    ExpiresActive On
    ExpiresByType image/webp "access plus 1 year"
    ExpiresByType text/css "access plus 1 month"
    ExpiresByType application/javascript "access plus 1 month"
</IfModule>

HTACCESS;

    file_put_contents($htaccessPath, $htaccessContent . "\n" . $seguranca);
    escrever(".htaccess atualizado com cabeçalhos de segurança");
} else {
    avisar("Cabeçalhos de segurança já presentes em .htaccess");
}

// ===================================================
// robots.txt
// ===================================================

$robotsPath = $projectRoot . '/robots.txt';
$robotsContent = <<<ROBOTS
User-agent: *
Disallow: /src/
Disallow: /vendor/
Disallow: /app/
Disallow: /logs/
Disallow: /.env
Disallow: /*.json$
Disallow: /*.php$
Allow: /public/assets/
Allow: /public/css/
Allow: /public/js/
Sitemap: https://seudominio.com/sitemap.xml
ROBOTS;

file_put_contents($robotsPath, $robotsContent);
escrever("robots.txt criado/atualizado");

// ===================================================
// Verificação de permissões
// ===================================================

function ajustarPermissoes($dir, $fileMode = 0644, $dirMode = 0755) {
    $iterator = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($dir, RecursiveDirectoryIterator::SKIP_DOTS),
        RecursiveIteratorIterator::SELF_FIRST
    );

    foreach ($iterator as $item) {
        chmod($item, $item->isDir() ? $dirMode : $fileMode);
    }
}

$pastas = [
    $projectRoot . '/tmp',
    $projectRoot . '/logs',
    $projectRoot . '/public/uploads',
];

foreach ($pastas as $pasta) {
    if (!file_exists($pasta)) {
        mkdir($pasta, 0755, true);
        escrever("Diretório criado: $pasta");
    }
    ajustarPermissoes($pasta);
}
escrever("Permissões ajustadas");

// ===================================================
// Minificação real (CSS/JS)
// ===================================================

if (class_exists('\\MatthiasMullie\\Minify\\Minify')) {
    $buildDir = $projectRoot . '/public/assets/build';
    if (!file_exists($buildDir)) {
        mkdir($buildDir, 0755, true);
        escrever("Diretório para minificados criado");
    }

    $cssSource = $projectRoot . '/public/assets/css/main.css';
    $jsSource  = $projectRoot . '/public/assets/js/main.js';

    if (file_exists($cssSource)) {
        $minifier = new \MatthiasMullie\Minify\CSS($cssSource);
        $minifier->minify($buildDir . '/main.min.css');
        escrever("CSS minificado com sucesso");
    } else {
        alerta("Arquivo CSS não encontrado: $cssSource");
    }

    if (file_exists($jsSource)) {
        $minifier = new \MatthiasMullie\Minify\JS($jsSource);
        $minifier->minify($buildDir . '/main.min.js');
        escrever("JS minificado com sucesso");
    } else {
        alerta("Arquivo JS não encontrado: $jsSource");
    }
} else {
    alerta("Instale matthiasmullie/minify via Composer para minificação real de CSS/JS");
}

// ===================================================
// .gitignore
// ===================================================

$gitignorePath = $projectRoot . '/.gitignore';
$linhasNecessarias = [
    '.env', '/vendor/', '/tmp/', '/logs/', '/public/assets/build/', '/public/uploads/', '/.idea/', '/.vscode/', '*.sql', '/node_modules/', '*.lock', '.DS_Store'
];

$gitignoreAtual = file_exists($gitignorePath) ? file_get_contents($gitignorePath) : '';
foreach ($linhasNecessarias as $linha) {
    if (strpos($gitignoreAtual, $linha) === false) {
        $gitignoreAtual .= "\n$linha";
    }
}
file_put_contents($gitignorePath, $gitignoreAtual);
escrever(".gitignore atualizado");

// ===================================================
// .env.example
// ===================================================

$envExamplePath = $projectRoot . '/.env.example';
if (!file_exists($envExamplePath)) {
    $envExample = <<<ENV
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=seubanco
DB_USERNAME=root
DB_PASSWORD=senha

APP_NAME=Discursivamente
APP_ENV=development
APP_DEBUG=true
APP_URL=http://localhost

MAIL_HOST=smtp.mail.com
MAIL_PORT=587
MAIL_USERNAME=email@mail.com
MAIL_PASSWORD=sua_senha
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=no-reply@mail.com
MAIL_FROM_NAME=Discursivamente

CLOUDINARY_CLOUD_NAME=sua_cloud
CLOUDINARY_API_KEY=api_key
CLOUDINARY_API_SECRET=api_secret

GOOGLE_CLIENT_ID=client_id
GOOGLE_CLIENT_SECRET=client_secret
GOOGLE_REDIRECT_URI=http://localhost/auth/google/callback
ENV;

    file_put_contents($envExamplePath, $envExample);
    escrever(".env.example criado");
} else {
    avisar(".env.example já existe");
}

echo "\n✅ Script concluído com sucesso!\n";
