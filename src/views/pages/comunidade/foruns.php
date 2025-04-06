<?php require_once BASE_PATH . '/src/views/partials/header.php'; ?>
<link rel="stylesheet" href="/css/forum.css">
</head>
    <!-- Conteúdo Principal -->
    <div class="container">
        <main class="main-content">
            <section class="forum-content">
                <header class="forum-header">
                    <h1>Bem-vindo ao Fórum da Biblioteca</h1>
                    <p>Um espaço profissional para discussões literárias, troca de recomendações e informações sobre nossos serviços.</p>
                </header>

                <!-- Seção de Anúncios/Informativos -->
                <section class="announcements">
                    <h2>Anúncios Importantes</h2>
                    <div class="announcement-list">
                        <article class="announcement-item">
                            <h3>Aviso: Atualização dos Termos de Uso</h3>
                            <p>Fique atento às novas diretrizes do fórum e da biblioteca.</p>
                            <span class="announcement-meta">por Admin • 5 horas atrás</span>
                        </article>
                        <article class="announcement-item">
                            <h3>Regulamento do Fórum</h3>
                            <p>Conheça as regras para manter um ambiente saudável e produtivo.</p>
                            <span class="announcement-meta">por Moderação • 1 dia atrás</span>
                        </article>
                    </div>
                </section>

                <!-- Categorias -->
                <section class="categories-section">
                    <h2>Categorias</h2>
                    <div class="categories-grid">
                        <article class="category">
                            <div class="category-header">
                                <h3>Discussões Literárias</h3>
                                <i class="fa fa-book-reader"></i>
                            </div>
                            <div class="category-body">
                                <p>Debates sobre livros, autores e gêneros literários diversos.</p>
                                <div class="stat">
                                    <i class="fa fa-comments"></i>
                                    <span>148 tópicos</span>
                                </div>
                            </div>
                        </article>

                        <article class="category">
                            <div class="category-header">
                                <h3>Eventos e Lançamentos</h3>
                                <i class="fa fa-calendar"></i>
                            </div>
                            <div class="category-body">
                                <p>Informações e discussões sobre eventos, lançamentos e feiras culturais.</p>
                                <div class="stat">
                                    <i class="fa fa-comments"></i>
                                    <span>87 tópicos</span>
                                </div>
                            </div>
                        </article>

                        <article class="category">
                            <div class="category-header">
                                <h3>Recomendações e Resenhas</h3>
                                <i class="fa fa-book"></i>
                            </div>
                            <div class="category-body">
                                <p>Sugestões de leitura e compartilhamento de resenhas detalhadas.</p>
                                <div class="stat">
                                    <i class="fa fa-comments"></i>
                                    <span>215 tópicos</span>
                                </div>
                            </div>
                        </article>

                        <article class="category">
                            <div class="category-header">
                                <h3>Perguntas e Suporte</h3>
                                <i class="fa fa-question-circle"></i>
                            </div>
                            <div class="category-body">
                                <p>Dúvidas sobre os serviços da biblioteca e sugestões de melhorias.</p>
                                <div class="stat">
                                    <i class="fa fa-comments"></i>
                                    <span>94 tópicos</span>
                                </div>
                            </div>
                        </article>
                    </div>
                </section>

                <!-- Tópicos em Destaque -->
                <section class="featured-topics">
                    <h2>Tópicos em Destaque</h2>
                    <div class="topic-list">
                        <article class="topic-card">
                            <div class="topic-icon">
                                <i class="fa fa-star"></i>
                            </div>
                            <div class="topic-details">
                                <h3 class="topic-title">Dicas de Leitura para 2025</h3>
                                <div class="topic-meta">por <strong>Mariana Oliveira</strong> • 4 horas atrás</div>
                                <p>Confira nossa seleção dos melhores livros para este ano.</p>
                                <div class="topic-stats">
                                    <span><i class="fa fa-comment"></i> 30 respostas</span>
                                    <span><i class="fa fa-eye"></i> 200 visualizações</span>
                                </div>
                            </div>
                        </article>
                        <article class="topic-card">
                            <div class="topic-icon">
                                <i class="fa fa-star"></i>
                            </div>
                            <div class="topic-details">
                                <h3 class="topic-title">Inovação na Literatura Digital</h3>
                                <div class="topic-meta">por <strong>Felipe Souza</strong> • 6 horas atrás</div>
                                <p>Discussão sobre o impacto das novas tecnologias na leitura e publicação.</p>
                                <div class="topic-stats">
                                    <span><i class="fa fa-comment"></i> 18 respostas</span>
                                    <span><i class="fa fa-eye"></i> 150 visualizações</span>
                                </div>
                            </div>
                        </article>
                    </div>
                </section>

                <!-- Tópicos Recentes -->
                <section class="recent-topics">
                    <h2>Tópicos Recentes</h2>
                    <div class="topic-list">
                        <article class="topic-card">
                            <div class="topic-icon">
                                <i class="fa fa-book-reader"></i>
                            </div>
                            <div class="topic-details">
                                <h3 class="topic-title">Análise: A Literatura Brasileira Contemporânea</h3>
                                <div class="topic-meta">por <strong>Maria Silva</strong> • 3 horas atrás</div>
                                <p>Discussão sobre as tendências atuais e os autores emergentes no cenário nacional.</p>
                                <div class="topic-stats">
                                    <span><i class="fa fa-comment"></i> 24 respostas</span>
                                    <span><i class="fa fa-eye"></i> 156 visualizações</span>
                                </div>
                            </div>
                        </article>
                        <article class="topic-card">
                            <div class="topic-icon">
                                <i class="fa fa-book"></i>
                            </div>
                            <div class="topic-details">
                                <h3 class="topic-title">Clube do Livro - Leitura de Junho</h3>
                                <div class="topic-meta">por <strong>João Santos</strong> • 1 dia atrás</div>
                                <p>Participe da votação e sugira obras para a próxima leitura do clube.</p>
                                <div class="topic-stats">
                                    <span><i class="fa fa-comment"></i> 42 respostas</span>
                                    <span><i class="fa fa-eye"></i> 287 visualizações</span>
                                </div>
                            </div>
                        </article>
                        <article class="topic-card">
                            <div class="topic-icon">
                                <i class="fa fa-comments"></i>
                            </div>
                            <div class="topic-details">
                                <h3 class="topic-title">Feira de Troca de Livros - Preciso de voluntários</h3>
                                <div class="topic-meta">por <strong>Ana Ferreira</strong> • 2 dias atrás</div>
                                <p>Ajude na organização e logística do nosso evento tradicional de troca de livros.</p>
                                <div class="topic-stats">
                                    <span><i class="fa fa-comment"></i> 18 respostas</span>
                                    <span><i class="fa fa-eye"></i> 142 visualizações</span>
                                </div>
                            </div>
                        </article>
                    </div>
                    <div class="create-topic-wrapper">
                        <a href="#" class="create-topic">Criar Novo Tópico</a>
                    </div>
                </section>
            </section>

            <!-- Sidebar -->
            <aside class="sidebar">
                <!-- Filtros de Tópicos -->
                <section class="sidebar-section filters-section">
                    <h3>Filtrar Tópicos</h3>
                    <div class="filters">
                        <span class="filter-item active">Mais Recentes</span>
                        <span class="filter-item">Mais Comentados</span>
                        <span class="filter-item">Mais Visualizados</span>
                        <span class="filter-item">Sem Respostas</span>
                    </div>
                </section>

                <!-- Membros Ativos -->
                <section class="sidebar-section active-members-section">
                    <h3>Membros Ativos</h3>
                    <ul class="active-members">
                        <li><a href="#">Carlos Almeida</a></li>
                        <li><a href="#">Fernanda Costa</a></li>
                        <li><a href="#">Ricardo Lima</a></li>
                        <li><a href="#">Larissa Souza</a></li>
                        <li><a href="#">Eduardo Martins</a></li>
                    </ul>
                </section>

                <!-- Atividades Recentes -->
                <section class="sidebar-section recent-activities-section">
                    <h3>Atividades Recentes</h3>
                    <ul class="recent-activities">
                        <li>João comentou em <a href="#">Clube do Livro - Leitura de Junho</a></li>
                        <li>Ana respondeu em <a href="#">Feira de Troca de Livros</a></li>
                        <li>Maria publicou <a href="#">Análise: A Literatura Brasileira Contemporânea</a></li>
                    </ul>
                </section>

                <!-- Newsletter -->
                <section class="sidebar-section newsletter-section">
                    <h3>Newsletter</h3>
                    <p>Assine para receber novidades e atualizações.</p>
                    <form class="newsletter-form">
                        <input type="email" placeholder="Seu e-mail">
                        <button type="submit"><i class="fa fa-envelope"></i></button>
                    </form>
                </section>

                <!-- Banner de Evento -->
                <section class="sidebar-section event-banner-section">
                    <div class="event-banner">
                        <h4>Noite de Autógrafos</h4>
                        <p>Encontro com o autor Carlos Drummond nesta sexta-feira às 19h na biblioteca central.</p>
                    </div>
                </section>
            </aside>
        </main>
    </div>

    <!-- Rodapé -->
    <footer>
        <div class="container">
            <div class="footer-content">
                <section class="footer-section about-section">
                    <h3>Sobre o Fórum</h3>
                    <ul>
                        <li><a href="#">Quem Somos</a></li>
                        <li><a href="#">Diretrizes da Comunidade</a></li>
                        <li><a href="#">Perguntas Frequentes</a></li>
                        <li><a href="#">Política de Privacidade</a></li>
                    </ul>
                </section>
                <section class="footer-section links-section">
                    <h3>Links Úteis</h3>
                    <ul>
                        <li><a href="#">Catálogo Online</a></li>
                        <li><a href="#">Horário de Funcionamento</a></li>
                        <li><a href="#">Reserva de Livros</a></li>
                        <li><a href="#">Mapa da Biblioteca</a></li>
                    </ul>
                </section>
                <section class="footer-section contact-section">
                    <h3>Contato</h3>
                    <ul>
                        <li><a href="#">Fale Conosco</a></li>
                        <li><a href="#">Suporte</a></li>
                        <li><a href="#">Trabalhe Conosco</a></li>
                    </ul>
                    <div class="social-icons">
                        <a href="#"><i class="fa fa-facebook"></i></a>
                        <a href="#"><i class="fa fa-twitter"></i></a>
                        <a href="#"><i class="fa fa-instagram"></i></a>
                    </div>
                </section>
            </div>
            <div class="footer-bottom">
                <p>&copy; 2025 Fórum da Biblioteca. Todos os direitos reservados.</p>
            </div>
        </div>
    </footer>
</body>
</html>
