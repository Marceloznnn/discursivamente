<?php require_once BASE_PATH . '/src/views/partials/header.php'; ?>
<!-- O header.php deve conter a abertura do HTML, head, etc. -->
<head><link rel="stylesheet" href="/assets/css/pages/home.css"></head>

<!-- Banner Principal -->
<section class="banner-container">
    <div class="banner-content">
        <h1 class="banner-title">Bem-vindo à Discursivamente</h1>
        <p class="banner-subtitle">Sua jornada literária começa aqui</p>
        <form class="search-form" action="/inicio/buscar" method="GET">
            <input type="text" name="q" placeholder="Pesquisar por título, autor ou assunto..." class="search-input">
            <button type="submit" class="search-button">Buscar</button>
        </form>
    </div>
</section>

<!-- Sobre Nós -->
<section class="sobre-nos-container">
    <div class="sobre-nos-content">
        <h2 class="sobre-nos-title">Sobre Nós</h2>
        <div class="sobre-nos-grid">
            <div class="sobre-nos-image">
                <!-- Placeholder para imagem ilustrativa -->
            </div>
            <div class="sobre-nos-text">
                <h3 class="sobre-nos-subtitle">Muito mais que uma biblioteca</h3>
                <p class="sobre-nos-paragraph">
                    A <strong>Discursivamente</strong> nasceu da paixão pela literatura e pela troca de ideias.
                    Somos uma plataforma que une dois mundos: uma extensa biblioteca virtual e um vibrante espaço de
                    comunicação e interação.
                </p>
                <p class="sobre-nos-paragraph">
                    Como biblioteca, oferecemos um vasto acervo digital cuidadosamente curado, abrangendo diversos
                    gêneros e áreas do conhecimento, para satisfazer tanto leitores casuais quanto pesquisadores.
                </p>
                <p class="sobre-nos-paragraph">
                    Como plataforma de comunicação, criamos um ambiente onde leitores, autores e entusiastas podem
                    se conectar, discutir obras, compartilhar interpretações e formar comunidades em torno de
                    interesses literários comuns.
                </p>
                <p class="sobre-nos-paragraph">
                    Nossa missão é democratizar o acesso ao conhecimento e estimular o diálogo enriquecedor que
                    surge quando mentes curiosas se encontram em torno de boas histórias e ideias transformadoras.
                </p>
                <div class="sobre-nos-features">
                    <div class="feature">
                        <div class="feature-icon biblioteca-icon"></div>
                        <h4>Biblioteca Digital</h4>
                        <p>Acesso a milhares de obras em formato digital</p>
                    </div>
                    <div class="feature">
                        <div class="feature-icon comunidade-icon"></div>
                        <h4>Comunidade</h4>
                        <p>Grupos de discussão e clubes do livro virtuais</p>
                    </div>
                    <div class="feature">
                        <div class="feature-icon comunicacao-icon"></div>
                        <h4>Comunicação</h4>
                        <p>Fóruns e chats para troca de ideias sobre literatura</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Citações e Inspirações (NOVA SEÇÃO) -->
<section class="citacoes-container" id="citacoes">
    <div class="container">
        <h2 class="section-title">Citações Inspiradoras</h2>
        <p class="section-subtitle">Palavras que nos motivam e transformam</p>
        
        <div class="citacao-wrapper">
            <div class="citacao-destaque" id="citacao-destaque">
                <!-- Citação destaque será inserida dinamicamente -->
                <div class="citacao-placeholder">
                    <div class="quote-icon"></div>
                    <p class="citacao-texto">A leitura é uma viagem que se faz com os olhos...</p>
                    <p class="citacao-autor">— Nome do Autor</p>
                </div>
            </div>
            
            <div class="citacoes-slider" id="citacoes-slider">
                <!-- Outras citações serão inseridas dinamicamente -->
            </div>
            
            <div class="citacao-controls">
                <button class="citacao-button" id="citacao-anterior">Anterior</button>
                <button class="citacao-button" id="citacao-proxima">Próxima</button>
            </div>
        </div>
    </div>
</section>

<!-- Livros em Destaque -->
<section class="livros-destaque">
    <div class="container">
        <h2 class="section-title">Livros em Destaque</h2>
        <div class="livros-slider">
            <button class="nav-button prev-button" id="prev-button">
                <span class="arrow-left"></span>
            </button>
            <div class="livros-wrapper" id="livros-wrapper">
                <div class="livros-container" id="livros-container">
                    <!-- Livros serão inseridos dinamicamente -->
                </div>
            </div>
            <button class="nav-button next-button" id="next-button">
                <span class="arrow-right"></span>
            </button>
        </div>
        <div class="pagination-dots" id="pagination-dots">
            <!-- Dots serão inseridos dinamicamente -->
        </div>
    </div>
</section>

<!-- Blog ou Artigos Literários (NOVA SEÇÃO) -->
<section class="blog-container" id="blog">
    <div class="container">
        <h2 class="section-title">Blog Literário</h2>
        <p class="section-subtitle">Dicas, resenhas e curiosidades do mundo dos livros</p>
        
        <div class="blog-grid" id="blog-posts-container">
            <!-- Posts serão inseridos dinamicamente -->
            <!-- Estrutura de exemplo para orientação do JS -->
            <div class="blog-post-placeholder">
                <div class="blog-post-image"></div>
                <div class="blog-post-content">
                    <span class="blog-post-category">Resenha</span>
                    <h3 class="blog-post-title">Título do Artigo</h3>
                    <p class="blog-post-excerpt">Uma breve descrição do conteúdo do artigo...</p>
                    <div class="blog-post-meta">
                        <span class="blog-post-author">Por Autor</span>
                        <span class="blog-post-date">DD/MM/AAAA</span>
                    </div>
                    <a href="#" class="blog-post-link">Continuar lendo</a>
                </div>
            </div>
        </div>
        
        <div class="blog-footer">
            <a href="/blog" class="ver-mais-link">Ver todos os artigos <span class="arrow-right-icon"></span></a>
        </div>
    </div>
</section>

<!-- Feedback de Usuários -->
<section class="feedback-usuarios">
    <div class="container">
        <h2 class="section-title">O Que Nossos Leitores Dizem</h2>
        <div class="feedback-container" id="feedback-container">
            <!-- Feedbacks serão inseridos dinamicamente -->
        </div>
    </div>
</section>

<!-- Eventos e Workshops (NOVA SEÇÃO) -->
<section class="eventos-container" id="eventos">
    <div class="container">
        <h2 class="section-title">Eventos e Workshops</h2>
        <p class="section-subtitle">Participe de encontros literários, debates e cursos</p>
        
        <div class="eventos-grid" id="eventos-grid">
            <!-- Eventos serão inseridos dinamicamente -->
            <!-- Estrutura de exemplo para orientação do JS -->
            <div class="evento-card-placeholder">
                <div class="evento-data">
                    <span class="evento-dia">00</span>
                    <span class="evento-mes">MÊS</span>
                </div>
                <div class="evento-content">
                    <h3 class="evento-titulo">Nome do Evento</h3>
                    <p class="evento-descricao">Descrição breve do evento...</p>
                    <div class="evento-detalhes">
                        <span class="evento-horario"><i class="clock-icon"></i> 00:00</span>
                        <span class="evento-local"><i class="location-icon"></i> Local do Evento</span>
                    </div>
                    <a href="#" class="evento-link">Saiba mais</a>
                </div>
            </div>
        </div>
        
        <div class="eventos-footer">
            <a href="/eventos" class="ver-todos-eventos">Ver calendário completo <span class="arrow-right-icon"></span></a>
        </div>
    </div>
</section>

<!-- Notícias e Atualizações (NOVA SEÇÃO) -->
<section class="noticias-container" id="noticias">
    <div class="container">
        <h2 class="section-title">Notícias Literárias</h2>
        <p class="section-subtitle">Fique por dentro das novidades do mundo dos livros</p>
        
        <div class="noticias-grid" id="noticias-grid">
            <!-- Notícias serão inseridas dinamicamente -->
            <!-- Estrutura de exemplo para orientação do JS -->
            <div class="noticia-card-placeholder">
                <div class="noticia-imagem"></div>
                <div class="noticia-content">
                    <span class="noticia-categoria">Premiação</span>
                    <h3 class="noticia-titulo">Título da Notícia</h3>
                    <p class="noticia-resumo">Um breve resumo da notícia...</p>
                    <div class="noticia-meta">
                        <span class="noticia-data">DD/MM/AAAA</span>
                    </div>
                    <a href="#" class="noticia-link">Ler mais</a>
                </div>
            </div>
        </div>
        
        <div class="noticias-footer">
            <a href="/noticias" class="ver-mais-noticias">Ver todas as notícias <span class="arrow-right-icon"></span></a>
        </div>
    </div>
</section>

<!-- Categorias de Livros -->
<section class="categorias-livros">
    <div class="container">
        <h2 class="section-title">Principais Categorias</h2>
        <div class="categorias-grid">
            <!-- Categoria 1 -->
            <div class="categoria-card">
                <div class="categoria-icon fiction-icon"></div>
                <h3 class="categoria-titulo">Ficção</h3>
                <p class="categoria-desc">Romances, fantasia, ficção científica, suspense e mais histórias cativantes para todos os gostos.</p>
                <a href="#" class="categoria-link">Explorar <span class="arrow">→</span></a>
            </div>
            <!-- Categoria 2 -->
            <div class="categoria-card">
                <div class="categoria-icon nonfiction-icon"></div>
                <h3 class="categoria-titulo">Não-Ficção</h3>
                <p class="categoria-desc">Biografias, história, ciências, filosofia e conhecimentos que transformam a forma de ver o mundo.</p>
                <a href="#" class="categoria-link">Explorar <span class="arrow">→</span></a>
            </div>
            <!-- Categoria 3 -->
            <div class="categoria-card">
                <div class="categoria-icon literature-icon"></div>
                <h3 class="categoria-titulo">Literatura Clássica</h3>
                <p class="categoria-desc">Obras atemporais que marcaram gerações e continuam ressoando com leitores contemporâneos.</p>
                <a href="#" class="categoria-link">Explorar <span class="arrow">→</span></a>
            </div>
            <!-- Categoria 4 -->
            <div class="categoria-card">
                <div class="categoria-icon business-icon"></div>
                <h3 class="categoria-titulo">Negócios</h3>
                <p class="categoria-desc">Gestão, liderança, finanças e estratégias para desenvolvimento profissional e sucesso corporativo.</p>
                <a href="#" class="categoria-link">Explorar <span class="arrow">→</span></a>
            </div>
            <!-- Categoria 5 -->
            <div class="categoria-card">
                <div class="categoria-icon children-icon"></div>
                <h3 class="categoria-titulo">Infantil</h3>
                <p class="categoria-desc">Histórias encantadoras para despertar a imaginação e cultivar o amor pela leitura desde cedo.</p>
                <a href="#" class="categoria-link">Explorar <span class="arrow">→</span></a>
            </div>
            <!-- Categoria 6 -->
            <div class="categoria-card">
                <div class="categoria-icon arts-icon"></div>
                <h3 class="categoria-titulo">Arte & Cultura</h3>
                <p class="categoria-desc">Fotografia, música, cinema, arquitetura e expressões artísticas que enriquecem nosso entendimento cultural.</p>
                <a href="#" class="categoria-link">Explorar <span class="arrow">→</span></a>
            </div>
        </div>
    </div>
</section>

<!-- Newsletter -->
<section class="newsletter">
    <div class="newsletter-container">
        <div class="newsletter-content">
            <div class="newsletter-text">
                <h2 class="newsletter-title">Fique por dentro das novidades</h2>
                <p class="newsletter-desc">Inscreva-se em nossa newsletter e receba lançamentos exclusivos, promoções especiais e recomendações personalizadas de leitura diretamente em seu email.</p>
                <ul class="newsletter-benefits">
                    <li><span class="check-icon"></span> Lançamentos em primeira mão</li>
                    <li><span class="check-icon"></span> Descontos exclusivos para assinantes</li>
                    <li><span class="check-icon"></span> Recomendações personalizadas</li>
                    <li><span class="check-icon"></span> Conteúdo literário exclusivo</li>
                </ul>
            </div>
            <div class="newsletter-form-container">
                <form class="newsletter-form" action="/newsletter/subscribe" method="POST">
                    <div class="form-group">
                        <label for="name">Nome</label>
                        <input type="text" id="name" name="name" placeholder="Seu nome completo" required>
                    </div>
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" id="email" name="email" placeholder="seu.email@exemplo.com" required>
                    </div>
                    <div class="form-group preferences">
                        <p class="preference-title">Interesses (opcional)</p>
                        <div class="checkbox-group">
                            <label class="checkbox-label">
                                <input type="checkbox" name="interests[]" value="fiction">
                                <span class="checkbox-custom"></span>
                                Ficção
                            </label>
                            <label class="checkbox-label">
                                <input type="checkbox" name="interests[]" value="nonfiction">
                                <span class="checkbox-custom"></span>
                                Não-Ficção
                            </label>
                            <label class="checkbox-label">
                                <input type="checkbox" name="interests[]" value="business">
                                <span class="checkbox-custom"></span>
                                Negócios
                            </label>
                            <label class="checkbox-label">
                                <input type="checkbox" name="interests[]" value="children">
                                <span class="checkbox-custom"></span>
                                Infantil
                            </label>
                        </div>
                    </div>
                    <button type="submit" class="newsletter-button">Inscrever-se</button>
                    <p class="privacy-note">Respeitamos sua privacidade. Você pode cancelar a inscrição a qualquer momento.</p>
                </form>
            </div>
        </div>
    </div>
</section>

<?php require_once BASE_PATH . '/src/views/partials/footer.php'; ?>
<!-- O footer.php deve conter o fechamento das tags body e html -->

<!-- Atualize o caminho do script conforme sua estrutura de assets -->
<script src="/assets/js/pages/home.js"></script>