<?php
// Verifica se as variáveis obrigatórias foram definidas e são objetos/arrays conforme esperado
if (!isset($thread) || !is_object($thread)) {
    echo "Erro: Dados da thread não encontrados.";
    exit;
}
if (!isset($community) || !is_object($community)) {
    echo "Erro: Dados da comunidade não encontrados.";
    exit;
}
if (!isset($forum) || !is_object($forum)) {
    echo "Erro: Dados do fórum não encontrados.";
    exit;
}
// Se não houver respostas, inicializa como array vazio
if (!isset($replies) || !is_array($replies)) {
    $replies = [];
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DISCURSIVAMENTE - <?php echo htmlspecialchars($thread->title); ?></title>
    <link rel="stylesheet" href="/assets/css/style.css">
    <link rel="stylesheet" href="/assets/css/components/cards.css">
    <link rel="stylesheet" href="/assets/css/pages/forum.css">
</head>
<body>
    <?php include_once '../../partials/header.php'; ?>
    
    <main class="container">
        <div class="breadcrumb">
            <a href="/community">Comunidades</a> &gt;
            <a href="/community/<?php echo htmlspecialchars($community->id); ?>"><?php echo htmlspecialchars($community->name); ?></a> &gt;
            <a href="/community/<?php echo htmlspecialchars($community->id); ?>/forum/<?php echo htmlspecialchars($forum->id); ?>"><?php echo htmlspecialchars($forum->title); ?></a> &gt;
            <span><?php echo htmlspecialchars($thread->title); ?></span>
        </div>
        
        <div class="thread-container">
            <div class="thread-header">
                <h1><?php echo htmlspecialchars($thread->title); ?></h1>
                <div class="thread-meta">
                    <span>Iniciado por <?php echo (isset($thread->user) && is_object($thread->user)) ? htmlspecialchars($thread->user->name) : 'Desconhecido'; ?></span>
                    <span><?php echo isset($thread->created_at) ? date('d/m/Y H:i', strtotime($thread->created_at)) : ''; ?></span>
                    <span><?php echo isset($thread->views) ? htmlspecialchars($thread->views) : '0'; ?> visualizações</span>
                    <span><?php echo isset($thread->replies_count) ? htmlspecialchars($thread->replies_count) : '0'; ?> respostas</span>
                </div>
            </div>
            
            <div class="posts-container">
                <!-- Post original -->
                <div class="post-item original-post">
                    <div class="post-sidebar">
                        <div class="user-avatar">
                            <img src="<?php echo (isset($thread->user->avatar) && $thread->user->avatar) ? htmlspecialchars($thread->user->avatar) : '/assets/images/default-avatar.png'; ?>" alt="Avatar">
                        </div>
                        <div class="user-info">
                            <h4><?php echo (isset($thread->user->name) && $thread->user->name) ? htmlspecialchars($thread->user->name) : 'Desconhecido'; ?></h4>
                            <div class="user-meta">
                                <span>Membro desde <?php echo isset($thread->user->created_at) ? date('m/Y', strtotime($thread->user->created_at)) : 'N/A'; ?></span>
                                <span><?php echo isset($thread->user->posts_count) ? htmlspecialchars($thread->user->posts_count) : '0'; ?> posts</span>
                            </div>
                        </div>
                    </div>
                    <div class="post-content">
                        <div class="post-body">
                            <?php echo isset($thread->content) ? $thread->content : ''; ?>
                        </div>
                        <div class="post-actions">
                            <div class="post-reactions">
                                <button class="reaction-btn"><i class="icon-like"></i> <?php echo isset($thread->likes_count) ? htmlspecialchars($thread->likes_count) : '0'; ?></button>
                            </div>
                            <div class="post-options">
                                <button class="option-btn"><i class="icon-share"></i> Compartilhar</button>
                                <button class="option-btn"><i class="icon-report"></i> Denunciar</button>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Respostas -->
                <?php if (count($replies) > 0): ?>
                    <?php foreach ($replies as $reply): ?>
                        <div class="post-item reply">
                            <div class="post-sidebar">
                                <div class="user-avatar">
                                    <img src="<?php echo (isset($reply->user->avatar) && $reply->user->avatar) ? htmlspecialchars($reply->user->avatar) : '/assets/images/default-avatar.png'; ?>" alt="Avatar">
                                </div>
                                <div class="user-info">
                                    <h4><?php echo (isset($reply->user->name) && $reply->user->name) ? htmlspecialchars($reply->user->name) : 'Desconhecido'; ?></h4>
                                    <div class="user-meta">
                                        <span>Membro desde <?php echo isset($reply->user->created_at) ? date('m/Y', strtotime($reply->user->created_at)) : 'N/A'; ?></span>
                                        <span><?php echo isset($reply->user->posts_count) ? htmlspecialchars($reply->user->posts_count) : '0'; ?> posts</span>
                                    </div>
                                </div>
                            </div>
                            <div class="post-content">
                                <div class="post-header">
                                    <span><?php echo isset($reply->created_at) ? date('d/m/Y H:i', strtotime($reply->created_at)) : ''; ?></span>
                                </div>
                                <div class="post-body">
                                    <?php echo isset($reply->content) ? $reply->content : ''; ?>
                                </div>
                                <div class="post-actions">
                                    <div class="post-reactions">
                                        <button class="reaction-btn"><i class="icon-like"></i> <?php echo isset($reply->likes_count) ? htmlspecialchars($reply->likes_count) : '0'; ?></button>
                                    </div>
                                    <div class="post-options">
                                        <button class="option-btn"><i class="icon-quote"></i> Citar</button>
                                        <button class="option-btn"><i class="icon-report"></i> Denunciar</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p>Nenhuma resposta encontrada.</p>
                <?php endif; ?>
                
                <!-- Formulário de resposta -->
                <?php if (isset($canReply) && $canReply): ?>
                    <div class="reply-form">
                        <h3>Responder ao Tópico</h3>
                        <form action="/community/<?php echo htmlspecialchars($community->id); ?>/forum/<?php echo htmlspecialchars($forum->id); ?>/thread/<?php echo htmlspecialchars($thread->id); ?>/reply" method="POST">
                            <div class="form-group">
                                <textarea name="content" rows="6" placeholder="Digite sua resposta..."></textarea>
                            </div>
                            <div class="form-actions">
                                <button type="submit" class="btn btn-primary">Enviar Resposta</button>
                            </div>
                        </form>
                    </div>
                <?php else: ?>
                    <div class="login-to-reply">
                        <p>Você precisa estar <a href="/auth/login">logado</a> para responder a este tópico.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </main>
    
    <?php include_once '../../partials/footer.php'; ?>
    <script src="/assets/js/app.js"></script>
    <script src="/assets/js/pages/thread.js"></script>
</body>
</html>
