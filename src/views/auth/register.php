<?php
// Inicia a sessão e define o título da página
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$pageTitle = "Registro - DISCURSIVAMENTE";
require_once BASE_PATH . '/src/views/partials/header.php';
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title><?php echo htmlspecialchars($pageTitle); ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css">
</head>
<body>
<div class="container py-5">
    <h2>Registro</h2>
    <?php if (isset($_SESSION['mensagem'])): ?>
        <div class="alert alert-<?php echo $_SESSION['tipo_mensagem'] ?? 'info'; ?>">
            <?php 
                echo htmlspecialchars($_SESSION['mensagem']); 
                unset($_SESSION['mensagem']);
            ?>
        </div>
    <?php endif; ?>
    <form action="/register" method="POST">
        <!-- Token CSRF -->
        <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
        
        <div class="mb-3">
            <label for="name" class="form-label">Nome</label>
            <input type="text" class="form-control" name="name" required>
        </div>
        
        <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input type="email" class="form-control" name="email" required>
        </div>
        
        <div class="mb-3">
            <label for="password" class="form-label">Senha</label>
            <input type="password" class="form-control" name="password" required>
        </div>
        
        <div class="mb-3">
            <label for="confirm_password" class="form-label">Confirmar Senha</label>
            <input type="password" class="form-control" name="confirm_password" required>
        </div>
        
        <button type="submit" class="btn btn-primary">Registrar</button>
    </form>
</div>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
</body>
</html>
