<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerenciar Livros</title>
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
            overflow-y: auto;
        }
        
        .titulo-pagina {
            margin-bottom: 25px;
            color: #333;
            border-bottom: 1px solid #eee;
            padding-bottom: 15px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .adicionar-livro-btn {
            background-color: #007bff;
            color: white;
            border: none;
            padding: 8px 15px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 0.9rem;
            transition: background-color 0.3s;
            display: flex;
            align-items: center;
        }
        
        .adicionar-livro-btn:hover {
            background-color: #0069d9;
        }
        
        .adicionar-livro-btn i {
            margin-right: 8px;
        }

        /* Cards de Estatísticas (estilo adaptado para combinar com o perfil) */
        .secoes-info {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
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
        
        .secao-card .valor-estatistica {
            font-size: 1.8rem;
            color: #333;
            margin: 10px 0;
            text-align: center;
        }
        
        .secao-card .descricao-estatistica {
            color: #666;
            text-align: center;
        }

        /* Filtros de livros */
        .filtros-container {
            background-color: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        
        .filtros-form {
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
            align-items: center;
        }
        
        .filtro-grupo {
            display: flex;
            align-items: center;
        }
        
        .filtro-grupo label {
            margin-right: 8px;
            font-weight: 500;
            color: #555;
        }
        
        .filtro-grupo select, .filtro-grupo input {
            padding: 8px 12px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 0.9rem;
        }
        
        .filtro-busca {
            flex-grow: 1;
            position: relative;
        }
        
        .filtro-busca input {
            width: 100%;
            padding: 8px 12px 8px 35px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 0.9rem;
        }
        
        .filtro-busca i {
            position: absolute;
            left: 12px;
            top: 50%;
            transform: translateY(-50%);
            color: #777;
        }
        
        .filtro-btn {
            background-color: #007bff;
            color: white;
            border: none;
            padding: 8px 15px;
            border-radius: 4px;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        
        .filtro-btn:hover {
            background-color: #0069d9;
        }

        /* Lista de livros */
        .livros-lista {
            margin-top: 20px;
        }
        
        .livro-card {
            display: flex;
            background-color: #fff;
            border-radius: 8px;
            margin-bottom: 20px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.05);
            overflow: hidden;
            border: 1px solid #eee;
        }
        
        .livro-imagem {
            width: 120px;
            height: 180px;
            object-fit: cover;
        }
        
        .livro-conteudo {
            flex-grow: 1;
            padding: 15px;
            display: flex;
            flex-direction: column;
        }
        
        .livro-header {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
        }
        
        .livro-titulo {
            font-size: 1.2rem;
            margin-bottom: 5px;
            color: #333;
        }
        
        .livro-autor {
            color: #666;
            margin-bottom: 10px;
            font-style: italic;
        }
        
        .livro-descricao {
            margin-bottom: 15px;
            color: #555;
            line-height: 1.4;
            flex-grow: 1;
        }
        
        .livro-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: auto;
        }
        
        .livro-meta {
            display: flex;
            gap: 15px;
        }
        
        .meta-item {
            display: flex;
            align-items: center;
            color: #777;
            font-size: 0.9rem;
        }
        
        .meta-item i {
            margin-right: 5px;
            color: #007bff;
        }
        
        .livro-acoes {
            display: flex;
            gap: 10px;
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
            display: flex;
            align-items: center;
        }
        
        .editar-btn:hover {
            background-color: #0069d9;
        }
        
        .deletar-btn {
            background-color: #dc3545;
            color: white;
            border: none;
            padding: 8px 15px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 0.9rem;
            transition: background-color 0.3s;
            display: flex;
            align-items: center;
        }
        
        .deletar-btn:hover {
            background-color: #c82333;
        }
        
        .editar-btn i, .deletar-btn i {
            margin-right: 5px;
        }
        
        .livro-badge {
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 0.75rem;
            font-weight: bold;
        }
        
        .badge-publicado {
            background-color: #28a745;
            color: white;
        }
        
        .badge-rascunho {
            background-color: #6c757d;
            color: white;
        }
        
        .avaliacao {
            display: flex;
            align-items: center;
            margin-bottom: 5px;
        }
        
        .avaliacao .estrelas {
            color: #ffc107;
            margin-right: 5px;
        }
        
        .avaliacao .valor {
            font-weight: bold;
        }
        
        /* Paginação */
        .paginacao {
            display: flex;
            justify-content: center;
            margin-top: 30px;
        }
        
        .paginacao-lista {
            display: flex;
            list-style: none;
        }
        
        .paginacao-item a {
            display: block;
            padding: 8px 12px;
            margin: 0 5px;
            border-radius: 4px;
            background-color: #f8f9fa;
            border: 1px solid #ddd;
            color: #007bff;
            text-decoration: none;
            transition: all 0.3s;
        }
        
        .paginacao-item.active a {
            background-color: #007bff;
            color: white;
            border-color: #007bff;
        }
        
        .paginacao-item a:hover {
            background-color: #e9ecef;
        }
    </style>
    <!-- Font Awesome para ícones -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
</head>
<body>
    <!-- Header principal fixo -->
    <header class="header-principal">
        <h1>Biblioteca Virtual</h1>
    </header>
    
    <!-- Container que envolve sidebar e conteúdo com borda -->
    <div class="container-principal">
        <!-- Sidebar (menu lateral) -->
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
                    <a href="#">
                        <i class="fas fa-edit"></i> Editar Informações
                    </a>
                </li>
                <li>
                    <a href="#" class="active">
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
            <h1 class="titulo-pagina">Gerenciar Livros
                <button class="editar-btn adicionar-livro-btn">
                    <i class="fas fa-plus"></i> Adicionar Novo Livro
                </button>
            </h1>
            
            <!-- Cards de Estatísticas (estilo do perfil) -->
            <div class="secoes-info">
                <div class="secao-card">
                    <h3><i class="fas fa-book"></i> Livros Publicados</h3>
                    <p class="valor-estatistica">32</p>
                    <p class="descricao-estatistica">Total de livros publicados na plataforma</p>
                    <div class="secao-footer">
                        <button class="editar-btn">
                            <i class="fas fa-eye"></i> Ver Todos
                        </button>
                    </div>
                </div>
                
                <div class="secao-card">
                    <h3><i class="fas fa-star"></i> Avaliações</h3>
                    <p class="valor-estatistica">4.7</p>
                    <p class="descricao-estatistica">Média de avaliações recebidas</p>
                    <div class="secao-footer">
                        <button class="editar-btn">
                            <i class="fas fa-chart-bar"></i> Ver Detalhes
                        </button>
                    </div>
                </div>
                
                <div class="secao-card">
                    <h3><i class="fas fa-comments"></i> Comentários</h3>
                    <p class="valor-estatistica">238</p>
                    <p class="descricao-estatistica">Total de comentários recebidos</p>
                    <div class="secao-footer">
                        <button class="editar-btn">
                            <i class="fas fa-eye"></i> Ver Todos
                        </button>
                    </div>
                </div>
                
                <div class="secao-card">
                    <h3><i class="fas fa-eye"></i> Visualizações</h3>
                    <p class="valor-estatistica">5.4K</p>
                    <p class="descricao-estatistica">Total de visualizações</p>
                    <div class="secao-footer">
                        <button class="editar-btn">
                            <i class="fas fa-chart-line"></i> Ver Análise
                        </button>
                    </div>
                </div>
            </div>
            
            <!-- Filtros de livros -->
            <section class="filtros-container">
                <form class="filtros-form">
                    <div class="filtro-grupo">
                        <label for="filtro-genero">Gênero:</label>
                        <select id="filtro-genero">
                            <option value="">Todos</option>
                            <option value="ficcao">Ficção Científica</option>
                            <option value="fantasia">Fantasia</option>
                            <option value="romance">Romance</option>
                            <option value="suspense">Suspense</option>
                        </select>
                    </div>
                    
                    <div class="filtro-grupo">
                        <label for="filtro-status">Status:</label>
                        <select id="filtro-status">
                            <option value="">Todos</option>
                            <option value="publicado">Publicado</option>
                            <option value="rascunho">Rascunho</option>
                        </select>
                    </div>
                    
                    <div class="filtro-grupo">
                        <label for="filtro-ano">Ano:</label>
                        <select id="filtro-ano">
                            <option value="">Todos</option>
                            <option value="2025">2025</option>
                            <option value="2024">2024</option>
                            <option value="2023">2023</option>
                            <option value="2022">2022</option>
                        </select>
                    </div>
                    
                    <div class="filtro-busca">
                        <i class="fas fa-search"></i>
                        <input type="text" placeholder="Buscar por título ou autor...">
                    </div>
                    
                    <button type="submit" class="filtro-btn">Filtrar</button>
                </form>
            </section>
            
            <!-- Lista de livros -->
            <section class="livros-lista">
                <!-- Livro 1 -->
                <div class="livro-card">
                    <img src="https://th.bing.com/th/id/OIP.nQlzmrFpKiFiEG93V2qACQHaLH?rs=1&pid=ImgDetMain" alt="Capa do Livro" class="livro-imagem">
                    <div class="livro-conteudo">
                        <div class="livro-header">
                            <div>
                                <h3 class="livro-titulo">A Odisseia no Espaço</h3>
                                <p class="livro-autor">por João Silva</p>
                                <div class="avaliacao">
                                    <span class="estrelas">
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star-half-alt"></i>
                                    </span>
                                    <span class="valor">4.5</span>
                                </div>
                            </div>
                            <span class="livro-badge badge-publicado">Publicado</span>
                        </div>
                        
                        <p class="livro-descricao">
                            Uma história épica sobre exploração espacial e descobertas que mudam a compreensão da humanidade sobre o universo. Acompanhe a jornada da tripulação da nave Aurora em sua missão para explorar um novo sistema solar.
                        </p>
                        
                        <div class="livro-footer">
                            <div class="livro-meta">
                                <span class="meta-item">
                                    <i class="fas fa-eye"></i> 1.2K visualizações
                                </span>
                                <span class="meta-item">
                                    <i class="fas fa-comments"></i> 45 comentários
                                </span>
                                <span class="meta-item">
                                    <i class="fas fa-calendar-alt"></i> 15/03/2024
                                </span>
                            </div>
                            
                            <div class="livro-acoes">
                                <button class="editar-btn">
                                    <i class="fas fa-edit"></i> Editar
                                </button>
                                <button class="deletar-btn">
                                    <i class="fas fa-trash-alt"></i> Deletar
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Livro 2 -->
                <div class="livro-card">
                    <img src="https://m.media-amazon.com/images/I/51JAbYb0JqL._SL1000_.jpg" alt="Capa do Livro" class="livro-imagem">
                    <div class="livro-conteudo">
                        <div class="livro-header">
                            <div>
                                <h3 class="livro-titulo">Memórias de um Tempo Perdido</h3>
                                <p class="livro-autor">por João Silva</p>
                                <div class="avaliacao">
                                    <span class="estrelas">
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                    </span>
                                    <span class="valor">5.0</span>
                                </div>
                            </div>
                            <span class="livro-badge badge-publicado">Publicado</span>
                        </div>
                        
                        <p class="livro-descricao">
                            Um romance emocionante sobre as lembranças de uma vida marcada pelos acontecimentos históricos do século XX. Uma jornada através das décadas, acompanhando uma família e suas lutas para sobreviver em tempos de guerra e mudança.
                        </p>
                        
                        <div class="livro-footer">
                            <div class="livro-meta">
                                <span class="meta-item">
                                    <i class="fas fa-eye"></i> 985 visualizações
                                </span>
                                <span class="meta-item">
                                    <i class="fas fa-comments"></i> 32 comentários
                                </span>
                                <span class="meta-item">
                                    <i class="fas fa-calendar-alt"></i> 02/12/2023
                                </span>
                            </div>
                            
                            <div class="livro-acoes">
                                <button class="editar-btn">
                                    <i class="fas fa-edit"></i> Editar
                                </button>
                                <button class="deletar-btn">
                                    <i class="fas fa-trash-alt"></i> Deletar
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Livro 3 -->
                <div class="livro-card">
                    <img src="https://th.bing.com/th/id/R.e8694e9a9243ddf94275caface93fea9?rik=0Nubo4TMhDWj1w&riu=http%3a%2f%2fdesigncomcafe.com.br%2fwp-content%2fuploads%2f2017%2f08%2fcapas-de-livros-the-night-ocean.jpg&ehk=EWOF79c9vhCRI4rr2dpLpQXam4YaogR3N7GG6XKkwe8%3d&risl=&pid=ImgRaw&r=0" alt="Capa do Livro" class="livro-imagem">
                    <div class="livro-conteudo">
                        <div class="livro-header">
                            <div>
                                <h3 class="livro-titulo">O Mistério da Montanha Azul</h3>
                                <p class="livro-autor">por João Silva</p>
                                <div class="avaliacao">
                                    <span class="estrelas">
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                        <i class="far fa-star"></i>
                                    </span>
                                    <span class="valor">4.0</span>
                                </div>
                            </div>
                            <span class="livro-badge badge-rascunho">Rascunho</span>
                        </div>
                        
                        <p class="livro-descricao">
                            Um suspense envolvente sobre estranhos acontecimentos em uma pequena cidade aos pés da famosa Montanha Azul. Os moradores começam a relatar visões inexplicáveis e desaparecimentos misteriosos que irão testar os limites da realidade.
                        </p>
                        
                        <div class="livro-footer">
                            <div class="livro-meta">
                                <span class="meta-item">
                                    <i class="fas fa-eye"></i> N/A
                                </span>
                                <span class="meta-item">
                                    <i class="fas fa-comments"></i> N/A
                                </span>
                                <span class="meta-item">
                                    <i class="fas fa-calendar-alt"></i> Em edição
                                </span>
                            </div>
                            
                            <div class="livro-acoes">
                                <button class="editar-btn">
                                    <i class="fas fa-edit"></i> Editar
                                </button>
                                <button class="deletar-btn">
                                    <i class="fas fa-trash-alt"></i> Deletar
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
            
            <!-- Paginação -->
            <div class="paginacao">
                <ul class="paginacao-lista">
                    <li class="paginacao-item">
                        <a href="#"><i class="fas fa-chevron-left"></i></a>
                    </li>
                    <li class="paginacao-item active">
                        <a href="#">1</a>
                    </li>
                    <li class="paginacao-item">
                        <a href="#">2</a>
                    </li>
                    <li class="paginacao-item">
                        <a href="#">3</a>
                    </li>
                    <li class="paginacao-item">
                        <a href="#"><i class="fas fa-chevron-right"></i></a>
                    </li>
                </ul>
            </div>
        </main>
    </div>
</body>
</html>