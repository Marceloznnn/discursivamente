<?php
/**
 * Script de Segurança e Otimização para o Discursivamente 2.1
 * 
 * Este script implementa várias melhorias de segurança e otimização:
 * - Adição de cabeçalhos de segurança no .htaccess
 * - Criação de arquivo robots.txt aprimorado
 * - Configuração de CSP (Content Security Policy)
 * - Verificação de permissões de arquivos e diretórios
 * - Otimização e minificação de CSS/JS
 */

// Define a raiz do projeto
$projectRoot = realpath(__DIR__ . '/..');

// ====================================
// Cabeçalhos de segurança no .htaccess
// ====================================

$htaccessPath = $projectRoot . '/public/.htaccess';
$htaccessContent = '';

if (file_exists($htaccessPath)) {
    $htaccessContent = file_get_contents($htaccessPath);
} else {
    // Conteúdo básico para reescrita de URL se não existir
    $htaccessContent = "
<IfModule mod_rewrite.c>
    RewriteEngine On
    RewriteBase /
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteRule ^ index.php [QSA,L]
</IfModule>
";
}

// Adiciona cabeçalhos de segurança se ainda não existirem
if (strpos($htaccessContent, '<IfModule mod_headers.c>') === false) {
    $securityHeaders = "
# Cabeçalhos de segurança
<IfModule mod_headers.c>
    # Proteção contra clickjacking
    Header always set X-Frame-Options \"SAMEORIGIN\"
    
    # Proteção XSS
    Header always set X-XSS-Protection \"1; mode=block\"
    
    # Evitar MIME-type sniffing
    Header always set X-Content-Type-Options \"nosniff\"
    
    # Referrer Policy
    Header always set Referrer-Policy \"strict-origin-when-cross-origin\"
    
    # Permissões de origem
    Header always set Access-Control-Allow-Origin \"*\"
    
    # Habilitar HSTS (forçar HTTPS) - só descomente quando tiver SSL configurado
    # Header always set Strict-Transport-Security \"max-age=31536000; includeSubDomains; preload\"

    # Content Security Policy (CSP)
    # Header always set Content-Security-Policy \"default-src 'self'; script-src 'self' 'unsafe-inline' https://cdn.jsdelivr.net https://ajax.googleapis.com; style-src 'self' 'unsafe-inline' https://cdn.jsdelivr.net https://fonts.googleapis.com; font-src 'self' https://fonts.gstatic.com; img-src 'self' data: https://res.cloudinary.com; connect-src 'self' https://api.cloudinary.com;\"
</IfModule>

# Desabilitar listagem de diretórios
Options -Indexes

# Proteger arquivos sensíveis
<FilesMatch \"(^\.|\.env|composer\.(json|lock)|package(-lock)?\.json|\.gitignore|phpunit\.xml)\">
    <IfModule mod_authz_core.c>
        Require all denied
    </IfModule>
    <IfModule !mod_authz_core.c>
        Order deny,allow
        Deny from all
    </IfModule>
</FilesMatch>

# Compressão de conteúdo
<IfModule mod_deflate.c>
    AddOutputFilterByType DEFLATE text/html text/plain text/xml text/css text/javascript application/javascript application/json
</IfModule>

# Cache de navegador
<IfModule mod_expires.c>
    ExpiresActive On
    ExpiresByType image/jpg \"access plus 1 year\"
    ExpiresByType image/jpeg \"access plus 1 year\"
    ExpiresByType image/gif \"access plus 1 year\"
    ExpiresByType image/png \"access plus 1 year\"
    ExpiresByType image/webp \"access plus 1 year\"
    ExpiresByType image/svg+xml \"access plus 1 month\"
    ExpiresByType image/x-icon \"access plus 1 year\"
    ExpiresByType video/mp4 \"access plus 1 month\"
    ExpiresByType application/pdf \"access plus 1 month\"
    ExpiresByType text/css \"access plus 1 month\"
    ExpiresByType text/javascript \"access plus 1 month\"
    ExpiresByType application/javascript \"access plus 1 month\"
    ExpiresByType application/x-javascript \"access plus 1 month\"
    ExpiresByType text/html \"access plus 0 seconds\"
    ExpiresByType application/xhtml+xml \"access plus 0 seconds\"
</IfModule>
";

    // Adiciona os cabeçalhos de segurança ao .htaccess
    $htaccessContent .= $securityHeaders;
    file_put_contents($htaccessPath, $htaccessContent);
    echo "✅ Cabeçalhos de segurança adicionados ao .htaccess\n";
} else {
    echo "ℹ️ Cabeçalhos de segurança já existem no .htaccess\n";
}

// ====================================
// Arquivo robots.txt aprimorado
// ====================================

$robotsPath = $projectRoot . '/robots.txt';
$robotsContent = "
User-agent: *
Disallow: /src/
Disallow: /vendor/
Disallow: /scripts/
Disallow: /app/
Disallow: /admin/
Disallow: /config/
Disallow: /logs/
Disallow: /tmp/
Disallow: /.git/
Disallow: /.env
Disallow: /*.json$
Disallow: /*.lock$
Disallow: /*.php$
Allow: /public/*.php$
Allow: /index.php
Allow: /public/assets/
Allow: /public/images/
Allow: /public/css/
Allow: /public/js/

# Sitemaps
Sitemap: https://seusite.com/sitemap.xml
";

file_put_contents($robotsPath, $robotsContent);
echo "✅ Arquivo robots.txt criado/atualizado\n";

// ====================================
// Verificação de permissões
// ====================================

function checkAndFixPermissions($dir, $fileMode = 0644, $dirMode = 0755) {
    $iterator = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($dir, RecursiveDirectoryIterator::SKIP_DOTS),
        RecursiveIteratorIterator::SELF_FIRST
    );

    foreach ($iterator as $path => $fileInfo) {
        if ($fileInfo->isDir()) {
            if (substr($fileInfo->getFilename(), 0, 1) !== '.') { // Ignora diretórios ocultos
                chmod($path, $dirMode);
            }
        } else {
            chmod($path, $fileMode);
        }
    }
}

// Diretórios que precisam de permissões específicas
$diretoriosEscrita = [
    $projectRoot . '/tmp',
    $projectRoot . '/logs',
    $projectRoot . '/public/uploads',
];

// Garante que diretórios de escrita existam
foreach ($diretoriosEscrita as $dir) {
    if (!file_exists($dir)) {
        mkdir($dir, 0755, true);
        echo "✅ Diretório criado: $dir\n";
    }
}

// Aplicar permissões
foreach ($diretoriosEscrita as $dir) {
    checkAndFixPermissions($dir, 0644, 0755);
}
echo "✅ Permissões de diretórios verificadas e corrigidas\n";

// ====================================
// Minificação de CSS e JS (se necessário)
// ====================================

// Verifica se o pacote matthiasmullie/minify está instalado
if (class_exists('\\MatthiasMullie\\Minify\\Minify')) {
    echo "ℹ️ Pacote de minificação encontrado. Pode usar os comandos npm para minificação.\n";

    // Cria diretório para arquivos minificados se não existir
    if (!file_exists($projectRoot . '/public/assets/build')) {
        mkdir($projectRoot . '/public/assets/build', 0755, true);
        echo "✅ Diretório para arquivos minificados criado\n";
    }
    
} else {
    echo "⚠️ Recomendado instalar matthiasmullie/minify para otimização de CSS/JS\n";
}

// ====================================
// Verifica e aplica .gitignore
// ====================================

$gitignorePath = $projectRoot . '/.gitignore';
$gitignoreContent = '';

if (file_exists($gitignorePath)) {
    $gitignoreContent = file_get_contents($gitignorePath);
} else {
    $gitignoreContent = '';
}

$gitignoreRecommended = "
# Arquivos de ambiente
.env
.env.*
!.env.example

# Diretório vendor
/vendor/

# Arquivos de cache e temporários
/tmp/*
!/tmp/.gitkeep
/var/cache/*
!/var/cache/.gitkeep
/var/log/*
!/var/log/.gitkeep
/logs/*
!/logs/.gitkeep

# Arquivos minificados gerados
/public/assets/build/*
!/public/assets/build/.gitkeep

# Uploads de usuários
/public/uploads/*
!/public/uploads/.gitkeep

# Arquivos do PhpStorm
/.idea/

# Arquivos do VSCode
/.vscode/
*.code-workspace

# Arquivos do NetBeans
/nbproject/

# Arquivos do PHPUnit
.phpunit.result.cache

# Arquivo de backup do banco de dados
*.sql
*.sqlite

# Cache do composer
composer.phar
/.phpunit.cache/
/node_modules/
npm-debug.log
yarn-error.log
package-lock.json
yarn.lock

# Arquivos do sistema
.DS_Store
Thumbs.db
";

// Adiciona apenas seções faltantes no gitignore
$linesToAdd = [];
$gitignoreLines = explode("\n", $gitignoreContent);
$recommendedLines = explode("\n", $gitignoreRecommended);

foreach ($recommendedLines as $line) {
    $line = trim($line);
    if ($line && !in_array($line, $gitignoreLines) && $line[0] != '#') {
        $linesToAdd[] = $line;
    }
}

if (count($linesToAdd) > 0) {
    file_put_contents($gitignorePath, $gitignoreContent . "\n# Adições automáticas\n" . implode("\n", $linesToAdd) . "\n");
    echo "✅ Arquivo .gitignore atualizado\n";
} else {
    echo "ℹ️ Arquivo .gitignore já contém todas as recomendações\n";
}

// ====================================
// Criar arquivo .env.example se não existir
// ====================================

$envExamplePath = $projectRoot . '/.env.example';
if (!file_exists($envExamplePath)) {
    $envExampleContent = "# Configurações do Banco de Dados
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=discursivamente_db
DB_USERNAME=root
DB_PASSWORD=

# Configurações do Ambiente
APP_NAME=Discursivamente
APP_ENV=development
APP_DEBUG=true
APP_URL=http://localhost

# Configurações de E-mail
MAIL_HOST=smtp.example.com
MAIL_PORT=587
MAIL_USERNAME=projeto.discursivamente@gmail.com
MAIL_PASSWORD=frue xhal ioqg oqvq
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=no-reply@example.com
MAIL_FROM_NAME=Discursivamente

# Cloudinary
CLOUDINARY_CLOUD_NAME=seu_cloud_name
CLOUDINARY_API_KEY=sua_api_key
CLOUDINARY_API_SECRET=seu_api_secret
CLOUDINARY_URL=cloudinary://sua_api_key:seu_api_secret@seu_cloud_name

# Google OAuth
GOOGLE_CLIENT_ID=seu_client_id
GOOGLE_CLIENT_SECRET=seu_client_secret
GOOGLE_REDIRECT_URI=http://localhost:8000/auth/google/callback
";

    file_put_contents($envExamplePath, $envExampleContent);
    echo "✅ Arquivo .env.example criado\n";
} else {
    echo "ℹ️ Arquivo .env.example já existe\n";
}

echo "\n✅ Script de segurança e otimização concluído!\n";
