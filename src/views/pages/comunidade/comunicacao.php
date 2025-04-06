<?php require_once BASE_PATH . '/src/views/partials/header.php'; ?>
<link rel="stylesheet" href="/css/comunidade.css">
</head>
<body>
    <!-- Banner Principal -->
    <section class="banner">
        <div class="container">
            <h1>Bem-vindo à Central das Comunidades!</h1>
            <p>Explore um universo de conexões! Encontre clubes, fóruns de discussão e clubes de leitura que combinam com seus interesses e paixões.</p>
            <a href="/explorar.php" class="btn">Explorar Comunidades</a>
            <a href="/criar-comunidade.php" class="btn">Criar sua Comunidade</a>
        </div>
    </section>
    
    <!-- Seção de Pesquisa Rápida -->
    <section class="container">
        <h2 class="section-title">Encontre sua Comunidade</h2>
        <form action="/community_search.php" method="GET" class="search-form">
            <input type="text" name="q" placeholder="Digite o nome ou tema da comunidade" required>
            <button type="submit" class="btn">Buscar</button>
        </form>
    </section>
    
    <!-- Seção de Resumos de Comunidades -->
    <section class="container">
        <h2 class="section-title">Comunidades Populares</h2>
        <div class="communities-grid">
            <!-- Comunidade 1 -->
            <div class="community-card">
                <div class="card-img">📖</div>
                <div class="card-content">
                    <h3 class="card-title">Clube do Livro Clássicos</h3>
                    <p class="card-description">Discussões mensais sobre literatura clássica e seus impactos na sociedade contemporânea.</p>
                    <div class="card-stats">
                        <i>👤</i><span>256 membros</span>
                        <i>👁️</i><span>1.2k visualizações</span>
                    </div>
                    <div class="card-actions">
                        <a href="/comunidades/clube-livro-clasicos.php" class="btn">Ver Detalhes</a>
                        <a href="/comunidades/clube-livro-clasicos/entrar.php" class="btn">Participar</a>
                    </div>
                </div>
            </div>
            
            <!-- Comunidade 2 -->
            <div class="community-card">
                <div class="card-img">💻</div>
                <div class="card-content">
                    <h3 class="card-title">Fórum Tecnologia & Inovação</h3>
                    <p class="card-description">Debates sobre as últimas tendências e avanços tecnológicos que estão moldando o futuro.</p>
                    <div class="card-stats">
                        <i>👤</i><span>1.8k membros</span>
                        <i>👁️</i><span>5.3k visualizações</span>
                    </div>
                    <div class="card-actions">
                        <a href="/comunidades/forum-tecnologia.php" class="btn">Ver Detalhes</a>
                        <a href="/comunidades/forum-tecnologia/entrar.php" class="btn">Participar</a>
                    </div>
                </div>
            </div>
            
            <!-- Comunidade 3 -->
            <div class="community-card">
                <div class="card-img">🎨</div>
                <div class="card-content">
                    <h3 class="card-title">Clube de Arte Contemporânea</h3>
                    <p class="card-description">Espaço para artistas e entusiastas compartilharem trabalhos e discutirem técnicas artísticas.</p>
                    <div class="card-stats">
                        <i>👤</i><span>453 membros</span>
                        <i>👁️</i><span>2.1k visualizações</span>
                    </div>
                    <div class="card-actions">
                        <a href="/comunidades/arte-contemporanea.php" class="btn">Ver Detalhes</a>
                        <a href="/comunidades/arte-contemporanea/entrar.php" class="btn">Participar</a>
                    </div>
                </div>
            </div>
            
            <!-- Comunidade 4 -->
            <div class="community-card">
                <div class="card-img">🌱</div>
                <div class="card-content">
                    <h3 class="card-title">Sustentabilidade em Ação</h3>
                    <p class="card-description">Compartilhamento de práticas sustentáveis e discussões sobre meio ambiente e conservação.</p>
                    <div class="card-stats">
                        <i>👤</i><span>742 membros</span>
                        <i>👁️</i><span>3.5k visualizações</span>
                    </div>
                    <div class="card-actions">
                        <a href="/comunidades/sustentabilidade.php" class="btn">Ver Detalhes</a>
                        <a href="/comunidades/sustentabilidade/entrar.php" class="btn">Participar</a>
                    </div>
                </div>
            </div>
            
            <!-- Comunidade 5 -->
            <div class="community-card">
                <div class="card-img">🎮</div>
                <div class="card-content">
                    <h3 class="card-title">Gamers Unidos</h3>
                    <p class="card-description">Discussões sobre jogos, estratégias, lançamentos e organização de torneios online.</p>
                    <div class="card-stats">
                        <i>👤</i><span>2.3k membros</span>
                        <i>👁️</i><span>8.7k visualizações</span>
                    </div>
                    <div class="card-actions">
                        <a href="/comunidades/gamers-unidos.php" class="btn">Ver Detalhes</a>
                        <a href="/comunidades/gamers-unidos/entrar.php" class="btn">Participar</a>
                    </div>
                </div>
            </div>
            
            <!-- Comunidade 6 -->
            <div class="community-card">
                <div class="card-img">🧠</div>
                <div class="card-content">
                    <h3 class="card-title">Círculo Filosófico</h3>
                    <p class="card-description">Debates sobre filosofia clássica e contemporânea, ética e questões existenciais.</p>
                    <div class="card-stats">
                        <i>👤</i><span>389 membros</span>
                        <i>👁️</i><span>1.9k visualizações</span>
                    </div>
                    <div class="card-actions">
                        <a href="/comunidades/circulo-filosofico.php" class="btn">Ver Detalhes</a>
                        <a href="/comunidades/circulo-filosofico/entrar.php" class="btn">Participar</a>
                    </div>
                </div>
            </div>
        </div>
        <div class="view-more">
            <a href="/comunidades/todas.php" class="btn">Ver Todas as Comunidades</a>
        </div>
    </section>
    
    <!-- Seção de Destaques -->
    <section class="highlights container">
        <h2 class="section-title">Em Destaque</h2>
        
        <div class="tab-container">
            <button class="tab active" data-tab="mais-acessadas">Comunidades Mais Acessadas</button>
            <button class="tab" data-tab="clubes-leitura">Clubes de Leitura em Destaque</button>
        </div>
        
        <div class="carousel" id="mais-acessadas">
            <div class="carousel-container">
                <!-- Card 1 -->
                <div class="carousel-card community-card">
                    <div class="feature-badge">TOP 1</div>
                    <div class="card-img">🎮</div>
                    <div class="card-content">
                        <h3 class="card-title">Gamers Unidos</h3>
                        <p class="card-description">A maior comunidade de gamers com discussões diárias e eventos exclusivos.</p>
                        <div class="card-stats">
                            <i>👤</i><span>2.3k membros</span>
                            <i>👁️</i><span>8.7k visualizações</span>
                        </div>
                        <div class="card-actions">
                            <a href="/comunidades/gamers-unidos.php" class="btn">Ver Detalhes</a>
                            <a href="/comunidades/gamers-unidos/entrar.php" class="btn">Participar</a>
                        </div>
                    </div>
                </div>
                
                <!-- Card 2 -->
                <div class="carousel-card community-card">
                    <div class="feature-badge">TOP 2</div>
                    <div class="card-img">💻</div>
                    <div class="card-content">
                        <h3 class="card-title">Fórum Tecnologia & Inovação</h3>
                        <p class="card-description">Debates sobre as últimas tendências e avanços tecnológicos.</p>
                        <div class="card-stats">
                            <i>👤</i><span>1.8k membros</span>
                            <i>👁️</i><span>5.3k visualizações</span>
                        </div>
                        <div class="card-actions">
                            <a href="/comunidades/forum-tecnologia.php" class="btn">Ver Detalhes</a>
                            <a href="/comunidades/forum-tecnologia/entrar.php" class="btn">Participar</a>
                        </div>
                    </div>
                </div>
                
                <!-- Card 3 -->
                <div class="carousel-card community-card">
                    <div class="feature-badge">TOP 3</div>
                    <div class="card-img">🌱</div>
                    <div class="card-content">
                        <h3 class="card-title">Sustentabilidade em Ação</h3>
                        <p class="card-description">Compartilhamento de práticas sustentáveis e discussões sobre meio ambiente.</p>
                        <div class="card-stats">
                            <i>👤</i><span>742 membros</span>
                            <i>👁️</i><span>3.5k visualizações</span>
                        </div>
                        <div class="card-actions">
                            <a href="/comunidades/sustentabilidade.php" class="btn">Ver Detalhes</a>
                            <a href="/comunidades/sustentabilidade/entrar.php" class="btn">Participar</a>
                        </div>
                    </div>
                </div>
                
                <!-- Card 4 -->
                <div class="carousel-card community-card">
                    <div class="feature-badge">TOP 4</div>
                    <div class="card-img">🎨</div>
                    <div class="card-content">
                        <h3 class="card-title">Clube de Arte Contemporânea</h3>
                        <p class="card-description">Espaço para artistas e entusiastas compartilharem trabalhos e técnicas.</p>
                        <div class="card-stats">
                            <i>👤</i><span>453 membros</span>
                            <i>👁️</i><span>2.1k visualizações</span>
                        </div>
                        <div class="card-actions">
                            <a href="/comunidades/arte-contemporanea.php" class="btn">Ver Detalhes</a>
                            <a href="/comunidades/arte-contemporanea/entrar.php" class="btn">Participar</a>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="carousel-controls">
                <button id="prev">◀</button>
                <button id="next">▶</button>
            </div>
        </div>
        
        <!-- Seção de Clubes de Leitura (conteúdo alternativo para a tab) -->
        <div class="carousel" id="clubes-leitura" style="display:none;">
            <div class="carousel-container">
                <!-- Exemplo de Card de Clube de Leitura -->
                <div class="carousel-card community-card">
                    <div class="feature-badge">Destaque</div>
                    <div class="card-img">📚</div>
                    <div class="card-content">
                        <h3 class="card-title">Clube dos Amantes da Literatura</h3>
                        <p class="card-description">Encontros semanais para discutir obras literárias de diferentes gêneros.</p>
                        <div class="card-stats">
                            <i>👤</i><span>1.1k membros</span>
                            <i>👁️</i><span>4.2k visualizações</span>
                        </div>
                        <div class="card-actions">
                            <a href="/clubes/leitura/amantes-literatura.php" class="btn">Ver Detalhes</a>
                            <a href="/clubes/leitura/amantes-literatura/entrar.php" class="btn">Participar</a>
                        </div>
                    </div>
                </div>
                <!-- Você pode adicionar mais cards conforme necessário -->
            </div>
            
            <div class="carousel-controls">
                <button id="prev-leitura">◀</button>
                <button id="next-leitura">▶</button>
            </div>
        </div>
    </section>
    
    <!-- Seção de Acesso Rápido -->
    <section class="quick-access container">
        <h2 class="section-title">Acesso Rápido</h2>
        <div class="categories">
            <!-- Categoria 1 -->
            <div class="category">
                <div class="category-icon">👥</div>
                <h3>Clubes</h3>
                <p>Encontre grupos com interesses semelhantes aos seus e participe de atividades organizadas regularmente.</p>
                <a href="/clubes.php" class="btn">Acessar Clubes</a>
            </div>
            
            <!-- Categoria 2 -->
            <div class="category">
                <div class="category-icon">💬</div>
                <h3>Fóruns de Discussão</h3>
                <p>Participe de debates sobre diversos temas, compartilhe opiniões e aprenda com diferentes perspectivas.</p>
                <a href="/foruns.php" class="btn">Explorar Fóruns</a>
            </div>
            
            <!-- Categoria 3 -->
            <div class="category">
                <div class="category-icon">📚</div>
                <h3>Clubes de Leitura</h3>
                <p>Junte-se a apaixonados por literatura para discutir livros, autores e expandir seus horizontes literários.</p>
                <a href="/clubes/leitura.php" class="btn">Ver Clubes de Leitura</a>
            </div>
            
            <!-- Nova Categoria: Eventos -->
            <div class="category">
                <div class="category-icon">📅</div>
                <h3>Eventos</h3>
                <p>Fique por dentro de encontros, webinars e workshops organizados por nossas comunidades.</p>
                <a href="/eventos.php" class="btn">Ver Eventos</a>
            </div>
        </div>
    </section>
    
    <!-- Seção de Comunidade do Mês -->
    <section class="container">
        <h2 class="section-title">Comunidade do Mês</h2>
        <div class="featured-community">
            <div class="featured-img">🌟</div>
            <div class="featured-content">
                <h3>Clube dos Inovadores</h3>
                <p>Conheça a comunidade que tem se destacado por reunir mentes criativas e promover debates inspiradores sobre inovação e tecnologia.</p>
                <a href="/comunidades/clube-dos-inovadores.php" class="btn">Saiba Mais</a>
                <a href="/comunidades/clube-dos-inovadores/entrar.php" class="btn">Junte-se Agora</a>
            </div>
        </div>
    </section>
    
    <!-- Rodapé -->
    <footer>
        <div class="container footer-container">
            <!-- Coluna 1 -->
            <div class="footer-column">
                <h3>Central das Comunidades</h3>
                <p>Conectando pessoas com interesses em comum desde 2023. Nossa missão é criar um ambiente digital onde todos possam encontrar seu espaço.</p>
                <div class="social-icons">
                    <a href="https://facebook.com" target="_blank" class="social-icon">f</a>
                    <a href="https://twitter.com" target="_blank" class="social-icon">t</a>
                    <a href="https://linkedin.com" target="_blank" class="social-icon">in</a>
                    <a href="https://instagram.com" target="_blank" class="social-icon">ig</a>
                </div>
            </div>
            
            <!-- Coluna 2 -->
            <div class="footer-column">
                <h3>Links Úteis</h3>
                <ul>
                    <li><a href="/sobre.php">Sobre Nós</a></li>
                    <li><a href="/como-funciona.php">Como Funciona</a></li>
                    <li><a href="/contato.php">Contato</a></li>
                    <li><a href="/faq.php">FAQ</a></li>
                    <li><a href="/blog.php">Blog</a></li>
                </ul>
            </div>
            
            <!-- Coluna 3 -->
            <div class="footer-column">
                <h3>Políticas</h3>
                <ul>
                    <li><a href="/termos-de-uso.php">Termos de Uso</a></li>
                    <li><a href="/politica-de-privacidade.php">Política de Privacidade</a></li>
                    <li><a href="/politica-de-cookies.php">Política de Cookies</a></li>
                    <li><a href="/regras-da-comunidade.php">Regras da Comunidade</a></li>
                </ul>
            </div>
            
            <!-- Coluna 4 -->
            <div class="footer-column">
                <h3>Newsletter</h3>
                <p>Inscreva-se para receber atualizações sobre novas comunidades e eventos.</p>
                <form class="newsletter" action="/newsletter.php" method="POST">
                    <input type="email" name="email" placeholder="Seu e-mail" required>
                    <button type="submit" class="btn">Inscrever-se</button>
                </form>
            </div>
        </div>
        
        <div class="copyright container">
            <p>© 2025 Central das Comunidades. Todos os direitos reservados.</p>
            <a href="#top" class="btn">Voltar ao Topo</a>
        </div>
    </footer>
    
    <script>
        // Script para o carrossel e troca de tabs
        document.addEventListener('DOMContentLoaded', function() {
            const carousel = document.querySelector('.carousel-container');
            const cards = document.querySelectorAll('.carousel-card');
            const prevBtn = document.getElementById('prev');
            const nextBtn = document.getElementById('next');
            
            let position = 0;
            const cardWidth = 300 + 32; // largura do card + margin
            
            prevBtn.addEventListener('click', function() {
                if (position < 0) {
                    position += cardWidth;
                    carousel.style.transform = `translateX(${position}px)`;
                }
            });
            
            nextBtn.addEventListener('click', function() {
                if (position > -(cardWidth * (cards.length - 3))) {
                    position -= cardWidth;
                    carousel.style.transform = `translateX(${position}px)`;
                }
            });
            
            // Script para alternar entre as tabs de Destaques
            const tabs = document.querySelectorAll('.tab');
            const maisAcessadas = document.getElementById('mais-acessadas');
            const clubesLeitura = document.getElementById('clubes-leitura');
            
            tabs.forEach(tab => {
                tab.addEventListener('click', function() {
                    tabs.forEach(t => t.classList.remove('active'));
                    this.classList.add('active');
                    
                    if(this.getAttribute('data-tab') === 'mais-acessadas'){
                        maisAcessadas.style.display = 'block';
                        clubesLeitura.style.display = 'none';
                    } else {
                        maisAcessadas.style.display = 'none';
                        clubesLeitura.style.display = 'block';
                    }
                });
            });
            
            // Controles do carrossel para clubes de leitura (se houver mais de um)
            const prevLeitura = document.getElementById('prev-leitura');
            const nextLeitura = document.getElementById('next-leitura');
            const leituraCarousel = document.querySelector('#clubes-leitura .carousel-container');
            const leituraCards = document.querySelectorAll('#clubes-leitura .carousel-card');
            let leituraPos = 0;
            
            if(prevLeitura && nextLeitura){
                prevLeitura.addEventListener('click', function() {
                    if (leituraPos < 0) {
                        leituraPos += cardWidth;
                        leituraCarousel.style.transform = `translateX(${leituraPos}px)`;
                    }
                });
                
                nextLeitura.addEventListener('click', function() {
                    if (leituraPos > -(cardWidth * (leituraCards.length - 3))) {
                        leituraPos -= cardWidth;
                        leituraCarousel.style.transform = `translateX(${leituraPos}px)`;
                    }
                });
            }
        });
    </script>
</body>
</html>
