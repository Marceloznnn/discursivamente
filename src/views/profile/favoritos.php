<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DISCURSIVAMENTE - Favoritos</title>
    <link rel="stylesheet" href="/assets/css/style.css">
    <link rel="stylesheet" href="/assets/css/components/cards.css">
    <link rel="stylesheet" href="/assets/css/pages/perfil.css">
</head>
<body>
    <?php include_once '../partials/header.php'; ?>
    
    <main class="container">
        <div class="profile-container">
            <div class="section-header">
                <h1>Meus Favoritos</h1>
                <div class="section-actions">
                    <a href="/profile" class="btn btn-outline">Voltar ao Perfil</a>
                </div>
            </div>
            
            <div class="favorites-tabs">
                <a href="#" class="tab active">Comunidades</a>
                <a href="#" class="tab">Discussões</a>
                <a href="#" class="tab">Artigos</a>
            </div>
            
            <div class="favorites-container">
                <?php if (empty($favorites)): ?>
                    <div class="empty-state">
                        <div class="empty-icon">
                            <i class="icon-favorites-empty"></i>
                        </div>
                        <h3>Nenhum favorito encontrado</h3>
                        <p>Você ainda não adicionou nenhum item aos seus favoritos.</p>
                        <a href="/community" class="btn btn-primary">Explorar Comunidades</a>
                    </div>
                <?php else: ?>
                    <div class="favorites-grid">
                        <?php foreach ($favorites as $favorite): ?>
                            <div class="favorite-card">
                                <div class="favorite-header">
                                    <img src="<?php echo $favorite->image; ?>" alt="<?php echo $favorite->title; ?>">
                                </div>
                                <div class="favorite-content">
                                    <h3><a href="<?php echo $favorite->url; ?>"><?php echo $favorite->title; ?></a></h3>
                                    <p><?php echo $favorite->description; ?></p>
                                </div>
                                <div class="favorite-footer">
                                    <span><?php echo $favorite->type; ?></span>
                                    <span>Adicionado em <?php echo date('d/m/Y', strtotime($favorite->created_at)); ?></span>
                                    <button class="btn-icon remove-favorite" data-id="<?php echo $favorite->id; ?>">
                                        <i class="icon-heart-filled"></i>
                                    </button>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </main>
    
    <?php include_once '../partials/footer.php'; ?>
    <script src="/assets/js/app.js"></script>
    <script src="/assets/js/pages/favorites.js"></script>
</body>
</html>