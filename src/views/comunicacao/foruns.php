<?php require_once BASE_PATH . '/src/views/partials/header.php'; ?>

<!-- Banner de Boas-Vindas dos Fóruns -->
<section class="forum-banner">
    <div class="container">
        <div class="banner-content">
            <h1>Bem-vindo aos Fóruns Discursivamente!</h1>
            <p class="lead">Compartilhe ideias, faça perguntas e participe de debates enriquecedores com nossa comunidade.</p>
        </div>
    </div>
</section>

<!-- Seção de Introdução -->
<section class="forum-intro">
    <div class="container">
        <div class="row">
            <div class="col-md-8 offset-md-2 text-center">
                <h2>Um Espaço para Diálogos e Reflexões</h2>
                <p>Nossos fóruns são ambientes dedicados à construção coletiva de conhecimento, onde diferentes perspectivas 
                   são valorizadas e respeitadas. Aqui você pode iniciar discussões sobre temas que lhe interessam, 
                   compartilhar suas ideias e aprender com outros membros da comunidade.</p>
            </div>
        </div>
    </div>
</section>

<!-- Botões de Ação Principal -->
<section class="forum-actions">
    <div class="container">
        <div class="row">
            <div class="col-md-8">
                <div class="input-group mb-3">
                    <input type="text" class="form-control form-control-lg" placeholder="Pesquisar nos fóruns...">
                    <button class="btn btn-primary" type="button"><i class="fas fa-search"></i></button>
                </div>
            </div>
            <div class="col-md-4 text-end">
                <button class="btn btn-success btn-lg criar-topico"><i class="fas fa-plus-circle me-2"></i>Criar Novo Tópico</button>
            </div>
        </div>
    </div>
</section>

<!-- Categorias de Fóruns -->
<section class="forum-categories">
    <div class="container">
        <h2 class="section-title">Categorias de Discussão</h2>
        
        <div class="row">
            <!-- Categoria Literatura -->
            <div class="col-md-4 mb-4">
                <div class="category-card">
                    <div class="card-icon">
                        <i class="fas fa-book"></i>
                    </div>
                    <div class="card-content">
                        <h3>Literatura</h3>
                        <div class="stats">
                            <span><i class="fas fa-comments"></i> 124 tópicos</span>
                            <span><i class="fas fa-users"></i> 1.5k membros</span>
                        </div>
                        <p>Discussões sobre obras literárias, autores, gêneros e análises críticas.</p>
                        <a href="#" class="btn btn-outline-primary">Explorar</a>
                    </div>
                </div>
            </div>
            
            <!-- Categoria Filosofia -->
            <div class="col-md-4 mb-4">
                <div class="category-card">
                    <div class="card-icon">
                        <i class="fas fa-brain"></i>
                    </div>
                    <div class="card-content">
                        <h3>Filosofia</h3>
                        <div class="stats">
                            <span><i class="fas fa-comments"></i> 98 tópicos</span>
                            <span><i class="fas fa-users"></i> 920 membros</span>
                        </div>
                        <p>Debates sobre pensadores clássicos e contemporâneos, correntes filosóficas e questionamentos.</p>
                        <a href="#" class="btn btn-outline-primary">Explorar</a>
                    </div>
                </div>
            </div>
            
            <!-- Categoria Ciências -->
            <div class="col-md-4 mb-4">
                <div class="category-card">
                    <div class="card-icon">
                        <i class="fas fa-flask"></i>
                    </div>
                    <div class="card-content">
                        <h3>Ciências</h3>
                        <div class="stats">
                            <span><i class="fas fa-comments"></i> 87 tópicos</span>
                            <span><i class="fas fa-users"></i> 763 membros</span>
                        </div>
                        <p>Discussões sobre avanços científicos, descobertas recentes e o impacto da ciência na sociedade.</p>
                        <a href="#" class="btn btn-outline-primary">Explorar</a>
                    </div>
                </div>
            </div>
            
            <!-- Categoria Arte e Cultura -->
            <div class="col-md-4 mb-4">
                <div class="category-card">
                    <div class="card-icon">
                        <i class="fas fa-palette"></i>
                    </div>
                    <div class="card-content">
                        <h3>Arte e Cultura</h3>
                        <div class="stats">
                            <span><i class="fas fa-comments"></i> 76 tópicos</span>
                            <span><i class="fas fa-users"></i> 692 membros</span>
                        </div>
                        <p>Conversas sobre manifestações artísticas, movimentos culturais e expressões contemporâneas.</p>
                        <a href="#" class="btn btn-outline-primary">Explorar</a>
                    </div>
                </div>
            </div>
            
            <!-- Categoria Escrita Criativa -->
            <div class="col-md-4 mb-4">
                <div class="category-card">
                    <div class="card-icon">
                        <i class="fas fa-pencil-alt"></i>
                    </div>
                    <div class="card-content">
                        <h3>Escrita Criativa</h3>
                        <div class="stats">
                            <span><i class="fas fa-comments"></i> 112 tópicos</span>
                            <span><i class="fas fa-users"></i> 845 membros</span>
                        </div>
                        <p>Compartilhamento de textos autorais, técnicas de escrita e feedback construtivo.</p>
                        <a href="#" class="btn btn-outline-primary">Explorar</a>
                    </div>
                </div>
            </div>
            
            <!-- Categoria Debates Contemporâneos -->
            <div class="col-md-4 mb-4">
                <div class="category-card">
                    <div class="card-icon">
                        <i class="fas fa-comments"></i>
                    </div>
                    <div class="card-content">
                        <h3>Debates Contemporâneos</h3>
                        <div class="stats">
                            <span><i class="fas fa-comments"></i> 143 tópicos</span>
                            <span><i class="fas fa-users"></i> 1.2k membros</span>
                        </div>
                        <p>Discussões sobre temas atuais, questões sociais e desafios contemporâneos.</p>
                        <a href="#" class="btn btn-outline-primary">Explorar</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Tópicos Recentes -->
<section class="recent-topics">
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="section-title mb-0">Discussões Recentes</h2>
            <div class="dropdown">
                <button class="btn btn-outline-secondary dropdown-toggle" type="button" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
                    Ordenar por: Mais Recentes
                </button>
                <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                    <li><a class="dropdown-item active" href="#">Mais Recentes</a></li>
                    <li><a class="dropdown-item" href="#">Mais Comentados</a></li>
                    <li><a class="dropdown-item" href="#">Mais Visualizados</a></li>
                </ul>
            </div>
        </div>
        
        <!-- Lista de Tópicos -->
        <div class="topic-list">
            <!-- Tópico 1 -->
            <div class="topic-card">
                <div class="user-avatar">
                    <img src="/assets/images/avatar1.jpg" alt="Avatar do usuário" onerror="this.src='/assets/images/default-avatar.png'">
                </div>
                <div class="topic-content">
                    <div class="topic-header">
                        <h3><a href="#">O impacto da literatura distópica na visão da sociedade contemporânea</a></h3>
                        <span class="badge bg-primary">Literatura</span>
                    </div>
                    <p class="topic-excerpt">Como obras como "1984" e "Admirável Mundo Novo" moldam nossa percepção dos riscos sociais e tecnológicos atuais...</p>
                    <div class="topic-meta">
                        <span><i class="fas fa-user"></i> Por Maria Silva</span>
                        <span><i class="fas fa-clock"></i> Há 2 horas</span>
                        <span><i class="fas fa-comment"></i> 14 respostas</span>
                        <span><i class="fas fa-eye"></i> 89 visualizações</span>
                    </div>
                </div>
            </div>
            
            <!-- Tópico 2 -->
            <div class="topic-card">
                <div class="user-avatar">
                    <img src="/assets/images/avatar2.jpg" alt="Avatar do usuário" onerror="this.src='/assets/images/default-avatar.png'">
                </div>
                <div class="topic-content">
                    <div class="topic-header">
                        <h3><a href="#">Filosofia estoica aplicada ao século XXI: ainda faz sentido?</a></h3>
                        <span class="badge bg-info">Filosofia</span>
                    </div>
                    <p class="topic-excerpt">Discutindo como os princípios estoicos de Epicteto, Sêneca e Marco Aurélio podem ser aplicados em nosso contexto atual...</p>
                    <div class="topic-meta">
                        <span><i class="fas fa-user"></i> Por Carlos Mendes</span>
                        <span><i class="fas fa-clock"></i> Há 5 horas</span>
                        <span><i class="fas fa-comment"></i> 23 respostas</span>
                        <span><i class="fas fa-eye"></i> 142 visualizações</span>
                    </div>
                </div>
            </div>
            
            <!-- Tópico 3 -->
            <div class="topic-card">
                <div class="user-avatar">
                    <img src="/assets/images/avatar3.jpg" alt="Avatar do usuário" onerror="this.src='/assets/images/default-avatar.png'">
                </div>
                <div class="topic-content">
                    <div class="topic-header">
                        <h3><a href="#">Técnicas de construção de personagens complexos na literatura</a></h3>
                        <span class="badge bg-success">Escrita Criativa</span>
                    </div>
                    <p class="topic-excerpt">Compartilhando métodos e abordagens para criar personagens tridimensionais que cativam os leitores...</p>
                    <div class="topic-meta">
                        <span><i class="fas fa-user"></i> Por Ana Castro</span>
                        <span><i class="fas fa-clock"></i> Há 1 dia</span>
                        <span><i class="fas fa-comment"></i> 19 respostas</span>
                        <span><i class="fas fa-eye"></i> 124 visualizações</span>
                    </div>
                </div>
            </div>
            
            <!-- Tópico 4 -->
            <div class="topic-card">
                <div class="user-avatar">
                    <img src="/assets/images/avatar4.jpg" alt="Avatar do usuário" onerror="this.src='/assets/images/default-avatar.png'">
                </div>
                <div class="topic-content">
                    <div class="topic-header">
                        <h3><a href="#">O papel da arte na conscientização sobre questões ambientais</a></h3>
                        <span class="badge bg-warning text-dark">Arte e Cultura</span>
                    </div>
                    <p class="topic-excerpt">Como artistas contemporâneos estão usando diferentes mídias para alertar sobre a crise climática e sustentabilidade...</p>
                    <div class="topic-meta">
                        <span><i class="fas fa-user"></i> Por Roberto Alves</span>
                        <span><i class="fas fa-clock"></i> Há 2 dias</span>
                        <span><i class="fas fa-comment"></i> 31 respostas</span>
                        <span><i class="fas fa-eye"></i> 203 visualizações</span>
                    </div>
                </div>
            </div>
            
            <!-- Tópico 5 -->
            <div class="topic-card">
                <div class="user-avatar">
                    <img src="/assets/images/avatar5.jpg" alt="Avatar do usuário" onerror="this.src='/assets/images/default-avatar.png'">
                </div>
                <div class="topic-content">
                    <div class="topic-header">
                        <h3><a href="#">Avanços recentes em física quântica e suas implicações filosóficas</a></h3>
                        <span class="badge bg-danger">Ciências</span>
                    </div>
                    <p class="topic-excerpt">Discutindo como as descobertas na área de física quântica desafiam nossa compreensão da realidade...</p>
                    <div class="topic-meta">
                        <span><i class="fas fa-user"></i> Por Pedro Santos</span>
                        <span><i class="fas fa-clock"></i> Há 3 dias</span>
                        <span><i class="fas fa-comment"></i> 27 respostas</span>
                        <span><i class="fas fa-eye"></i> 176 visualizações</span>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Paginação -->
        <nav class="mt-4">
            <ul class="pagination justify-content-center">
                <li class="page-item disabled">
                    <a class="page-link" href="#" tabindex="-1" aria-disabled="true">Anterior</a>
                </li>
                <li class="page-item active"><a class="page-link" href="#">1</a></li>
                <li class="page-item"><a class="page-link" href="#">2</a></li>
                <li class="page-item"><a class="page-link" href="#">3</a></li>
                <li class="page-item">
                    <a class="page-link" href="#">Próxima</a>
                </li>
            </ul>
        </nav>
    </div>
</section>

<!-- CSS Personalizado -->
<style>
    /* Estilos do Banner */
    .forum-banner {
        background: linear-gradient(rgba(0, 0, 0, 0.7), rgba(0, 0, 0, 0.7)), url('/assets/images/forum-banner.jpg');
        background-size: cover;
        background-position: center;
        color: white;
        padding: 80px 0;
        margin-bottom: 30px;
        text-align: center;
    }
    
    .forum-banner h1 {
        font-size: 2.8rem;
        font-weight: 700;
        margin-bottom: 20px;
    }
    
    .forum-banner .lead {
        font-size: 1.3rem;
        max-width: 800px;
        margin: 0 auto;
    }
    
    /* Seção de Introdução */
    .forum-intro {
        padding: 40px 0;
        background-color: #f8f9fa;
        margin-bottom: 30px;
    }
    
    .forum-intro h2 {
        margin-bottom: 20px;
        color: #343a40;
    }
    
    .forum-intro p {
        font-size: 1.1rem;
        line-height: 1.6;
        color: #495057;
    }
    
    /* Botões de Ação */
    .forum-actions {
        padding: 20px 0 40px 0;
    }
    
    .criar-topico {
        transition: all 0.3s ease;
        box-shadow: 0 4px 6px rgba(0,0,0,0.1);
    }
    
    .criar-topico:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 8px rgba(0,0,0,0.15);
    }
    
    /* Categorias de Fóruns */
    .forum-categories {
        padding: 40px 0;
        background-color: #f1f3f5;
    }
    
    .section-title {
        font-size: 1.8rem;
        font-weight: 600;
        margin-bottom: 30px;
        color: #343a40;
        position: relative;
        padding-bottom: 10px;
    }
    
    .section-title:after {
        content: '';
        position: absolute;
        left: 0;
        bottom: 0;
        width: 60px;
        height: 3px;
        background-color: #007bff;
    }
    
    .category-card {
        display: flex;
        background-color: #fff;
        border-radius: 8px;
        overflow: hidden;
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        transition: transform 0.3s, box-shadow 0.3s;
        height: 100%;
    }
    
    .category-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 25px rgba(0,0,0,0.15);
    }
    
    .card-icon {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 80px;
        background-color: #f8f9fa;
        font-size: 2rem;
        color: #007bff;
    }
    
    .card-content {
        padding: 20px;
        flex: 1;
    }
    
    .card-content h3 {
        font-size: 1.25rem;
        margin-bottom: 10px;
        font-weight: 600;
    }
    
    .card-content .stats {
        display: flex;
        gap: 12px;
        margin-bottom: 10px;
        font-size: 0.85rem;
        color: #6c757d;
    }
    
    .card-content p {
        font-size: 0.95rem;
        margin-bottom: 15px;
        color: #495057;
    }
    
    /* Tópicos Recentes */
    .recent-topics {
        padding: 50px 0;
    }
    
    .topic-card {
        display: flex;
        background-color: #fff;
        border-radius: 8px;
        padding: 20px;
        margin-bottom: 20px;
        box-shadow: 0 3px 10px rgba(0,0,0,0.08);
        transition: transform 0.2s, box-shadow 0.2s;
    }
    
    .topic-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 5px 15px rgba(0,0,0,0.12);
    }
    
    .user-avatar {
        width: 60px;
        height: 60px;
        margin-right: 20px;
        flex-shrink: 0;
    }
    
    .user-avatar img {
        width: 100%;
        height: 100%;
        border-radius: 50%;
        object-fit: cover;
    }
    
    .topic-content {
        flex: 1;
    }
    
    .topic-header {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        margin-bottom: 10px;
    }
    
    .topic-header h3 {
        font-size: 1.2rem;
        font-weight: 600;
        margin: 0;
    }
    
    .topic-header h3 a {
        color: #343a40;
        text-decoration: none;
        transition: color 0.2s;
    }
    
    .topic-header h3 a:hover {
        color: #007bff;
    }
    
    .topic-excerpt {
        font-size: 0.95rem;
        color: #6c757d;
        margin-bottom: 12px;
    }
    
    .topic-meta {
        display: flex;
        flex-wrap: wrap;
        gap: 15px;
        font-size: 0.85rem;
        color: #6c757d;
    }
    
    .topic-meta span {
        display: flex;
        align-items: center;
    }
    
    .topic-meta i {
        margin-right: 5px;
    }
    
    /* Responsividade */
    @media (max-width: 992px) {
        .forum-banner h1 {
            font-size: 2.4rem;
        }
        
        .category-card {
            margin-bottom: 20px;
        }
    }
    
    @media (max-width: 768px) {
        .forum-banner {
            padding: 60px 0;
        }
        
        .forum-banner h1 {
            font-size: 2rem;
        }
        
        .forum-actions .col-md-4 {
            margin-top: 15px;
            text-align: center;
        }
        
        .topic-card {
            flex-direction: column;
        }
        
        .user-avatar {
            margin-bottom: 15px;
        }
        
        .topic-header {
            flex-direction: column;
        }
        
        .topic-header .badge {
            align-self: flex-start;
            margin-top: 10px;
        }
    }
    
    @media (max-width: 576px) {
        .forum-banner h1 {
            font-size: 1.8rem;
        }
        
        .topic-meta {
            flex-direction: column;
            gap: 8px;
        }
    }
</style>

<?php require_once BASE_PATH . '/src/views/partials/footer.php'; ?>