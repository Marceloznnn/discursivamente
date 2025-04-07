<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DISCURSIVAMENTE - Redefinir Senha</title>
    <link rel="stylesheet" href="/assets/css/style.css">
    <link rel="stylesheet" href="/assets/css/components/forms.css">
    <link rel="stylesheet" href="/assets/css/pages/perfil.css">
</head>
<body>
    <?php include_once '../partials/header.php'; ?>
    
    <main class="container">
        <div class="reset-password-container">
            <h1>Redefinir Senha</h1>
            
            <?php if ($invalidToken): ?>
                <div class="alert alert-danger">
                    <p>O token de redefinição de senha é inválido ou expirou. Por favor, solicite uma nova redefinição de senha.</p>
                    <a href="/auth/forgot-password" class="btn btn-primary mt-3">Solicitar Nova Senha</a>
                </div>
            <?php elseif ($passwordReset): ?>
                <div class="alert alert-success">
                    <p>Sua senha foi redefinida com sucesso! Agora você pode fazer login com sua nova senha.</p>
                    <a href="/auth/login" class="btn btn-primary mt-3">Fazer Login</a>
                </div>
            <?php else: ?>
                <div class="reset-form">
                    <form action="/auth/reset-password" method="POST">
                        <input type="hidden" name="token" value="<?php echo $token; ?>">
                        <input type="hidden" name="email" value="<?php echo $email; ?>">
                        
                        <div class="form-group">
                            <label for="password">Nova Senha</label>
                            <input type="password" id="password" name="password" required>
                            <div class="password-requirements">
                                <p>A senha deve conter:</p>
                                <ul>
                                    <li class="requirement" data-requirement="length">No mínimo 8 caracteres</li>
                                    <li class="requirement" data-requirement="uppercase">Pelo menos uma letra maiúscula</li>
                                    <li class="requirement" data-requirement="lowercase">Pelo menos uma letra minúscula</li>
                                    <li class="requirement" data-requirement="number">Pelo menos um número</li>
                                    <li class="requirement" data-requirement="special">Pelo menos um caractere especial</li>
                                </ul>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label for="password_confirmation">Confirmar Nova Senha</label>
                            <input type="password" id="password_confirmation" name="password_confirmation" required>
                        </div>
                        
                        <div class="form-actions">
                            <button type="submit" class="btn btn-primary">Redefinir Senha</button>
                        </div>
                    </form>
                </div>
            <?php endif; ?>
        </div>
    </main>
    
    <?php include_once '../partials/footer.php'; ?>
    <script src="/assets/js/app.js"></script>
    <script src="/assets/js/pages/reset-password.js"></script>
</body>
</html>