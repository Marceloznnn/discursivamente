<?php
// config/database.php
// Carrega variáveis do .env para $_ENV e $_SERVER (apenas se não estiverem setadas)
$envPath = __DIR__ . '/../.env';
if (file_exists($envPath)) {
    $lines = file($envPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0 || strpos($line, '=') === false) continue;
        list($name, $value) = explode('=', $line, 2);
        $name = trim($name);
        $value = trim($value, " \t\n\r\0\x0B\"'");
        if (!isset($_ENV[$name])) $_ENV[$name] = $value;
        if (!isset($_SERVER[$name])) $_SERVER[$name] = $value;
        putenv("$name=$value");
    }
}

function env($key, $default = null) {
    if (isset($_ENV[$key])) return $_ENV[$key];
    if (isset($_SERVER[$key])) return $_SERVER[$key];
    $value = getenv($key);
    return $value !== false ? $value : $default;
}

return [
    'host'     => env('DB_HOST', '127.0.0.1'),
    'port'     => env('DB_PORT', '3306'),
    'database' => env('DB_NAME', ''),
    'username' => env('DB_USER', ''),
    'password' => env('DB_PASS', ''),
];
