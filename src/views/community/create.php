<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DISCURSIVAMENTE - Criar Comunidade</title>
    <link rel="stylesheet" href="/assets/css/style.css">
    <link rel="stylesheet" href="/assets/css/components/forms.css">
    <link rel="stylesheet" href="/assets/css/pages/comunidade.css">
</head>
<body>
    <?php include_once '../partials/header.php'; ?>
    
    <main class="container">
        <div class="create-community-container">
            <h1>Criar Nova Comunidade</h1>
            <form action="/community/store" method="POST" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="name">Nome da Comunidade</label>
                    <input type="text" id="name" name="name" required>
                </div>
                
                <div class="form-group">
                    <label for="description">Descrição</label>
                    <textarea id="description" name="description" rows="4" required></textarea>
                </div>
                
                <div class="form-group">
                    <label for="cover_image">Imagem de Capa</label>
                    <input type="file" id="cover_image" name="cover_image">
                </div>
                
                <div class="form-group">
                    <label for="category">Categoria</label>
                    <select id="category" name="category_id" required>
                        <option value="">Selecione uma categoria</option>
                        <?php foreach ($categories as $category): ?>
                            <option value="<?php echo $category->id; ?>"><?php echo $category->name; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="privacy">Privacidade</label>
                    <select id="privacy" name="privacy" required>
                        <option value="public">Pública - Qualquer um pode ver e participar</option>
                        <option value="restricted">Restrita - Qualquer um pode ver, mas somente membros podem participar</option>
                        <option value="private">Privada - Somente membros podem ver e participar</option>
                    </select>
                </div>
                
                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">Criar Comunidade</button>
                    <a href="/community" class="btn btn-outline">Cancelar</a>
                </div>
            </form>
        </div>
    </main>
    
    <?php include_once '../partials/footer.php'; ?>
    <script src="/assets/js/app.js"></script>
</body>
</html>