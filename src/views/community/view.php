<?php
// Valida e inicializa as variáveis essenciais

if (!isset($community) || !is_object($community)) {
    echo "Erro: Dados da comunidade não encontrados.";
    exit;
}

if (!isset($currentUser) || !is_object($currentUser)) {
    // Caso o usuário atual não esteja definido, define um objeto padrão
    $currentUser = (object)[
        'avatar' => '/assets/images/default-avatar.png'
    ];
}

if (!isset($isMember)) {
    $isMember = false;
}

if (!isset($discussions) || !is_array($discussions)) {
    $discussions = [];
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DISCURSIVAMENTE - <?php echo htmlspecialchars($community->name ?? 'Comunidade'); ?></title>
    <link rel="stylesheet" href="/assets/css/style.css">
    <link rel="stylesheet" href="/assets/css/components/cards.css">
    <link rel="stylesheet" href="/assets/css/pages/comunidade.css">
</head>
<body>
    <?php include_once '../partials/header.php'; ?>
    
    <main class="container">
        <div class="community-header">
            <div class="community-cover" style="background-image: url('<?php echo htmlspecialchars($community->cover_image ?? '/assets/images/default-cover.jpg'); ?>')">
                <div class="community-info">
                    <h1><?php echo htmlspecialchars($community->name ?? 'Comunidade'); ?></h1>
                    <p><?php echo htmlspecialchars($community->description ?? ''); ?></p>
                    <div class="community-stats">
                        <span><?php echo isset($community->members_count) ? htmlspecialchars($community->members_count) : '0'; ?> membros</span>
                        <span><?php echo isset($community->posts_count) ? htmlspecialchars($community->posts_count) : '0'; ?> posts</span>
                        <span>Criada em <?php echo isset($community->created_at) ? date('d/m/Y', strtotime($community->created_at)) : 'N/A'; ?></span>
                    </div>
                </div>
            </div>
            
            <div class="community-actions">
                <?php if (!$isMember): ?>
                    <button class="btn btn-primary">Participar</button>
                <?php else: ?>
                    <button class="btn btn-secondary">Sair da Comunidade</button>
                <?php endif; ?>
                <button class="btn btn-outline">Compartilhar</button>
            </div>
        </div>
        
        <div class="community-content">
            <div class="community-tabs">
                <a href="#" class="tab active">Discussões</a>
                <a href="#" class="tab">Fóruns</a>
                <a href="#" class="tab">Eventos</a>
                <a href="#" class="tab">Membros</a>
                <a href="#" class="tab">Sobre</a>
            </div>
            
            <div class="community-discussions">
                <div class="create-post">
                    <img src="<?php echo htmlspecialchars($currentUser->avatar); ?>" alt="Avatar" class="user-avatar">
                    <div class="post-input">
                        <textarea placeholder="Inicie uma discussão..."></textarea>
                        <button class="btn btn-primary">Publicar</button>
                    </div>
                </div>
                
                <div class="discussions-list">
                    <?php if (!empty($discussions)): ?>
                        <?php foreach ($discussions as $discussion): ?>
                            <div class="discussion-card">
                                <div class="discussion-header">
                                    <img src="<?php echo htmlspecialchars($discussion->user->avatar ?? '/assets/images/default-avatar.png'); ?>" alt="Avatar" class="user-avatar">
                                    <div class="user-info">
                                        <h4><?php echo htmlspecialchars($discussion->user->name ?? 'Desconhecido'); ?></h4>
                                        <span><?php echo isset($discussion->created_at) ? date('d/m/Y H:i', strtotime($discussion->created_at)) : ''; ?></span>
                                    </div>
                                </div>
                                <div class="discussion-content">
                                    <h3><?php echo htmlspecialchars($discussion->title ?? ''); ?></h3>
                                    <p><?php echo htmlspecialchars($discussion->content ?? ''); ?></p>
                                </div>
                                <div class="discussion-actions">
                                    <button class="action-btn"><i class="icon-like"></i> <?php echo isset($discussion->likes_count) ? htmlspecialchars($discussion->likes_count) : '0'; ?></button>
                                    <button class="action-btn"><i class="icon-comment"></i> <?php echo isset($discussion->comments_count) ? htmlspecialchars($discussion->comments_count) : '0'; ?></button>
                                    <button class="action-btn"><i class="icon-share"></i> Compartilhar</button>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p>Nenhuma discussão encontrada.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </main>
    
    <?php include_once '../partials/footer.php'; ?>
    <script src="/assets/js/app.js"></script>
    <script src="/assets/js/pages/community-view.js"></script>
</body>
</html>
