<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DISCURSIVAMENTE - Histórico</title>
    <link rel="stylesheet" href="/assets/css/style.css">
    <link rel="stylesheet" href="/assets/css/components/cards.css">
    <link rel="stylesheet" href="/assets/css/pages/perfil.css">
</head>
<body>
    <?php include_once '../partials/header.php'; ?>
    
    <main class="container">
        <div class="history-container">
            <div class="section-header">
                <h1>Meu Histórico</h1>
                <div class="section-actions">
                    <a href="/profile" class="btn btn-outline">Voltar ao Perfil</a>
                    <button class="btn btn-warning" data-toggle="modal" data-target="clear-history-modal">
                        <i class="icon-trash"></i> Limpar Histórico
                    </button>
                </div>
            </div>
            
            <div class="history-filters">
                <div class="filter-group">
                    <label for="history-type">Filtrar por tipo:</label>
                    <select id="history-type" class="filter-select">
                        <option value="all">Todos</option>
                        <option value="forum">Fóruns</option>
                        <option value="community">Comunidades</option>
                        <option value="article">Artigos</option>
                    </select>
                </div>
                
                <div class="filter-group">
                    <label for="history-date">Filtrar por data:</label>
                    <select id="history-date" class="filter-select">
                        <option value="all">Todo o período</option>
                        <option value="today">Hoje</option>
                        <option value="week">Esta semana</option>
                        <option value="month">Este mês</option>
                    </select>
                </div>
            </div>
            
            <div class="history-list">
                <?php if (empty($history)): ?>
                    <div class="empty-state">
                        <div class="empty-icon">
                            <i class="icon-history-empty"></i>
                        </div>
                        <h3>Histórico vazio</h3>
                        <p>Você ainda não visitou nenhuma página ou conteúdo.</p>
                        <a href="/community" class="btn btn-primary">Explorar Comunidades</a>
                    </div>
                <?php else: ?>
                    <?php foreach ($history as $date => $items): ?>
                        <div class="history-date-group">
                            <h3 class="history-date"><?php echo $date; ?></h3>
                            
                            <?php foreach ($items as $item): ?>
                                <div class="history-item" data-type="<?php echo $item->type; ?>">
                                    <div class="history-icon">
                                        <i class="icon-<?php echo $item->type; ?>"></i>
                                    </div>
                                    <div class="history-content">
                                        <h4><a href="<?php echo $item->url; ?>"><?php echo $item->title; ?></a></h4>
                                        <div class="history-meta">
                                            <span class="history-type"><?php echo $item->type_name; ?></span>
                                            <span class="history-time"><?php echo $item->time; ?></span>
                                        </div>
                                    </div>
                                    <div class="history-actions">
                                        <button class="btn-icon remove-history" data-id="<?php echo $item->id; ?>">
                                            <i class="icon-times"></i>
                                        </button>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </main>
    
    <!-- Modal de Limpar Histórico -->
    <div id="clear-history-modal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Limpar Histórico</h2>
                <button class="close-modal">&times;</button>
            </div>
            <div class="modal-body">
                <p>Tem certeza que deseja limpar todo o seu histórico? Esta ação não pode ser desfeita.</p>
                <form action="/profile/clear-history" method="POST">
                    <div class="form-actions">
                        <button type="button" class="btn btn-outline cancel-modal">Cancelar</button>
                        <button type="submit" class="btn btn-warning">Limpar Histórico</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <?php include_once '../partials/footer.php'; ?>
    <script src="/assets/js/app.js"></script>
    <script src="/assets/js/pages/history.js"></script>
</body>
</html>