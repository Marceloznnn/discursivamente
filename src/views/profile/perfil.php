<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perfil do Usuário</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        body {
            background-color: #f5f5f5;
        }
        
        /* Header principal fixo */
        .header-principal {
            background-color: #333;
            color: white;
            height: 60px;
            width: 100%;
            position: fixed;
            top: 0;
            left: 0;
            display: flex;
            align-items: center;
            padding: 0 20px;
            z-index: 100;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
        }
        
        .header-principal h1 {
            font-size: 1.5rem;
        }
        
        /* Container principal que envolve sidebar e conteúdo */
        .container-principal {
            display: flex;
            margin-top: 80px;
            max-width: 1200px;
            margin-left: auto;
            margin-right: auto;
            border: 1px solid #ddd;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
        }
        
        /* Sidebar (novo header lateral) */
        .sidebar {
            width: 250px;
            background-color: #f8f9fa;
            border-right: 1px solid #ddd;
            height: calc(100vh - 100px);
            padding: 20px 0;
        }
        
        .sidebar-header {
            text-align: center;
            padding-bottom: 15px;
            border-bottom: 1px solid #ddd;
            margin-bottom: 20px;
        }
        
        .sidebar-header h2 {
            font-size: 1.2rem;
            color: #444;
        }
        
        .menu-navigation {
            list-style: none;
        }
        
        .menu-navigation li {
            margin-bottom: 5px;
        }
        
        .menu-navigation a {
            display: block;
            padding: 12px 20px;
            color: #333;
            text-decoration: none;
            transition: all 0.3s;
            border-left: 4px solid transparent;
        }
        
        .menu-navigation a:hover, .menu-navigation a.active {
            background-color: #e9ecef;
            border-left: 4px solid #007bff;
            color: #007bff;
        }
        
        .menu-navigation i {
            margin-right: 10px;
            width: 20px;
            text-align: center;
        }
        
        .logout-button {
            margin-top: 30px;
            padding: 0 20px;
        }
        
        .logout-button button {
            width: 100%;
            padding: 12px;
            background-color: #dc3545;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            transition: background-color 0.3s;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .logout-button button:hover {
            background-color: #c82333;
        }
        
        .logout-button i {
            margin-right: 8px;
        }
        
        /* Conteúdo principal */
        .conteudo-principal {
            flex-grow: 1;
            padding: 25px;
            background-color: white;
        }
        
        .titulo-pagina {
            margin-bottom: 25px;
            color: #333;
            border-bottom: 1px solid #eee;
            padding-bottom: 15px;
        }
        
        /* Perfil do usuário */
        .perfil-usuario {
            display: flex;
            align-items: flex-start;
            margin-bottom: 30px;
        }
        
        .foto-perfil {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            object-fit: cover;
            margin-right: 30px;
            border: 4px solid #f8f9fa;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        
        .info-usuario {
            flex-grow: 1;
        }
        
        .info-usuario h2 {
            margin-bottom: 10px;
            font-size: 1.8rem;
            color: #333;
        }
        
        .info-usuario p {
            color: #666;
            line-height: 1.5;
            margin-bottom: 15px;
        }
        
        .editar-btn {
            background-color: #007bff;
            color: white;
            border: none;
            padding: 8px 15px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 0.9rem;
            transition: background-color 0.3s;
        }
        
        .editar-btn:hover {
            background-color: #0069d9;
        }
        
        /* Seções de informação do usuário */
        .secoes-info {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 20px;
        }
        
        .secao-card {
            background-color: #f8f9fa;
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.05);
        }
        
        .secao-card h3 {
            margin-bottom: 15px;
            color: #333;
            font-size: 1.2rem;
            display: flex;
            align-items: center;
        }
        
        .secao-card h3 i {
            margin-right: 10px;
            color: #007bff;
        }
        
        .secao-card ul {
            list-style: none;
            margin-bottom: 15px;
        }
        
        .secao-card li {
            padding: 8px 0;
            border-bottom: 1px solid #eee;
            display: flex;
            justify-content: space-between;
        }
        
        .secao-card .label {
            font-weight: bold;
            color: #555;
        }
        
        .secao-footer {
            display: flex;
            justify-content: flex-end;
            margin-top: 10px;
        }
        
        /* Para ícones usarei Font Awesome */
        .fa {
            display: inline-block;
            width: 16px;
            height: 16px;
            margin-right: 5px;
        }
    </style>
    <!-- Font Awesome para ícones -->
     <link rel="stylesheet" href="/css/perfil.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
</head>
    <!-- Header principal fixo (já existente) -->
    <header class="header-principal">
        <h1>Biblioteca Virtual</h1>
    </header>
    
    <!-- Container que envolve sidebar e conteúdo com borda -->
    <div class="container-principal">
        <!-- Sidebar (novo header lateral) -->
        <aside class="sidebar">
            <div class="sidebar-header">
                <h2>Menu de Navegação</h2>
            </div>
            
            <ul class="menu-navigation">
                <li>
                    <a href="#" class="active">
                        <i class="fas fa-user"></i> Perfil do Usuário
                    </a>
                </li>
                <li>
                    <a href="#">
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
        
        <!-- Conteúdo principal -->
        <main class="conteudo-principal">
            <h1 class="titulo-pagina">Perfil do Usuário</h1>
            
            <!-- Informações principais do perfil -->
            <section class="perfil-usuario">
                <img src="/api/placeholder/150/150" alt="Foto do Perfil" class="foto-perfil">
                
                <div class="info-usuario">
                    <h2>João Silva</h2>
                    <p>"Apaixonado por literatura clássica e ficção científica. Sempre em busca de novas histórias para explorar!"</p>
                    <button class="editar-btn">
                        <i class="fas fa-edit"></i> Editar Perfil
                    </button>
                </div>
            </section>
            
            <!-- Seções com informações detalhadas -->
            <div class="secoes-info">
                <!-- Informações Pessoais -->
                <div class="secao-card">
                    <h3><i class="fas fa-id-card"></i> Informações Pessoais</h3>
                    <ul>
                        <li>
                            <span class="label">Nome:</span>
                            <span>João Silva</span>
                        </li>
                        <li>
                            <span class="label">E-mail:</span>
                            <span>joao.silva@email.com</span>
                        </li>
                        <li>
                            <span class="label">Telefone:</span>
                            <span>(11) 98765-4321</span>
                        </li>
                        <li>
                            <span class="label">Data de Nascimento:</span>
                            <span>15/05/1990</span>
                        </li>
                    </ul>
                    <div class="secao-footer">
                        <button class="editar-btn">
                            <i class="fas fa-edit"></i> Editar
                        </button>
                    </div>
                </div>
                
                <!-- Estatísticas de Leitura -->
                <div class="secao-card">
                    <h3><i class="fas fa-chart-line"></i> Estatísticas de Leitura</h3>
                    <ul>
                        <li>
                            <span class="label">Total de Livros:</span>
                            <span>42</span>
                        </li>
                        <li>
                            <span class="label">Livros Lidos:</span>
                            <span>28</span>
                        </li>
                        <li>
                            <span class="label">Livros em Leitura:</span>
                            <span>3</span>
                        </li>
                        <li>
                            <span class="label">Lista de Desejos:</span>
                            <span>11</span>
                        </li>
                    </ul>
                    <div class="secao-footer">
                        <button class="editar-btn">
                            <i class="fas fa-eye"></i> Ver Detalhes
                        </button>
                    </div>
                </div>
                
                <!-- Gêneros Favoritos -->
                <div class="secao-card">
                    <h3><i class="fas fa-bookmark"></i> Gêneros Favoritos</h3>
                    <ul>
                        <li>Ficção Científica</li>
                        <li>Fantasia</li>
                        <li>Literatura Clássica</li>
                        <li>Suspense</li>
                    </ul>
                    <div class="secao-footer">
                        <button class="editar-btn">
                            <i class="fas fa-edit"></i> Gerenciar
                        </button>
                    </div>
                </div>
                
                <!-- Preferências -->
                <div class="secao-card">
                    <h3><i class="fas fa-sliders-h"></i> Preferências</h3>
                    <ul>
                        <li>
                            <span class="label">Notificações:</span>
                            <span>Ativadas</span>
                        </li>
                        <li>
                            <span class="label">Tema:</span>
                            <span>Claro</span>
                        </li>
                        <li>
                            <span class="label">Idioma Principal:</span>
                            <span>Português</span>
                        </li>
                    </ul>
                    <div class="secao-footer">
                        <button class="editar-btn">
                            <i class="fas fa-cog"></i> Configurar
                        </button>
                    </div>
                </div>
            </div>
        </main>
    </div>
</body>
</html>