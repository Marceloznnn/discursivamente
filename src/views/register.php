<?php require_once BASE_PATH . '/src/views/partials/header.php'; ?>
<link rel="stylesheet" href="/css/register.css">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<main>
    <section>
        <!-- Lado do formulário -->
        <div class="form-side">
            <h2>Registro de Usuário</h2>
            <?php if(isset($error)) { echo "<p style='color:red;'>$error</p>"; } ?>
            <form action="/register" method="post">
                <div class="input-group">
                    <input type="text" name="nome" id="nome" required>
                    <label for="nome">Nome</label>
                </div>
                
                <div class="input-group">
                    <input type="email" name="email" id="email" required>
                    <label for="email">Email</label>
                </div>
                
                <div class="input-group">
                    <input type="password" name="senha" id="senha" required>
                    <label for="senha">Senha</label>
                </div>
                
                <div class="input-group">
                    <input type="password" name="confirm_senha" id="confirm_senha" required>
                    <label for="confirm_senha">Confirme a Senha</label>
                </div>
                
                <input type="submit" value="Criar Conta">
            </form>
            <p class="form-footer">Já possui uma conta? <a href="/login">Faça login aqui</a>.</p>
        </div>
        
        <!-- Lado da imagem e boas-vindas -->
        <div class="image-side">
            <div class="particles">
                <div class="particle"></div>
                <div class="particle"></div>
                <div class="particle"></div>
            </div>
            <div class="welcome-content">
                <h3>Bem-vindo à nossa plataforma!</h3>
                <p>Entre para uma comunidade de usuários e desfrute de todos os benefícios que temos para oferecer. Já tem uma conta?</p>
                <a href="/login" class="login-btn">Fazer Login</a>
            </div>
        </div>
    </section>
</main>