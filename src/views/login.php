<?php
// login.php - Localizado, por exemplo, em public/login.php
// Inclua os arquivos de configuração e o modelo de usuário
require_once __DIR__ . '/../../src/config/database.php';
require_once __DIR__ . '/../../src/models/UserModel.php';
require_once BASE_PATH . '/src/views/partials/header.php';
$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = trim($_POST['email'] ?? '');
    $senha = trim($_POST['senha'] ?? '');
    
    if (empty($email) || empty($senha)) {
        $error = "Preencha todos os campos.";
    } else {
        $userModel = new UserModel($pdo); // $pdo deve estar definido no arquivo database.php
        $user = $userModel->getUserByEmail($email);
        
        if ($user && password_verify($senha, $user['password'])) {
            // Login efetuado com sucesso: salva os dados do usuário na sessão
            $_SESSION['user'] = $user;
            header("Location: /perfil");
            exit();
        } else {
            $error = "Email ou senha incorretos.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | Sistema Profissional</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Inclua aqui o CSS que criei para você -->
    <link rel="stylesheet" href="/css/login.css">
</head>
<body>
    <div id="loginContainer" class="container-login">
        <!-- Lado esquerdo - Imagem e mensagem de boas-vindas -->
        <div id="loginImageSide" class="side-image">
            <div id="particlesContainer" class="particles">
                <div id="particle1" class="particle"></div>
                <div id="particle2" class="particle"></div>
                <div id="particle3" class="particle"></div>
            </div>
            <div id="welcomeContent" class="welcome-content">
                <h3>Bem-vindo novamente!</h3>
                <p>É bom ter você de volta. Acesse sua conta para continuar de onde parou. Ainda não tem uma conta?</p>
                <a href="/register" id="registerButton" class="btn-register">Criar Conta</a>
            </div>
        </div>
        
        <!-- Lado direito - Formulário de login -->
        <div id="loginFormSide" class="side-form">
            <h2>Entrar no Sistema</h2>
            
            <?php if (!empty($error)) : ?>
                <div id="errorMessage" class="error-message"><?php echo $error; ?></div>
            <?php endif; ?>
            
            <form action="/login" method="post">
                <div id="emailGroup" class="form-group">
                    <input type="email" name="email" id="emailInput" class="form-control" placeholder="Seu email" required>
                </div>
                <div id="passwordGroup" class="form-group">
                    <input type="password" name="senha" id="passwordInput" class="form-control" placeholder="Sua senha" required>
                </div>
                <button type="submit" id="loginButton" class="btn-login">Fazer Login</button>
            </form>
            <div id="linkRegister" class="link-register">
                Não tem uma conta? <a href="/register">Registre-se</a>
            </div>
        </div>
    </div>
</body>
</html>
