<?php require_once BASE_PATH . '/src/views/partials/header.php'; ?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Discursivamente - Biblioteca Virtual</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&family=Cormorant+Garamond:wght@300;400;600&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="/css/home.css">
</head>

<body>
    <section class="banner-container">
        <div class="banner-content">
            <h1 class="banner-title">Bem-vindo à Discursivamente</h1>
            <p class="banner-subtitle">Sua jornada literária começa aqui</p>

            <form class="search-form">
                <input type="text" placeholder="Pesquisar por título, autor ou assunto..." class="search-input">
                <button type="submit" class="search-button">Buscar</button>
            </form>
        </div>
    </section>
    <section class="sobre-nos-container">
        <div class="sobre-nos-content">
            <h2 class="sobre-nos-title">Sobre Nós</h2>

            <div class="sobre-nos-grid">
                <div class="sobre-nos-image">
                    <!-- Esta div conterá a imagem ilustrativa -->
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
    <!-- Seção de Livros em Destaque -->
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

    <!-- Seção de Feedback de Usuários -->
    <section class="feedback-usuarios">
        <div class="container">
            <h2 class="section-title">O Que Nossos Leitores Dizem</h2>
            <div class="feedback-container" id="feedback-container">
                <!-- Feedbacks serão inseridos dinamicamente -->
            </div>
        </div>
    </section>
    <!-- Book Categories Section -->
    <section class="categorias-livros">
        <div class="container">
            <h2 class="section-title">Principais Categorias</h2>

            <div class="categorias-grid">
                <!-- Category 1 -->
                <div class="categoria-card">
                    <div class="categoria-icon fiction-icon"></div>
                    <h3 class="categoria-titulo">Ficção</h3>
                    <p class="categoria-desc">Romances, fantasia, ficção científica, suspense e mais histórias
                        cativantes para todos os gostos.</p>
                    <a href="#" class="categoria-link">Explorar <span class="arrow">→</span></a>
                </div>

                <!-- Category 2 -->
                <div class="categoria-card">
                    <div class="categoria-icon nonfiction-icon"></div>
                    <h3 class="categoria-titulo">Não-Ficção</h3>
                    <p class="categoria-desc">Biografias, história, ciências, filosofia e conhecimentos que transformam
                        a forma de ver o mundo.</p>
                    <a href="#" class="categoria-link">Explorar <span class="arrow">→</span></a>
                </div>

                <!-- Category 3 -->
                <div class="categoria-card">
                    <div class="categoria-icon literature-icon"></div>
                    <h3 class="categoria-titulo">Literatura Clássica</h3>
                    <p class="categoria-desc">Obras atemporais que marcaram gerações e continuam ressoando com leitores
                        contemporâneos.</p>
                    <a href="#" class="categoria-link">Explorar <span class="arrow">→</span></a>
                </div>

                <!-- Category 4 -->
                <div class="categoria-card">
                    <div class="categoria-icon business-icon"></div>
                    <h3 class="categoria-titulo">Negócios</h3>
                    <p class="categoria-desc">Gestão, liderança, finanças e estratégias para desenvolvimento
                        profissional e sucesso corporativo.</p>
                    <a href="#" class="categoria-link">Explorar <span class="arrow">→</span></a>
                </div>

                <!-- Category 5 -->
                <div class="categoria-card">
                    <div class="categoria-icon children-icon"></div>
                    <h3 class="categoria-titulo">Infantil</h3>
                    <p class="categoria-desc">Histórias encantadoras para despertar a imaginação e cultivar o amor pela
                        leitura desde cedo.</p>
                    <a href="#" class="categoria-link">Explorar <span class="arrow">→</span></a>
                </div>

                <!-- Category 6 -->
                <div class="categoria-card">
                    <div class="categoria-icon arts-icon"></div>
                    <h3 class="categoria-titulo">Arte & Cultura</h3>
                    <p class="categoria-desc">Fotografia, música, cinema, arquitetura e expressões artísticas que
                        enriquecem nosso entendimento cultural.</p>
                    <a href="#" class="categoria-link">Explorar <span class="arrow">→</span></a>
                </div>
            </div>
        </div>
    </section>

    <!-- Newsletter Section -->
    <section class="newsletter">
        <div class="newsletter-container">
            <div class="newsletter-content">
                <div class="newsletter-text">
                    <h2 class="newsletter-title">Fique por dentro das novidades</h2>
                    <p class="newsletter-desc">Inscreva-se em nossa newsletter e receba lançamentos exclusivos,
                        promoções especiais e recomendações personalizadas de leitura diretamente em seu email.</p>

                    <ul class="newsletter-benefits">
                        <li><span class="check-icon"></span> Lançamentos em primeira mão</li>
                        <li><span class="check-icon"></span> Descontos exclusivos para assinantes</li>
                        <li><span class="check-icon"></span> Recomendações personalizadas</li>
                        <li><span class="check-icon"></span> Conteúdo literário exclusivo</li>
                    </ul>
                </div>

                <div class="newsletter-form-container">
                    <form class="newsletter-form">
                        <div class="form-group">
                            <label for="name">Nome</label>
                            <input type="text" id="name" placeholder="Seu nome completo" required>
                        </div>

                        <div class="form-group">
                            <label for="email">Email</label>
                            <input type="email" id="email" placeholder="seu.email@exemplo.com" required>
                        </div>

                        <div class="form-group preferences">
                            <p class="preference-title">Interesses (opcional)</p>
                            <div class="checkbox-group">
                                <label class="checkbox-label">
                                    <input type="checkbox" name="interests" value="fiction">
                                    <span class="checkbox-custom"></span>
                                    Ficção
                                </label>

                                <label class="checkbox-label">
                                    <input type="checkbox" name="interests" value="nonfiction">
                                    <span class="checkbox-custom"></span>
                                    Não-Ficção
                                </label>

                                <label class="checkbox-label">
                                    <input type="checkbox" name="interests" value="business">
                                    <span class="checkbox-custom"></span>
                                    Negócios
                                </label>

                                <label class="checkbox-label">
                                    <input type="checkbox" name="interests" value="children">
                                    <span class="checkbox-custom"></span>
                                    Infantil
                                </label>
                            </div>
                        </div>

                        <button type="submit" class="newsletter-button">Inscrever-se</button>

                        <p class="privacy-note">Respeitamos sua privacidade. Você pode cancelar a inscrição a qualquer
                            momento.</p>
                    </form>
                </div>
            </div>
        </div>
    </section>
    <script src="/js/home.js"></script>
</body>

</html>