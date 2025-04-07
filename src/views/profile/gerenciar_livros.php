<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DISCURSIVAMENTE - Gerenciar Conta</title>
    <link rel="stylesheet" href="/assets/css/style.css">
    <link rel="stylesheet" href="/assets/css/components/forms.css">
    <link rel="stylesheet" href="/assets/css/pages/perfil.css">
</head>
<body>
    <?php include_once '../partials/header.php'; ?>
    
    <main class="container">
        <div class="manage-account-container">
            <div class="section-header">
                <h1>Gerenciar Conta</h1>
                <div class="section-actions">
                    <a href="/profile" class="btn btn-outline">Voltar ao Perfil</a>
                </div>
            </div>
            
            <div class="settings-tabs">
                <a href="#" class="tab active">Configurações de Conta</a>
                <a href="#" class="tab">Privacidade</a>
                <a href="#" class="tab">Notificações</a>
                <a href="#" class="tab">Conexões</a>
            </div>
            
            <div class="settings-section">
                <h2>Informações da Conta</h2>
                
                <form action="/profile/update-account" method="POST">
                    <div class="form-group">
                        <label for="email">E-mail</label>
                        <input type="email" id="email" name="email" value="<?php echo $user->email; ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="username">Nome de Usuário</label>
                        <input type="text" id="username" name="username" value="<?php echo $user->username; ?>" required>
                    </div>
                    
                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary">Salvar Alterações</button>
                    </div>
                </form>
            </div>
            
            <div class="settings-section">
                <h2>Alterar Senha</h2>
                
                <form action="/profile/update-password" method="POST">
                    <div class="form-group">
                        <label for="current_password">Senha Atual</label>
                        <input type="password" id="current_password" name="current_password" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="new_password">Nova Senha</label>
                        <input type="password" id="new_password" name="new_password" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="new_password_confirmation">Confirmar Nova Senha</label>
                        <input type="password" id="new_password_confirmation" name="new_password_confirmation" required>
                    </div>
                    
                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary">Alterar Senha</button>
                    </div>
                </form>
            </div>
            
            <div class="settings-section danger-zone">
                <h2>Zona de Perigo</h2>
                
                <div class="danger-actions">
                    <div class="danger-action">
                        <div class="danger-info">
                            <h3>Desativar Conta</h3>
                            <p>Sua conta será desativada temporariamente. Você poderá reativá-la a qualquer momento fazendo login novamente.</p>
                        </div>
                        <button class="btn btn-warning" data-toggle="modal" data-target="deactivate-modal">Desativar Conta</button>
                    </div>
                    
                    <div class="danger-action">
                        <div class="danger-info">
                            <h3>Excluir Conta</h3>
                            <p>Sua conta será excluída permanentemente. Todos os seus dados serão removidos e não poderão ser recuperados.</p>
                        </div>
                        <button class="btn btn-danger" data-toggle="modal" data-target="delete-modal">Excluir Conta</button>
                    </div>
                </div>
            </div>
        </div>
    </main>
    
    <!-- Modal de Desativação de Conta -->
    <div id="deactivate-modal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Desativar Conta</h2>
                <button class="close-modal">&times;</button>
            </div>
            <div class="modal-body">
                <p>Tem certeza que deseja desativar sua conta? Você poderá reativá-la a qualquer momento fazendo login novamente.</p>
                <form action="/profile/deactivate" method="POST">
                    <div class="form-group">
                        <label for="deactivate_password">Digite sua senha para confirmar</label>
                        <input type="password" id="deactivate_password" name="password" required>
                    </div>
                    <div class="form-actions">
                        <button type="button" class="btn btn-outline cancel-modal">Cancelar</button>
                        <button type="submit" class="btn btn-warning">Desativar Conta</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <!-- Modal de Exclusão de Conta -->
    <div id="delete-modal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Excluir Conta</h2>
                <button class="close-modal">&times;</button>
            </div>
            <div class="modal-body">
                <p>Tem certeza que deseja excluir sua conta? Esta ação é irreversível e todos os seus dados serão perdidos permanentemente.</p>
                <form action="/profile/delete" method="POST">
                    <div class="form-group">
                        <label for="delete_password">Digite sua senha para confirmar</label>
                        <input type="password" id="delete_password" name="password" required>
                    </div>
                    <div class="form-actions">
                        <button type="button" class="btn btn-outline cancel-modal">Cancelar</button>
                        <button type="submit" class="btn btn-danger">Excluir Conta</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <?php include_once '../partials/footer.php'; ?>
    <script src="/assets/js/app.js"></script>
    <script src="/assets/js/pages/manage-account.js"></script>
</body>
</html>