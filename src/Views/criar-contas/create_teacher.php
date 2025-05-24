<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

// --- CREDENCIAIS DIRETAS DO BANCO ---
$host = '127.0.0.1';
$port = '3306';
$db   = 'discursivamente_db';
$user = 'root';
$pass = ''; // coloque sua senha se houver

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

if (php_sapi_name() === 'cli') {
    $name = $argv[1] ?? null;
    $email = $argv[2] ?? null;
    $password = $argv[3] ?? null;
    $type = $argv[4] ?? null;

    if (!$name || !$email || !$password || !$type) {
        echo "Por favor, forneça as informações do usuário:\n";
        if (!$name) {
            echo "Nome: ";
            $name = trim(fgets(STDIN));
        }
        if (!$email) {
            echo "Email: ";
            $email = trim(fgets(STDIN));
        }
        if (!$password) {
            echo "Senha: ";
            $password = trim(fgets(STDIN));
        }
        if (!$type || !in_array($type, $validTypes)) {
            echo "Tipo (admin, instituicao, teacher, student): ";
            $type = trim(fgets(STDIN));
        }
    }
} else {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $name = $_POST['name'] ?? '';
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';
        $type = $_POST['type'] ?? '';
    } else {
        ?>
        <!DOCTYPE html>
        <html>
        <head>
            <title>Criar Usuário</title>
            <style>
                body { font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; padding: 20px; }
                .form-group { margin-bottom: 15px; }
                label { display: block; margin-bottom: 5px; }
                input, select { width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px; }
                button { background: #4CAF50; color: white; padding: 10px 15px; border: none; border-radius: 4px; cursor: pointer; }
            </style>
        </head>
        <body>
            <h1>Criar Novo Usuário</h1>
            <form method="post" action="">
                <div class="form-group">
                    <label for="name">Nome:</label>
                    <input type="text" id="name" name="name" required>
                </div>
                <div class="form-group">
                    <label for="email">Email:</label>
                    <input type="email" id="email" name="email" required>
                </div>
                <div class="form-group">
                    <label for="password">Senha:</label>
                    <input type="password" id="password" name="password" required>
                </div>
                <div class="form-group">
                    <label for="type">Tipo de Usuário:</label>
                    <select name="type" id="type" required>
                        <option value="">Selecione...</option>
                        <option value="admin">Admin</option>
                        <option value="instituicao">Instituição</option>
                        <option value="teacher">Professor</option>
                        <option value="student">Estudante</option>
                    </select>
                </div>
                <button type="submit">Criar Usuário</button>
            </form>
        </body>
        </html>
        <?php
        exit;
    }
}

if (empty($name) || empty($email) || empty($password) || empty($type)) {
    die("Erro: Todos os campos são obrigatórios.\n");
}

if (!in_array($type, $validTypes)) {
    die("Erro: Tipo de usuário inválido.\n");
}

// Verifica se o e-mail já existe
$stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
$stmt->execute([$email]);
if ($stmt->fetch()) {
    die("Erro: Email já está em uso.\n");
}

// Cria novo usuário
$hash = password_hash($password, PASSWORD_BCRYPT);
$stmt = $pdo->prepare("INSERT INTO users (name, email, password, type) VALUES (?, ?, ?, ?)");
$stmt->execute([$name, $email, $hash, $type]);

if (php_sapi_name() === 'cli') {
    echo "Usuário criado com sucesso!\n";
    echo "Nome: $name\n";
    echo "Email: $email\n";
    echo "Tipo: $type\n";
} else {
    echo "<div style='padding: 20px; font-family: Arial;'>
            <h2>Usuário criado com sucesso!</h2>
            <p><strong>Nome:</strong> $name</p>
            <p><strong>Email:</strong> $email</p>
            <p><strong>Tipo:</strong> $type</p>
            <p><a href='login.php'>Ir para login</a></p>
          </div>";
}
