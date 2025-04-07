<?php 
require_once BASE_PATH . '/src/Helpers/Auth.php';

use Helpers\Auth;

$loggedIn = Auth::isLoggedIn();
$profileImage = Auth::getProfileImage();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $title ?? 'Biblioteca Discursivamente'; ?></title>
    <!-- Google Fonts para Roboto -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;600;700&display=swap" rel="stylesheet">
    <!-- CSS do site -->
    <link rel="stylesheet" href="/assets/css/components/footer.css">
    <link rel="stylesheet" href="/assets/css/style.css">
    <link rel="stylesheet" href="/assets/css/components/navigation.css">
    <!-- Font Awesome para ícones -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <!-- Favicon -->
    <link rel="apple-touch-icon" sizes="180x180" href="/assets/images/icons/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="/assets/images/icons/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="/assets/images/icons/favicon-16x16.png">
    <link rel="manifest" href="/manifest.json">
</head>
<body>
    <!-- Header principal do site -->
    <header id="siteHeader" role="banner">
        <div id="headerContainer">
            <!-- Logo e título do site -->
            <div id="siteLogo">
                <a href="/home" id="logoLink" aria-label="Ir para página inicial">
                    <i id="logoIcon" class="fas fa-book-reader" aria-hidden="true"></i>
                    <h1 id="logoTitle">Discursivamente</h1>
                </a>
            </div>

            <!-- Botão do menu mobile -->
            <button id="mobileMenuBtn" aria-label="Abrir menu de navegação" aria-expanded="false" aria-controls="mainNavigation">
                <i id="mobileMenuIcon" class="fas fa-bars" aria-hidden="true"></i>
            </button>

            <!-- Navegação principal -->
            <nav id="mainNavigation" role="navigation" aria-label="Menu principal">
                <ul id="navList">
                    <li id="navHomeItem">
                        <a href="/home" id="navHomeLink">
                            <i class="fas fa-home" aria-hidden="true"></i> Home
                        </a>
                    </li>
                    <li id="navCommunityItem">
                        <a href="/comunidade/comunicacao" id="navCommunityLink">
                            <i class="fas fa-comments" aria-hidden="true"></i> Comunidade
                        </a>
                    </li>
                    <li id="navLibraryItem">
                        <a href="/biblioteca" id="navLibraryLink">
                            <i class="fas fa-book" aria-hidden="true"></i> Biblioteca
                        </a>
                    </li>
                    <li id="navAppointmentsItem">
                        <a href="/compromissos" id="navAppointmentsLink">
                            <i class="fas fa-calendar-check" aria-hidden="true"></i> Compromissos
                        </a>
                    </li>
                    <li id="navAboutItem">
                        <a href="/quem-somos" id="navAboutLink">
                            <i class="fas fa-users" aria-hidden="true"></i> Quem Somos
                        </a>
                    </li>
                    
                    <?php if ($loggedIn): ?>
                        <!-- Exibe o menu do usuário caso esteja logado -->
                        <li id="userDropdownItem">
                            <a href="#" id="dropdownToggle" aria-haspopup="true" aria-expanded="false">
                                <div id="userProfile">
                                    <img src="<?php echo $profileImage; ?>" alt="Imagem do Usuário" id="userImage">
                                    <span id="userProfileText">Meu Perfil</span>
                                </div>
                                <i id="dropdownIcon" class="fas fa-chevron-down" aria-hidden="true"></i>
                            </a>
                            <ul id="dropdownMenu" aria-label="Menu do usuário">
                                <li id="viewProfileItem">
                                    <a href="/perfil" id="viewProfileLink">
                                        <i class="fas fa-id-card" aria-hidden="true"></i> Ver Perfil
                                    </a>
                                </li>
                                <li id="myReservationsItem">
                                    <a href="/minhas-reservas" id="myReservationsLink">
                                        <i class="fas fa-bookmark" aria-hidden="true"></i> Minhas Reservas
                                    </a>
                                </li>
                                <li id="historyItem">
                                    <a href="/historico" id="historyLink">
                                        <i class="fas fa-history" aria-hidden="true"></i> Histórico
                                    </a>
                                </li>
                                <li id="settingsItem">
                                    <a href="/configuracoes" id="settingsLink">
                                        <i class="fas fa-cog" aria-hidden="true"></i> Configurações
                                    </a>
                                </li>
                                <li id="divider" role="separator"></li>
                                <li id="logoutItem">
                                    <a href="/logout" id="logoutLink">
                                        <i class="fas fa-sign-out-alt" aria-hidden="true"></i> Sair
                                    </a>
                                </li>
                            </ul>
                        </li>
                    <?php else: ?>
                        <!-- Exibe o link de login se o usuário não estiver autenticado -->
                        <li id="loginItem">
                            <a href="/login" id="loginLink">
                                <i class="fas fa-sign-in-alt" aria-hidden="true"></i> Fazer Login
                            </a>
                        </li>
                    <?php endif; ?>
                </ul>
            </nav>
        </div>
    </header>
    <script src="/assets/js/components/header.js"></script>
</body>
</html>
