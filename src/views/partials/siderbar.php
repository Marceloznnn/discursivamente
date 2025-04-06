<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Informações - Perfil do Usuário</title>
    <!-- Font Awesome para ícones -->
     <link rel="stylesheet" href="/css/perfil.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
</head>
<body>
    <!-- Header principal fixo (já existente) -->
    <header class="header-principal">
        <h1>Biblioteca Virtual</h1>
    </header>
    
    <!-- Container que envolve sidebar e conteúdo com borda -->
    <div class="container-principal">
        <!-- Sidebar (header lateral) -->
        <aside class="sidebar">
            <div class="sidebar-header">
                <h2>Menu de Navegação</h2>
            </div>
            
            <ul class="menu-navigation">
                <li>
                    <a href="#">
                        <i class="fas fa-user"></i> Perfil do Usuário
                    </a>
                </li>
                <li>
                    <a href="#" class="active">
                        <i class="fas fa-edit"></i> Editar Informações
                    </a>
                </li>
                <li>
                    <a href="#">
                        <i class="fas fa-book"></i> Gerenciar Livros
                    </a>
                </li>
                <li>
                    <a href="#">
                        <i class="fas fa-star"></i> Avaliações e Feedbacks
                    </a>
                </li>
                <li>
                    <a href="#">
                        <i class="fas fa-history"></i> Histórico de Leitura
                    </a>
                </li>
                <li>
                    <a href="#">
                        <i class="fas fa-cog"></i> Configurações
                    </a>
                </li>
            </ul>
            
            <div class="logout-button">
                <button>
                    <i class="fas fa-sign-out-alt"></i> Sair
                </button>
            </div>
        </aside>
        