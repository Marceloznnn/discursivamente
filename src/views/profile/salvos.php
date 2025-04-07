<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DISCURSIVAMENTE - Itens Salvos</title>
    <link rel="stylesheet" href="/assets/css/style.css">
    <link rel="stylesheet" href="/assets/css/components/cards.css">
    <link rel="stylesheet" href="/assets/css/pages/perfil.css">
</head>
<body>
    <?php include_once '../partials/header.php'; ?>
    
    <main class="container">
        <div class="saved-items-container">
            <div class="section-header">
                <h1>Itens Salvos</h1>
                <div class="section-actions">
                    <a href="/profile" class="btn btn-outline">Voltar ao Perfil</a>
                </div>
            </div>
            
            <div class="saved-tabs">
                <a href="#" class="tab active">Todos</a>
                <a href="#" class="tab">Discussões</a>
                <a href="#" class="tab">Artigos</a>
                <a href="#" class="tab">Links</a>
            </div>
            
            <div class="saved-items-search">
                <div class="search-container">
                    <input type="text" class="search-input" placeholder="Buscar nos itens salvos...">
                    <button class="search-btn">
                        <i class="icon-search"></i>
                    </button>
                </div>
            </div>
            
            <div class="saved-items-list">
                <?php if (empty($savedItems)): ?>
                    <div class="empty-state">
                        <div class="empty-icon">
                            <i class="icon-saved-empty"></i>
                        </div>
                        <h3>Nenhum item salvo</h3>
                        <p>Você ainda não salvou nenhum conteúdo. Ao encontrar algo interessante, clique no ícone de marcador para salvar para mais tarde.</p>
                        <a href="/community" class="btn btn-primary">Explorar Comunidades</a>
                    </div>
                <?php else: ?>
                    <?php foreach ($savedItems as $item): ?>
                        <div class="saved-item" data-type="<?php echo $item->type; ?>">
                            <div class="saved-item-type">
                                <i class="icon-<?php echo $item->type; ?>"></i>
                            </div>
                            <div class="saved-item-content">
                                <h3><a href="<?php echo $item->url; ?>"><?php echo $item->title; ?></a></h3>
                                <p><?php echo $item->excerpt; ?></p>
                                <div class="saved-item-meta">
                                    <span><?php echo $item->source; ?></span>
                                    <span>Salvo em <?php echo date('d/m/Y', strtotime($item->saved_at)); ?></span>
                                </div>
                            </div>
                            <div class="saved-item-actions">
                                <button class="btn-icon remove-saved" data-id="<?php echo $item->id; ?>">
                                    <i class="icon-bookmark-filled"></i>
                                </button>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </main>
    
    <?php include_once '../partials/footer.php'; ?>
    <script src="/assets/js/app.js"></script>
    <script src="/assets/js/pages/saved.js"></script>
</body>
</html>