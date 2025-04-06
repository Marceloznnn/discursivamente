<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Clube de Livros - Onde a Leitura Ganha Vida</title>
    <style>
        :root {
            --primary-color: #7b5d34;
            --secondary-color: #e6dfd1;
            --accent-color: #a67c52;
            --text-color: #333333;
            --light-text: #f5f5f5;
            --bg-color: #f9f7f4;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Georgia', serif;
        }
        
        body {
            background-color: var(--bg-color);
            color: var(--text-color);
            line-height: 1.6;
        }
        
        /* Header */
        header {
            background-color: white;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            position: sticky;
            top: 0;
            z-index: 100;
        }
        
        .header-container {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1rem 5%;
            max-width: 1200px;
            margin: 0 auto;
        }
        
        .logo {
            display: flex;
            align-items: center;
        }
        
        .logo img {
            height: 50px;
            margin-right: 10px;
        }
        
        .logo h1 {
            font-size: 1.8rem;
            color: var(--primary-color);
        }
        
        nav ul {
            display: flex;
            list-style: none;
        }
        
        nav ul li {
            margin-left: 1.5rem;
        }
        
        nav ul li a {
            text-decoration: none;
            color: var(--text-color);
            font-weight: 500;
            transition: color 0.3s;
        }
        
        nav ul li a:hover {
            color: var(--accent-color);
        }
        
        .search-bar {
            display: flex;
            align-items: center;
            margin-left: 1.5rem;
        }
        
        .search-bar input {
            padding: 0.5rem;
            border: 1px solid #ddd;
            border-radius: 20px;
            width: 200px;
        }
        
        .mobile-menu {
            display: none;
            cursor: pointer;
            font-size: 1.5rem;
        }
        
        /* Banner */
        .banner {
            background-image: url('/api/placeholder/1200/400');
            background-size: cover;
            background-position: center;
            height: 400px;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            color: white;
            position: relative;
        }
        
        .banner::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.5);
        }
        
        .banner-content {
            position: relative;
            max-width: 800px;
            padding: 0 2rem;
        }
        
        .banner h2 {
            font-size: 2.5rem;
            margin-bottom: 1rem;
        }
        
        .banner p {
            font-size: 1.2rem;
            margin-bottom: 2rem;
        }
        
        .cta-button {
            background-color: var(--primary-color);
            color: white;
            padding: 0.8rem 2rem;
            border: none;
            border-radius: 30px;
            font-size: 1rem;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        
        .cta-button:hover {
            background-color: var(--accent-color);
        }
        
        /* Main Content */
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 3rem 2rem;
        }
        
        section {
            margin-bottom: 4rem;
        }
        
        .section-title {
            font-size: 2rem;
            color: var(--primary-color);
            margin-bottom: 2rem;
            position: relative;
            padding-bottom: 0.5rem;
        }
        
        .section-title::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 100px;
            height: 3px;
            background-color: var(--accent-color);
        }
        
        /* Events Section */
        .events-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 2rem;
        }
        
        .event-card {
            background-color: white;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 3px 10px rgba(0,0,0,0.1);
            transition: transform 0.3s;
        }
        
        .event-card:hover {
            transform: translateY(-5px);
        }
        
        .event-date {
            background-color: var(--primary-color);
            color: white;
            padding: 0.5rem;
            text-align: center;
        }
        
        .event-content {
            padding: 1.5rem;
        }
        
        .event-content h3 {
            margin-bottom: 0.5rem;
            color: var(--primary-color);
        }
        
        .event-content p {
            margin-bottom: 1rem;
            color: #666;
        }
        
        .calendar {
            margin-top: 2rem;
            padding: 1.5rem;
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 3px 10px rgba(0,0,0,0.1);
        }
        
        /* Books Section */
        .books-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
            gap: 2rem;
        }
        
        .book-card {
            background-color: white;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 3px 10px rgba(0,0,0,0.1);
            transition: transform 0.3s;
        }
        
        .book-card:hover {
            transform: translateY(-5px);
        }
        
        .book-cover {
            height: 300px;
            background-color: #eee;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .book-cover img {
            max-height: 100%;
            max-width: 100%;
        }
        
        .book-info {
            padding: 1.5rem;
        }
        
        .book-info h3 {
            font-size: 1.2rem;
            margin-bottom: 0.5rem;
            color: var(--primary-color);
        }
        
        .book-info p {
            font-size: 0.9rem;
            margin-bottom: 1rem;
            color: #666;
        }
        
        .book-button {
            background-color: var(--secondary-color);
            color: var(--primary-color);
            padding: 0.5rem 1rem;
            border: none;
            border-radius: 20px;
            font-size: 0.9rem;
            cursor: pointer;
            transition: background-color 0.3s;
            display: inline-block;
        }
        
        .book-button:hover {
            background-color: var(--accent-color);
            color: white;
        }
        
        /* Discussions Section */
        .discussions {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 2rem;
        }
        
        .discussion-card {
            background-color: white;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 3px 10px rgba(0,0,0,0.1);
            padding: 1.5rem;
        }
        
        .discussion-header {
            display: flex;
            align-items: center;
            margin-bottom: 1rem;
        }
        
        .discussion-header img {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            margin-right: 1rem;
        }
        
        .discussion-meta h3 {
            font-size: 1.1rem;
            margin-bottom: 0.2rem;
        }
        
        .discussion-meta span {
            font-size: 0.8rem;
            color: #888;
        }
        
        .discussion-content p {
            margin-bottom: 1rem;
            font-size: 0.95rem;
        }
        
        .discussion-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 0.9rem;
            color: #888;
        }
        
        /* Members Section */
        .members-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 2rem;
        }
        
        .member-card {
            background-color: white;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 3px 10px rgba(0,0,0,0.1);
            text-align: center;
            padding: 1.5rem;
        }
        
        .member-avatar {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            margin: 0 auto 1rem;
            overflow: hidden;
            background-color: #eee;
        }
        
        .member-avatar img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        
        .member-card h3 {
            font-size: 1.2rem;
            margin-bottom: 0.5rem;
            color: var(--primary-color);
        }
        
        .member-card p {
            font-size: 0.9rem;
            color: #666;
            margin-bottom: 1rem;
        }
        
        /* Footer */
        footer {
            background-color: var(--primary-color);
            color: white;
            padding: 3rem 0;
        }
        
        .footer-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 2rem;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 2rem;
        }
        
        .footer-section h3 {
            font-size: 1.3rem;
            margin-bottom: 1rem;
            position: relative;
            padding-bottom: 0.5rem;
        }
        
        .footer-section h3::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 50px;
            height: 2px;
            background-color: var(--secondary-color);
        }
        
        .footer-links {
            list-style: none;
        }
        
        .footer-links li {
            margin-bottom: 0.5rem;
        }
        
        .footer-links a {
            color: var(--secondary-color);
            text-decoration: none;
            transition: color 0.3s;
        }
        
        .footer-links a:hover {
            color: white;
        }
        
        .social-links {
            display: flex;
            gap: 1rem;
            margin-top: 1rem;
        }
        
        .social-links a {
            color: white;
            background-color: rgba(255,255,255,0.2);
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            text-decoration: none;
            transition: background-color 0.3s;
        }
        
        .social-links a:hover {
            background-color: rgba(255,255,255,0.4);
        }
        
        .newsletter input {
            width: 100%;
            padding: 0.8rem;
            border: none;
            border-radius: 4px;
            margin-bottom: 1rem;
        }
        
        .newsletter button {
            background-color: var(--accent-color);
            color: white;
            padding: 0.8rem;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            width: 100%;
            transition: background-color 0.3s;
        }
        
        .newsletter button:hover {
            background-color: var(--secondary-color);
            color: var(--primary-color);
        }
        
        .copyright {
            text-align: center;
            padding-top: 2rem;
            margin-top: 2rem;
            border-top: 1px solid rgba(255,255,255,0.1);
            font-size: 0.9rem;
        }
        
        /* Responsive */
        @media (max-width: 768px) {
            .header-container {
                flex-direction: column;
                padding: 1rem;
            }
            
            .logo {
                margin-bottom: 1rem;
            }
            
            nav ul {
                display: none;
                flex-direction: column;
                width: 100%;
            }
            
            nav ul.active {
                display: flex;
            }
            
            nav ul li {
                margin: 0.5rem 0;
                text-align: center;
            }
            
            .search-bar {
                margin: 1rem 0;
                width: 100%;
                justify-content: center;
            }
            
            .search-bar input {
                width: 100%;
            }
            
            .mobile-menu {
                display: block;
                position: absolute;
                top: 1.5rem;
                right: 1.5rem;
            }
            
            .banner {
                height: 300px;
            }
            
            .banner h2 {
                font-size: 1.8rem;
            }
            
            .banner p {
                font-size: 1rem;
            }
        }
    </style>
</head>
<body>
    <!-- Header -->
    <header>
        <div class="header-container">
            <div class="logo">
                <img src="/api/placeholder/50/50" alt="Logo do Clube de Livros">
                <h1>Clube de Livros</h1>
            </div>
            <nav>
                <ul>
                    <li><a href="#inicio">Início</a></li>
                    <li><a href="#agenda">Agenda de Leituras</a></li>
                    <li><a href="#discussoes">Discussões</a></li>
                    <li><a href="#resenhas">Resenhas</a></li>
                    <li><a href="#membros">Membros</a></li>
                    <li><a href="#contato">Contato</a></li>
                </ul>
                <div class="mobile-menu">☰</div>
            </nav>
            <div class="search-bar">
                <input type="text" placeholder="Buscar livros, tópicos ou eventos...">
            </div>
        </div>
    </header>

    <!-- Banner -->
    <section class="banner" id="inicio">
        <div class="banner-content">
            <h2>Bem-vindo ao Clube de Livros</h2>
            <p>Onde a Leitura Ganha Vida - Junte-se a nós para descobrir novos mundos através das palavras</p>
            <button class="cta-button">Participe Agora</button>
        </div>
    </section>

    <!-- Main Content -->
    <div class="container">
        <!-- Events Section -->
        <section id="agenda">
            <h2 class="section-title">Agenda e Eventos</h2>
            <div class="events-grid">
                <div class="event-card">
                    <div class="event-date">
                        <p>15 de Abril, 2025</p>
                        <p>19:00</p>
                    </div>
                    <div class="event-content">
                        <h3>Discussão: "Os Miseráveis"</h3>
                        <p>Vamos explorar a obra prima de Victor Hugo, discutindo temas de justiça social e redenção.</p>
                        <button class="book-button">Participar</button>
                    </div>
                </div>
                <div class="event-card">
                    <div class="event-date">
                        <p>22 de Abril, 2025</p>
                        <p>18:30</p>
                    </div>
                    <div class="event-content">
                        <h3>Lançamento: "Através do Espelho"</h3>
                        <p>Encontro especial com a autora Marina Cardoso para discussão do seu novo romance.</p>
                        <button class="book-button">Participar</button>
                    </div>
                </div>
                <div class="event-card">
                    <div class="event-date">
                        <p>30 de Abril, 2025</p>
                        <p>20:00</p>
                    </div>
                    <div class="event-content">
                        <h3>Workshop de Escrita Criativa</h3>
                        <p>Aprenda técnicas de escrita narrativa com o escritor Paulo Mendes.</p>
                        <button class="book-button">Participar</button>
                    </div>
                </div>
            </div>
            <div class="calendar">
                <h3>Calendário de Eventos</h3>
                <p>Visualize todos os eventos programados para o mês e organize sua agenda literária.</p>
                <!-- Aqui entraria um calendário interativo -->
            </div>
        </section>

        <!-- Books Section -->
        <section id="leituras">
            <h2 class="section-title">Livros em Destaque</h2>
            <div class="books-grid">
                <div class="book-card">
                    <div class="book-cover">
                        <img src="/api/placeholder/200/300" alt="Capa do livro">
                    </div>
                    <div class="book-info">
                        <h3>Os Miseráveis</h3>
                        <p>Victor Hugo</p>
                        <p>⭐⭐⭐⭐⭐ (4.8)</p>
                        <button class="book-button">Ver Detalhes</button>
                    </div>
                </div>
                <div class="book-card">
                    <div class="book-cover">
                        <img src="/api/placeholder/200/300" alt="Capa do livro">
                    </div>
                    <div class="book-info">
                        <h3>Através do Espelho</h3>
                        <p>Marina Cardoso</p>
                        <p>⭐⭐⭐⭐ (4.2)</p>
                        <button class="book-button">Ver Detalhes</button>
                    </div>
                </div>
                <div class="book-card">
                    <div class="book-cover">
                        <img src="/api/placeholder/200/300" alt="Capa do livro">
                    </div>
                    <div class="book-info">
                        <h3>Cem Anos de Solidão</h3>
                        <p>Gabriel García Márquez</p>
                        <p>⭐⭐⭐⭐⭐ (4.7)</p>
                        <button class="book-button">Ver Detalhes</button>
                    </div>
                </div>
                <div class="book-card">
                    <div class="book-cover">
                        <img src="/api/placeholder/200/300" alt="Capa do livro">
                    </div>
                    <div class="book-info">
                        <h3>O Processo</h3>
                        <p>Franz Kafka</p>
                        <p>⭐⭐⭐⭐ (4.5)</p>
                        <button class="book-button">Ver Detalhes</button>
                    </div>
                </div>
            </div>
        </section>

        <!-- Discussions Section -->
        <section id="discussoes">
            <h2 class="section-title">Discussões e Resenhas</h2>
            <div class="discussions">
                <div class="discussion-card">
                    <div class="discussion-header">
                        <img src="/api/placeholder/40/40" alt="Avatar">
                        <div class="discussion-meta">
                            <h3>Carlos Oliveira</h3>
                            <span>Há 3 dias</span>
                        </div>
                    </div>
                    <div class="discussion-content">
                        <p>"Os Miseráveis é uma obra que transcende seu tempo. A forma como Victor Hugo retrata os dilemas morais de Jean Valjean continua profundamente relevante nos dias de hoje..."</p>
                    </div>
                    <div class="discussion-footer">
                        <span>12 comentários</span>
                        <span>32 curtidas</span>
                    </div>
                </div>
                <div class="discussion-card">
                    <div class="discussion-header">
                        <img src="/api/placeholder/40/40" alt="Avatar">
                        <div class="discussion-meta">
                            <h3>Ana Beatriz</h3>
                            <span>Há 5 dias</span>
                        </div>
                    </div>
                    <div class="discussion-content">
                        <p>"Cem Anos de Solidão é uma jornada mágica através de gerações. A narrativa não-linear de García Márquez cria um universo próprio onde o fantástico e o ordinário se fundem..."</p>
                    </div>
                    <div class="discussion-footer">
                        <span>8 comentários</span>
                        <span>27 curtidas</span>
                    </div>
                </div>
                <div class="discussion-card">
                    <div class="discussion-header">
                        <img src="/api/placeholder/40/40" alt="Avatar">
                        <div class="discussion-meta">
                            <h3>Pedro Mendes</h3>
                            <span>Há 7 dias</span>
                        </div>
                    </div>
                    <div class="discussion-content">
                        <p>"O Processo de Kafka nos confronta com o absurdo da burocracia moderna. A sensação de impotência do protagonista diante de um sistema incompreensível reflete muitas de nossas frustações cotidianas..."</p>
                    </div>
                    <div class="discussion-footer">
                        <span>15 comentários</span>
                        <span>41 curtidas</span>
                    </div>
                </div>
            </div>
        </section>

        <!-- Members Section -->
        <section id="membros">
            <h2 class="section-title">Nossos Membros</h2>
            <div class="members-grid">
                <div class="member-card">
                    <div class="member-avatar">
                        <img src="/api/placeholder/100/100" alt="Avatar">
                    </div>
                    <h3>Carlos Oliveira</h3>
                    <p>Amante de clássicos e ficção histórica</p>
                    <button class="book-button">Ver Perfil</button>
                </div>
                <div class="member-card">
                    <div class="member-avatar">
                        <img src="/api/placeholder/100/100" alt="Avatar">
                    </div>
                    <h3>Ana Beatriz</h3>
                    <p>Fascinada por realismo fantástico e poesia</p>
                    <button class="book-button">Ver Perfil</button>
                </div>
                <div class="member-card">
                    <div class="member-avatar">
                        <img src="/api/placeholder/100/100" alt="Avatar">
                    </div>
                    <h3>Pedro Mendes</h3>
                    <p>Especialista em literatura existencialista</p>
                    <button class="book-button">Ver Perfil</button>
                </div>
                <div class="member-card">
                    <div class="member-avatar">
                        <img src="/api/placeholder/100/100" alt="Avatar">
                    </div>
                    <h3>Juliana Costa</h3>
                    <p>Entusiasta de literatura contemporânea</p>
                    <button class="book-button">Ver Perfil</button>
                </div>
            </div>
        </section>
    </div>

    <!-- Footer -->
    <footer>
        <div class="footer-container">
            <div class="footer-section">
                <h3>Sobre o Clube</h3>
                <p>O Clube de Livros é um espaço dedicado aos amantes da literatura, onde podemos compartilhar ideias, descobrir novas obras e crescer como leitores.</p>
            </div>
            <div class="footer-section">
                <h3>Links Úteis</h3>
                <ul class="footer-links">
                    <li><a href="#">Política de Privacidade</a></li>
                    <li><a href="#">Termos de Uso</a></li>
                    <li><a href="#">FAQ</a></li>
                    <li><a href="#">Contato</a></li>
                </ul>
            </div>
            <div class="footer-section">
                <h3>Siga-nos</h3>
                <div class="social-links">
                    <a href="#">F</a>
                    <a href="#">I</a>
                    <a href="#">T</a>
                    <a href="#">Y</a>
                </div>
            </div>
            <div class="footer-section">
                <h3>Newsletter</h3>
                <div class="newsletter">
                    <input type="email" placeholder="Seu e-mail">
                    <button>Inscrever-se</button>
                </div>
            </div>
        </div>
        <div class="copyright">
            <p>&copy; 2025 Clube de Livros. Todos os direitos reservados.</p>
        </div>
    </footer>

    <script>
        // Simple mobile menu toggle
        document.querySelector('.mobile-menu').addEventListener('click', function() {
            document.querySelector('nav ul').classList.toggle('active');
        });
    </script>
</body>
</html>