<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

// --- CREDENCIAIS DO BANCO ---
$host = '127.0.0.1';
$port = '3306';
$db   = 'discursivamente_db';
$user = 'root';
$pass = ''; // sua senha, se houver

try {
    $pdo = new PDO(
        "mysql:host=$host;port=$port;dbname=$db;charset=utf8mb4",
        $user,
        $pass,
        [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]
    );
} catch (PDOException $e) {
    die("Erro na conexão com o banco: " . $e->getMessage());
}

$validTypes = ['admin', 'instituicao', 'teacher', 'student'];

function process($pdo, $identifier, $type, $validTypes) {
    if (!in_array($type, $validTypes)) {
        exit("Erro: Tipo de usuário inválido.\n");
    }

    // Verifica se é ID ou Email
    if (is_numeric($identifier)) {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
    } else {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    }

    $stmt->execute([$identifier]);
    $user = $stmt->fetch();

    if (!$user) {
        exit("Erro: Usuário não encontrado.\n");
    }

    $update = $pdo->prepare("UPDATE users SET type = ? WHERE id = ?");
    $update->execute([$type, $user['id']]);

    return $user;
}

if (php_sapi_name() === 'cli') {
    // MODO TERMINAL
    $identifier = $argv[1] ?? null;
    $type = $argv[2] ?? null;

    if (!$identifier) {
        echo "Digite o ID ou Email do usuário: ";
        $identifier = trim(fgets(STDIN));
    }
    if (!$type) {
        echo "Digite o novo tipo (admin, instituicao, teacher, student): ";
        $type = trim(fgets(STDIN));
    }

    $user = process($pdo, $identifier, $type, $validTypes);

    echo "✅ Tipo alterado com sucesso!\n";
    echo "Nome: {$user['name']}\n";
    echo "Email: {$user['email']}\n";
    echo "Novo Tipo: {$type}\n";

} else {
    // MODO WEB
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $identifier = $_POST['identifier'] ?? '';
        $type = $_POST['type'] ?? '';

        if (empty($identifier) || empty($type)) {
            die("Erro: Todos os campos são obrigatórios.");
        }

        $user = process($pdo, $identifier, $type, $validTypes);

        echo "<div style='padding: 20px; font-family: Arial;'>
                <h2>✅ Tipo de usuário alterado com sucesso!</h2>
                <p><strong>Nome:</strong> {$user['name']}</p>
                <p><strong>Email:</strong> {$user['email']}</p>
                <p><strong>Novo Tipo:</strong> $type</p>
                <p><a href='javascript:history.back()'>Voltar</a></p>
              </div>";
        exit;
    }
    ?>
    <!DOCTYPE html>
    <html lang="pt-br">
    <head>
        <meta charset="UTF-8">
        <title>Alterar Tipo de Usuário</title>
        <style>
            body { font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; padding: 20px; }
            h1 { color: #800020; }
            .form-group { margin-bottom: 15px; }
            label { display: block; margin-bottom: 5px; }
            input, select { width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px; }
            button { background: #800020; color: white; padding: 10px 15px; border: none; border-radius: 4px; cursor: pointer; }
            button:hover { background: #a52a2a; }
        </style>
    </head>
    <body>

    <h1>Alterar Tipo de Usuário</h1>
    <form method="post" action="">
        <div class="form-group">
            <label for="identifier">ID ou Email do Usuário:</label>
            <input type="text" id="identifier" name="identifier" required>
        </div>
        <div class="form-group">
            <label for="type">Novo Tipo:</label>
            <select name="type" id="type" required>
                <option value="">Selecione...</option>
                <option value="admin">Admin</option>
                <option value="instituicao">Instituição</option>
                <option value="teacher">Professor</option>
                <option value="student">Estudante</option>
            </select>
        </div>
        <button type="submit">Alterar Tipo</button>
    </form>

    </body>
    </html>
    <?php
}
