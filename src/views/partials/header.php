<?php 
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$loggedIn = isset($_SESSION['user']) ? true : false;

// Verifica se existe a foto do usuário e se ela não está vazia; caso contrário, define a foto padrão.
$profileImage = (isset($_SESSION['user']['profileImage']) && !empty($_SESSION['user']['profileImage']))
    ? $_SESSION['user']['profileImage']
    : '/images/default.png';

?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $title ?? 'Biblioteca Discursivamente'; ?></title>
    <!-- Adicionado Google Fonts para Roboto -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <!-- Link para o CSS unificado -->
    <link rel="stylesheet" href="/css/patials.css">
    <link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="/favicon-16x16.png">
    <link rel="manifest" href="/site.webmanifest">
</head>
<body>
    <!-- Header principal do site -->
    <header class="site-header" role="banner">
        <div class="container header-container">
            <!-- Logo e título do site -->
            <div class="logo">
                <a href="/home" aria-label="Ir para página inicial">
                    <i class="fas fa-book-reader" aria-hidden="true"></i>
                    <h1>Discursivamente</h1>
                </a>
            </div>

            <!-- Botão do menu mobile -->
            <button class="mobile-menu-btn" id="mobileMenuBtn" aria-label="Abrir menu de navegação" aria-expanded="false" aria-controls="mainNav">
                <i class="fas fa-bars" aria-hidden="true"></i>
            </button>

            <!-- Navegação principal -->
            <nav id="mainNav" class="main-nav" role="navigation" aria-label="Menu principal">
                <ul class="nav-list">
                    <li><a href="/home" class="nav-link" aria-current="page"><i class="fas fa-home" aria-hidden="true"></i> Home</a></li>
                    <li><a href="/comunidade/comunicacao" class="nav-link"><i class="fas fa-comments" aria-hidden="true"></i> Comunidade</a></li>
                    <li><a href="/biblioteca" class="nav-link"><i class="fas fa-book" aria-hidden="true"></i> Biblioteca</a></li>
                    <li><a href="/compromissos" class="nav-link"><i class="fas fa-calendar-check" aria-hidden="true"></i> Compromissos</a></li>
                    <li><a href="/quem-somos" class="nav-link"><i class="fas fa-users" aria-hidden="true"></i> Quem Somos</a></li>
                    
                    <?php if ($loggedIn): ?>
                        <li class="dropdown">
                            <a href="#" class="nav-link dropdown-toggle" aria-haspopup="true" aria-expanded="false">
                                <div class="user-profile">
                                    <img src="<?php echo $profileImage; ?>" alt="Imagem do Usuário" class="user-img">
                                    <span>Meu Perfil</span>
                                </div>
                                <i class="fas fa-chevron-down" aria-hidden="true"></i>
                            </a>
                            <ul class="dropdown-menu" aria-label="Menu do usuário">
                                <li><a href="/perfil"><i class="fas fa-id-card" aria-hidden="true"></i> Ver Perfil</a></li>
                                <li><a href="/minhas-reservas"><i class="fas fa-bookmark" aria-hidden="true"></i> Minhas Reservas</a></li>
                                <li><a href="/historico"><i class="fas fa-history" aria-hidden="true"></i> Histórico</a></li>
                                <li><a href="/configuracoes"><i class="fas fa-cog" aria-hidden="true"></i> Configurações</a></li>
                                <li class="divider" role="separator"></li>
                                <li><a href="/logout" class="logout-btn"><i class="fas fa-sign-out-alt" aria-hidden="true"></i> Sair</a></li>
                            </ul>
                        </li>
                    <?php else: ?>
                        <li>
                            <a href="/login" class="nav-link login-btn">
                                <i class="fas fa-sign-in-alt" aria-hidden="true"></i> Fazer Login
                            </a>
                        </li>
                    <?php endif; ?>
                </ul>
            </nav>
        </div>
    </header>
    
    <!-- Script para funcionalidades do header -->
    <script src="/js/header.js"></script>
</body>
</html>
