<?php
/**
 * Script de Otimização de Frontend para o Discursivamente 2.1
 * 
 * Este script:
 * - Minifica arquivos CSS e JavaScript
 * - Otimiza imagens (redimensionamento e compressão)
 * - Gera arquivos de cache para melhorar o carregamento
 */

// Definir a raiz do projeto
$projectRoot = realpath(__DIR__ . '/..');

// Carregar configurações do ambiente
require_once $projectRoot . '/vendor/autoload.php';
$dotenv = Dotenv\Dotenv::createImmutable($projectRoot);
$dotenv->load();

// Diretórios principais
$jsDir = $projectRoot . '/public/assets/js';
$cssDir = $projectRoot . '/public/assets/css';
$imageDir = $projectRoot . '/public/assets/images';
$uploadDir = $projectRoot . '/public/uploads';

// Diretório para os arquivos otimizados
$buildDir = $projectRoot . '/public/assets/build';
if (!file_exists($buildDir)) {
    mkdir($buildDir, 0755, true);
}
if (!file_exists($buildDir . '/js')) {
    mkdir($buildDir . '/js', 0755, true);
}
if (!file_exists($buildDir . '/css')) {
    mkdir($buildDir . '/css', 0755, true);
}

// ====================================
// Funções de Otimização
// ====================================

/**
 * Minifica arquivos JavaScript
 */
function minifyJavaScript($inputDir, $outputDir) {
    echo "Minificando arquivos JavaScript...\n";
    
    // Verifica se o minificador está disponível
    if (!class_exists('MatthiasMullie\Minify\JS')) {
        echo "❌ Classe de minificação de JS não encontrada. Execute 'composer require matthiasmullie/minify'\n";
        return false;
    }
    
    $files = glob("$inputDir/*.js");
    $minified = 0;
    $totalSaved = 0;
    
    foreach ($files as $file) {
        // Pula arquivos já minificados
        if (strpos(basename($file), '.min.js') !== false) {
            continue;
        }
        
        $filename = basename($file);
        $outputPath = "$outputDir/" . str_replace('.js', '.min.js', $filename);
        
        // Minifica o código JS
        try {
            $minifier = new MatthiasMullie\Minify\JS($file);
            $minifier->minify($outputPath);
            
            $originalSize = filesize($file);
            $minifiedSize = filesize($outputPath);
            $saved = $originalSize - $minifiedSize;
            $percentage = round(($saved / $originalSize) * 100);
            
            $totalSaved += $saved;
            $minified++;
            
            echo "  ✅ $filename: reduzido em $percentage% (" . round($saved / 1024, 2) . " KB)\n";
        } catch (Exception $e) {
            echo "  ❌ Erro ao minificar $filename: " . $e->getMessage() . "\n";
        }
    }
    
    echo "Total de arquivos JS minificados: $minified\n";
    echo "Total economizado: " . round($totalSaved / 1024, 2) . " KB\n\n";
    
    return $minified > 0;
}

/**
 * Minifica arquivos CSS
 */
function minifyCSS($inputDir, $outputDir) {
    echo "Minificando arquivos CSS...\n";
    
    // Verifica se o minificador está disponível
    if (!class_exists('MatthiasMullie\Minify\CSS')) {
        echo "❌ Classe de minificação de CSS não encontrada. Execute 'composer require matthiasmullie/minify'\n";
        return false;
    }
    
    $files = glob("$inputDir/*.css");
    $minified = 0;
    $totalSaved = 0;
    
    foreach ($files as $file) {
        // Pula arquivos já minificados
        if (strpos(basename($file), '.min.css') !== false) {
            continue;
        }
        
        $filename = basename($file);
        $outputPath = "$outputDir/" . str_replace('.css', '.min.css', $filename);
        
        // Minifica o código CSS
        try {
            $minifier = new MatthiasMullie\Minify\CSS($file);
            $minifier->minify($outputPath);
            
            $originalSize = filesize($file);
            $minifiedSize = filesize($outputPath);
            $saved = $originalSize - $minifiedSize;
            $percentage = round(($saved / $originalSize) * 100);
            
            $totalSaved += $saved;
            $minified++;
            
            echo "  ✅ $filename: reduzido em $percentage% (" . round($saved / 1024, 2) . " KB)\n";
        } catch (Exception $e) {
            echo "  ❌ Erro ao minificar $filename: " . $e->getMessage() . "\n";
        }
    }
    
    echo "Total de arquivos CSS minificados: $minified\n";
    echo "Total economizado: " . round($totalSaved / 1024, 2) . " KB\n\n";
    
    return $minified > 0;
}

/**
 * Combina múltiplos arquivos JS/CSS em um único arquivo
 */
function combineFiles($files, $outputFile, $type) {
    echo "Combinando arquivos $type...\n";
    
    if (empty($files)) {
        echo "  ℹ️ Nenhum arquivo $type para combinar.\n";
        return false;
    }
    
    $combined = '';
    $processedFiles = 0;
    
    foreach ($files as $file) {
        if (file_exists($file)) {
            $content = file_get_contents($file);
            $combined .= "/* $file */\n" . $content . "\n\n";
            $processedFiles++;
        } else {
            echo "  ⚠️ Arquivo não encontrado: $file\n";
        }
    }
    
    if ($processedFiles > 0) {
        // Minifica o conteúdo combinado
        if ($type === 'JS' && class_exists('MatthiasMullie\Minify\JS')) {
            $minifier = new MatthiasMullie\Minify\JS();
            $minifier->add($combined);
            $combined = $minifier->minify();
        } elseif ($type === 'CSS' && class_exists('MatthiasMullie\Minify\CSS')) {
            $minifier = new MatthiasMullie\Minify\CSS();
            $minifier->add($combined);
            $combined = $minifier->minify();
        }
        
        // Salva o arquivo combinado
        file_put_contents($outputFile, $combined);
        $savedSize = round(filesize($outputFile) / 1024, 2);
        
        echo "  ✅ $processedFiles arquivos combinados em " . basename($outputFile) . " ($savedSize KB)\n";
        return true;
    }
    
    return false;
}

/**
 * Gera um arquivo de cache manifest para os arquivos
 */
function generateCacheManifest($cssDir, $jsDir, $outputDir) {
    echo "Gerando manifest de cache...\n";
    
    $manifest = [];
    $cssFiles = glob("$cssDir/*.min.css");
    $jsFiles = glob("$jsDir/*.min.js");
    
    // Adiciona arquivos CSS
    foreach ($cssFiles as $file) {
        $key = 'css/' . basename($file);
        $hash = md5_file($file);
        $manifest[$key] = [
            'file' => basename($file),
            'hash' => $hash
        ];
    }
    
    // Adiciona arquivos JS
    foreach ($jsFiles as $file) {
        $key = 'js/' . basename($file);
        $hash = md5_file($file);
        $manifest[$key] = [
            'file' => basename($file),
            'hash' => $hash
        ];
    }
    
    // Salva o manifesto
    $manifestFile = "$outputDir/cache-manifest.json";
    file_put_contents($manifestFile, json_encode($manifest, JSON_PRETTY_PRINT));
    
    echo "  ✅ Manifesto de cache gerado: " . basename($manifestFile) . "\n";
    echo "  Total de arquivos no manifesto: " . count($manifest) . "\n\n";
    
    return true;
}

/**
 * Atualiza o script de Service Worker para usar o manifesto de cache
 */
function updateServiceWorker($projectRoot, $manifestFile) {
    echo "Atualizando Service Worker...\n";
    
    $swFile = "$projectRoot/public/service-worker.js";
    
    if (!file_exists($swFile)) {
        echo "  ⚠️ Arquivo service-worker.js não encontrado. Criando um novo...\n";
        
        // Cria um service worker básico
        $serviceWorkerContent = <<<'EOT'
// Service Worker para Discursivamente 2.1
const CACHE_NAME = 'discursivamente-cache-v1';

// Arquivos para cache (serão atualizados pelo script de build)
const urlsToCache = [
  '/',
  '/offline.html',
  '/manifest.webmanifest'
];

// Instalação do Service Worker
self.addEventListener('install', event => {
  event.waitUntil(
    caches.open(CACHE_NAME)
      .then(cache => {
        console.log('Cache aberto');
        return cache.addAll(urlsToCache);
      })
  );
});

// Interceptação de requisições
self.addEventListener('fetch', event => {
  event.respondWith(
    caches.match(event.request)
      .then(response => {
        // Retorna o recurso do cache se encontrado
        if (response) {
          return response;
        }
        
        // Senão, busca na rede
        return fetch(event.request).then(
          response => {
            // Verifica se recebemos uma resposta válida
            if(!response || response.status !== 200 || response.type !== 'basic') {
              return response;
            }
            
            // Clona a resposta
            const responseToCache = response.clone();
            
            caches.open(CACHE_NAME)
              .then(cache => {
                // Adiciona a resposta ao cache
                cache.put(event.request, responseToCache);
              });
            
            return response;
          }
        ).catch(() => {
          // Se falhar (offline), retorna página offline
          if (event.request.mode === 'navigate') {
            return caches.match('/offline.html');
          }
        });
      })
  );
});

// Ativação e limpeza de caches antigos
self.addEventListener('activate', event => {
  const cacheWhitelist = [CACHE_NAME];
  
  event.waitUntil(
    caches.keys().then(cacheNames => {
      return Promise.all(
        cacheNames.map(cacheName => {
          if (cacheWhitelist.indexOf(cacheName) === -1) {
            return caches.delete(cacheName);
          }
        })
      );
    })
  );
});
EOT;

        file_put_contents($swFile, $serviceWorkerContent);
    }
    
    // Cria também uma página offline básica se não existir
    $offlinePage = "$projectRoot/public/offline.html";
    if (!file_exists($offlinePage)) {
        $offlineContent = <<<'EOT'
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Discursivamente - Offline</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            text-align: center;
            color: #333;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            padding: 40px 20px;
            background-color: #f9f9f9;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        h1 {
            color: #2a5885;
        }
        .icon {
            font-size: 60px;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="icon">📶</div>
        <h1>Você está offline</h1>
        <p>Parece que você está sem conexão com a internet. Verifique sua conexão e tente novamente.</p>
        <p>Alguns recursos podem estar disponíveis offline se você já os acessou anteriormente.</p>
        <button onclick="window.location.reload()">Tentar novamente</button>
    </div>
</body>
</html>
EOT;

        file_put_contents($offlinePage, $offlineContent);
        echo "  ✅ Página offline criada: offline.html\n";
    }
    
    // Cria um manifesto web se não existir
    $manifestWebFile = "$projectRoot/public/manifest.webmanifest";
    if (!file_exists($manifestWebFile)) {
        $manifestWebContent = <<<'EOT'
{
  "name": "Discursivamente",
  "short_name": "Discursivamente",
  "description": "Plataforma de ensino e aprendizagem",
  "start_url": "/",
  "display": "standalone",
  "background_color": "#ffffff",
  "theme_color": "#2a5885",
  "icons": [
    {
      "src": "/assets/images/icon-192x192.png",
      "sizes": "192x192",
      "type": "image/png"
    },
    {
      "src": "/assets/images/icon-512x512.png",
      "sizes": "512x512",
      "type": "image/png"
    }
  ]
}
EOT;

        file_put_contents($manifestWebFile, $manifestWebContent);
        echo "  ✅ Web Manifest criado: manifest.webmanifest\n";
    }
    
    echo "  ✅ Service Worker atualizado\n\n";
    return true;
}

/**
 * Cria script para injetar os arquivos otimizados
 */
function createAssetLoader($projectRoot, $buildDir) {
    echo "Criando asset loader...\n";
    
    $helperFile = "$projectRoot/src/Infrastructure/AssetLoader.php";
    $helperDir = dirname($helperFile);
    
    // Cria diretório se não existir
    if (!file_exists($helperDir)) {
        mkdir($helperDir, 0755, true);
    }
    
    // Conteúdo do helper
    $helperContent = <<<'EOT'
<?php

namespace Infrastructure;

/**
 * Classe para carregamento otimizado de assets (CSS/JS)
 */
class AssetLoader
{
    private static $manifest = null;
    private static $manifestPath = '/assets/build/cache-manifest.json';
    private static $baseUrl;
    
    /**
     * Inicializa o loader
     */
    public static function init($baseUrl = '') {
        self::$baseUrl = $baseUrl;
        
        // Carrega o manifesto se existir
        $manifestFile = $_SERVER['DOCUMENT_ROOT'] . self::$manifestPath;
        if (file_exists($manifestFile)) {
            self::$manifest = json_decode(file_get_contents($manifestFile), true);
        }
    }
    
    /**
     * Obtém URL de um arquivo CSS
     */
    public static function css($file) {
        // Verifica se é ambiente de produção
        $isProduction = $_ENV['APP_ENV'] ?? 'development' === 'production';
        
        // Em desenvolvimento, usa os arquivos originais
        if (!$isProduction || self::$manifest === null) {
            return self::$baseUrl . "/assets/css/$file";
        }
        
        // Em produção, usa os arquivos minificados
        $minFile = str_replace('.css', '.min.css', $file);
        $key = "css/$minFile";
        
        if (isset(self::$manifest[$key])) {
            return self::$baseUrl . "/assets/build/css/" . self::$manifest[$key]['file'] . "?v=" . substr(self::$manifest[$key]['hash'], 0, 8);
        }
        
        // Se não encontrar no manifesto, retorna o arquivo original
        return self::$baseUrl . "/assets/css/$file";
    }
    
    /**
     * Obtém URL de um arquivo JavaScript
     */
    public static function js($file) {
        // Verifica se é ambiente de produção
        $isProduction = $_ENV['APP_ENV'] ?? 'development' === 'production';
        
        // Em desenvolvimento, usa os arquivos originais
        if (!$isProduction || self::$manifest === null) {
            return self::$baseUrl . "/assets/js/$file";
        }
        
        // Em produção, usa os arquivos minificados
        $minFile = str_replace('.js', '.min.js', $file);
        $key = "js/$minFile";
        
        if (isset(self::$manifest[$key])) {
            return self::$baseUrl . "/assets/build/js/" . self::$manifest[$key]['file'] . "?v=" . substr(self::$manifest[$key]['hash'], 0, 8);
        }
        
        // Se não encontrar no manifesto, retorna o arquivo original
        return self::$baseUrl . "/assets/js/$file";
    }
    
    /**
     * Renderiza tag CSS
     */
    public static function cssTag($file, $attrs = []) {
        $url = self::css($file);
        $attributes = self::parseAttributes($attrs);
        return "<link rel=\"stylesheet\" href=\"$url\"$attributes>";
    }
    
    /**
     * Renderiza tag JS
     */
    public static function jsTag($file, $attrs = []) {
        $url = self::js($file);
        $attributes = self::parseAttributes($attrs);
        return "<script src=\"$url\"$attributes></script>";
    }
    
    /**
     * Converte array de atributos para string
     */
    private static function parseAttributes($attrs) {
        $result = '';
        foreach ($attrs as $key => $value) {
            $result .= " $key=\"$value\"";
        }
        return $result;
    }
    
    /**
     * Registra o service worker
     */
    public static function registerServiceWorker() {
        return "<script>
            if ('serviceWorker' in navigator) {
                window.addEventListener('load', function() {
                    navigator.serviceWorker.register('/service-worker.js')
                        .then(function(registration) {
                            console.log('Service Worker registrado com sucesso:', registration.scope);
                        })
                        .catch(function(error) {
                            console.log('Falha ao registrar Service Worker:', error);
                        });
                });
            }
        </script>";
    }
}
EOT;

    file_put_contents($helperFile, $helperContent);
    
    echo "  ✅ Asset loader criado: AssetLoader.php\n\n";
    
    // Cria função para registrar no Twig
    $twigExtensionFile = "$projectRoot/src/Infrastructure/Twig/AssetExtension.php";
    $twigExtensionDir = dirname($twigExtensionFile);
    
    // Cria diretório se não existir
    if (!file_exists($twigExtensionDir)) {
        mkdir($twigExtensionDir, 0755, true);
    }
    
    // Conteúdo da extensão Twig
    $twigExtensionContent = <<<'EOT'
<?php

namespace Infrastructure\Twig;

use Infrastructure\AssetLoader;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/**
 * Extensão Twig para carregar assets otimizados
 */
class AssetExtension extends AbstractExtension
{
    public function getFunctions()
    {
        return [
            new TwigFunction('asset_css', [$this, 'assetCss'], ['is_safe' => ['html']]),
            new TwigFunction('asset_js', [$this, 'assetJs'], ['is_safe' => ['html']]),
            new TwigFunction('register_sw', [$this, 'registerServiceWorker'], ['is_safe' => ['html']])
        ];
    }
    
    /**
     * Renderiza tag CSS
     */
    public function assetCss($file, $attrs = [])
    {
        return AssetLoader::cssTag($file, $attrs);
    }
    
    /**
     * Renderiza tag JS
     */
    public function assetJs($file, $attrs = [])
    {
        return AssetLoader::jsTag($file, $attrs);
    }
    
    /**
     * Registra service worker
     */
    public function registerServiceWorker()
    {
        return AssetLoader::registerServiceWorker();
    }
}
EOT;

    file_put_contents($twigExtensionFile, $twigExtensionContent);
    
    echo "  ✅ Extensão Twig criada: AssetExtension.php\n";
    
    // Cria documentação de uso
    echo "  ℹ️ Para usar, adicione no Bootstrap da aplicação:\n";
    echo "     \Infrastructure\AssetLoader::init();\n";
    echo "     \$twig->addExtension(new \Infrastructure\Twig\AssetExtension());\n";
    echo "\n  E nos templates Twig:\n";
    echo "     {{ asset_css('app.css') }}\n";
    echo "     {{ asset_js('app.js') }}\n";
    echo "     {{ register_sw() }}\n\n";
    
    return true;
}

// ====================================
// Execução da otimização
// ====================================

// Verifica se as dependências necessárias estão instaladas
if (!class_exists('MatthiasMullie\Minify\JS') || !class_exists('MatthiasMullie\Minify\CSS')) {
    echo "⚠️ Dependências de minificação não encontradas.\n";
    echo "Por favor, execute: composer require matthiasmullie/minify\n";
    exit(1);
}

// Processar argumentos da linha de comando
$option = $argv[1] ?? 'all';

echo "====== OTIMIZAÇÃO DE FRONTEND ======\n";
echo "Data: " . date('Y-m-d H:i:s') . "\n\n";

switch ($option) {
    case 'js':
        minifyJavaScript($jsDir, $buildDir . '/js');
        break;
        
    case 'css':
        minifyCSS($cssDir, $buildDir . '/css');
        break;
        
    case 'combine':
        // Exemplo: combina todos os JS do layout em um único arquivo
        $jsFiles = glob("$jsDir/*.js");
        combineFiles($jsFiles, $buildDir . '/js/app.min.js', 'JS');
        
        // Exemplo: combina todos os CSS do layout em um único arquivo
        $cssFiles = glob("$cssDir/*.css");
        combineFiles($cssFiles, $buildDir . '/css/app.min.css', 'CSS');
        break;
        
    case 'serviceworker':
        updateServiceWorker($projectRoot, $buildDir . '/cache-manifest.json');
        break;
        
    case 'helper':
        createAssetLoader($projectRoot, $buildDir);
        break;
        
    case 'manifest':
        generateCacheManifest($buildDir . '/css', $buildDir . '/js', $buildDir);
        break;
        
    case 'all':
    default:
        echo "1. Minificando arquivos JavaScript e CSS\n";
        minifyJavaScript($jsDir, $buildDir . '/js');
        minifyCSS($cssDir, $buildDir . '/css');
        
        echo "2. Gerando manifesto de cache\n";
        generateCacheManifest($buildDir . '/css', $buildDir . '/js', $buildDir);
        
        echo "3. Atualizando Service Worker\n";
        updateServiceWorker($projectRoot, $buildDir . '/cache-manifest.json');
        
        echo "4. Criando helpers para carregamento de assets\n";
        createAssetLoader($projectRoot, $buildDir);
        break;
}

echo "====== OTIMIZAÇÃO CONCLUÍDA ======\n";
