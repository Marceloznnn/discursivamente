<?php require_once BASE_PATH . '/src/views/partials/header.php'; ?>
<!-- O header.php deve conter a abertura do HTML, head, etc. -->
<head><link rel="stylesheet" href="/assets/css/pages/comunidade.css"></head>

<section class="banner-container">
    <div class="banner-content">
        <h1 class="banner-title">Comunidades Discursivamente</h1>
        <p class="banner-subtitle">Conecte-se com outros leitores apaixonados</p>
        <form class="search-form" action="/comunidades/buscar" method="GET">
            <input type="text" name="q" placeholder="Pesquisar por comunidades, tópicos ou interesses..." class="search-input">
            <button type="submit" class="search-button">Buscar</button>
        </form>
    </div>
</section>

<section class="comunidades-destaque">
    <div class="container">
        <h2 class="section-title">Comunidades em Destaque</h2>
        <div class="comunidades-grid" id="comunidades-destaque">
            <!-- Comunidades em destaque serão inseridas dinamicamente pelo JavaScript -->
        </div>
    </div>
</section>

<section class="categorias-comunidades">
    <div class="container">
        <h2 class="section-title">Explorar por Categoria</h2>
        <div class="categorias-tabs" id="categorias-tabs">
            <button class="tab-button active" data-categoria="todos">Todos</button>
            <button class="tab-button" data-categoria="foruns">Fóruns</button>
            <button class="tab-button" data-categoria="clubes-livro">Clubes do Livro</button>
            <button class="tab-button" data-categoria="grupos-estudo">Grupos de Estudo</button>
            <button class="tab-button" data-categoria="eventos">Eventos Literários</button>
        </div>
        <div class="categorias-content" id="categorias-content">
            <!-- Conteúdo das categorias será carregado dinamicamente -->
        </div>
    </div>
</section>

<section class="comunidades-populares">
    <div class="container">
        <div class="section-header">
            <h2 class="section-title">Comunidades Mais Ativas</h2>
            <a href="/comunidades/todas" class="ver-todas">Ver todas</a>
        </div>
        <div class="comunidades-slider">
            <button class="nav-button prev-button" id="comunidades-prev-button">
                <span class="arrow-left"></span>
            </button>
            <div class="comunidades-wrapper" id="comunidades-wrapper">
                <div class="comunidades-container" id="comunidades-container">
                    <!-- Comunidades serão inseridas dinamicamente -->
                </div>
            </div>
            <button class="nav-button next-button" id="comunidades-next-button">
                <span class="arrow-right"></span>
            </button>
        </div>
        <div class="pagination-dots" id="comunidades-pagination-dots">
            <!-- Dots serão inseridos dinamicamente -->
        </div>
    </div>
</section>

<section class="discussoes-recentes">
    <div class="container">
        <div class="section-header">
            <h2 class="section-title">Discussões em Andamento</h2>
            <div class="filtro-container">
                <select id="filtro-discussoes" class="filtro-select">
                    <option value="recentes">Mais recentes</option>
                    <option value="populares">Mais populares</option>
                    <option value="comentadas">Mais comentadas</option>
                </select>
            </div>
        </div>
        <div class="discussoes-grid" id="discussoes-grid">
            <!-- Discussões serão inseridas dinamicamente -->
        </div>
        <div class="ver-mais-container">
            <button id="carregar-mais-discussoes" class="ver-mais-button">Carregar mais discussões</button>
        </div>
    </div>
</section>

<section class="eventos-proximos">
    <div class="container">
        <h2 class="section-title">Próximos Eventos</h2>
        <div class="eventos-timeline" id="eventos-timeline">
            <!-- Eventos serão inseridos dinamicamente -->
        </div>
        <div class="eventos-calendario-link">
            <a href="/eventos/calendario" class="calendario-button">Ver calendário completo</a>
        </div>
    </div>
</section>

<section class="comunidade-mes">
    <div class="container">
        <div class="comunidade-mes-content">
            <div class="comunidade-mes-info">
                <div class="badge-destaque">Comunidade do Mês</div>
                <h2 class="comunidade-mes-title" id="comunidade-mes-title"><!-- Preenchido via JS --></h2>
                <p class="comunidade-mes-desc" id="comunidade-mes-desc"><!-- Preenchido via JS --></p>
                <div class="comunidade-stats">
                    <div class="stat">
                        <span class="stat-value" id="comunidade-membros"><!-- Preenchido via JS --></span>
                        <span class="stat-label">Membros</span>
                    </div>
                    <div class="stat">
                        <span class="stat-value" id="comunidade-posts"><!-- Preenchido via JS --></span>
                        <span class="stat-label">Discussões</span>
                    </div>
                    <div class="stat">
                        <span class="stat-value" id="comunidade-atividade"><!-- Preenchido via JS --></span>
                        <span class="stat-label">Nível de Atividade</span>
                    </div>
                </div>
                <a href="#" class="join-button" id="comunidade-mes-link">Participar desta comunidade</a>
            </div>
            <div class="comunidade-mes-image" id="comunidade-mes-image">
                <!-- Imagem da comunidade do mês será inserida via JS -->
            </div>
        </div>
    </div>
</section>

<section class="iniciar-comunidade">
    <div class="container">
        <div class="iniciar-content">
            <div class="iniciar-text">
                <h2 class="iniciar-title">Crie sua própria comunidade</h2>
                <p class="iniciar-desc">Não encontrou uma comunidade que corresponda ao seu interesse literário? Inicie sua própria comunidade e conecte-se com pessoas que compartilham da mesma paixão.</p>
                <ul class="iniciar-features">
                    <li><span class="check-icon"></span> Ferramentas completas de moderação</li>
                    <li><span class="check-icon"></span> Personalização de aparência e regras</li>
                    <li><span class="check-icon"></span> Integração com clubes do livro virtuais</li>
                    <li><span class="check-icon"></span> Suporte da equipe Discursivamente</li>
                </ul>
                <a href="/comunidades/criar" class="criar-button">Criar uma comunidade</a>
            </div>
            <div class="iniciar-image">
                <!-- Imagem ilustrativa -->
            </div>
        </div>
    </div>
</section>

<section class="newsletter">
    <div class="newsletter-container">
        <div class="newsletter-content">
            <div class="newsletter-text">
                <h2 class="newsletter-title">Receba atualizações das suas comunidades</h2>
                <p class="newsletter-desc">Fique por dentro das novidades e discussões das suas comunidades favoritas com nossa newsletter personalizada.</p>
                <ul class="newsletter-benefits">
                    <li><span class="check-icon"></span> Resumo semanal de discussões</li>
                    <li><span class="check-icon"></span> Novos eventos e encontros virtuais</li>
                    <li><span class="check-icon"></span> Recomendações de comunidades similares</li>
                    <li><span class="check-icon"></span> Destaques e contribuições populares</li>
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
                                <input type="checkbox" name="interests[]" value="foruns">
                                <span class="checkbox-custom"></span>
                                Fóruns
                            </label>
                            <label class="checkbox-label">
                                <input type="checkbox" name="interests[]" value="clubes-livro">
                                <span class="checkbox-custom"></span>
                                Clubes do Livro
                            </label>
                            <label class="checkbox-label">
                                <input type="checkbox" name="interests[]" value="eventos">
                                <span class="checkbox-custom"></span>
                                Eventos
                            </label>
                            <label class="checkbox-label">
                                <input type="checkbox" name="interests[]" value="grupos-estudo">
                                <span class="checkbox-custom"></span>
                                Grupos de Estudo
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
<script src="/assets/js/pages/comunidades.js"></script>