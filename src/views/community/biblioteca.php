<?php require_once BASE_PATH . '/src/views/partials/header.php'; ?>

<head>
    <link rel="stylesheet" href="/assets/css/pages/biblioteca.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body>
    <header>
        <h1>Biblioteca Virtual</h1>
    </header>

    <main>
        <!-- Seção de Banners -->
        <section class="banner-section">
            <div class="banner-container">
                <div class="banner active">
                    <div class="banner-content">
                        <h2>Descubra Novos Mundos</h2>
                        <p>Explore nossa coleção de livros clássicos e contemporâneos</p>
                        <button class="explore-btn">Explorar</button>
                    </div>
                    <img src="/api/placeholder/1200/400" alt="Banner de livros">
                </div>
                <div class="banner">
                    <div class="banner-content">
                        <h2>Promoção Especial</h2>
                        <p>Confira nossos livros em promoção com até 50% de desconto</p>
                        <button class="explore-btn">Ver Ofertas</button>
                    </div>
                    <img src="/api/placeholder/1200/400" alt="Banner de promoção">
                </div>
                <div class="banner">
                    <div class="banner-content">
                        <h2>Autores Nacionais</h2>
                        <p>Conheça o melhor da literatura brasileira</p>
                        <button class="explore-btn">Descobrir</button>
                    </div>
                    <img src="/api/placeholder/1200/400" alt="Banner de autores nacionais">
                </div>
            </div>
            <button class="banner-nav prev"><i class="fas fa-chevron-left"></i></button>
            <button class="banner-nav next"><i class="fas fa-chevron-right"></i></button>
            <div class="banner-indicators">
                <span class="indicator active"></span>
                <span class="indicator"></span>
                <span class="indicator"></span>
            </div>
        </section>

        <!-- Seção de Categorias Gerais -->
        <section class="categories-section">
            <h2>Categorias</h2>
            <div class="categories-container">
                <div class="category-card">
                    <img src="https://th.bing.com/th/id/OIP.CvGGIiX2DoVbKVDEhM3lmwHaEK?w=303&h=180&c=7&r=0&o=5&pid=1.7" alt="Ficção">
                    <h3>Ficção</h3>
                    <p>Romance, Fantasia, Ficção Científica, Terror e mais</p>
                    <button class="explore-btn">Explorar</button>
                </div>
                <div class="category-card">
                    <img src="/api/placeholder/400/250" alt="Não-Ficção">
                    <h3>Não-Ficção</h3>
                    <p>Biografia, História, Ciência, Autoajuda e mais</p>
                    <button class="explore-btn">Explorar</button>
                </div>
                <div class="category-card">
                    <img src="/api/placeholder/400/250" alt="Infanto-Juvenil">
                    <h3>Infanto-Juvenil</h3>
                    <p>Infantil, Juvenil, Educativo, Quadrinhos e mais</p>
                    <button class="explore-btn">Explorar</button>
                </div>
            </div>
        </section>

        <!-- Seção de Livros em Destaque -->
        <section class="featured-books-section">
            <h2>Livros em Destaque</h2>
            <div class="featured-books-container">
                <div class="book-grid">
                    <div class="book-card">
                        <div class="book-cover">
                            <img src="/api/placeholder/180/270" alt="Livro 1">
                        </div>
                        <div class="book-details">
                            <h3>O Senhor dos Anéis</h3>
                            <p class="author">J.R.R. Tolkien</p>
                            <div class="book-actions">
                                <button class="view-details-btn">Ver Detalhes</button>
                                <button class="favorite-btn"><i class="far fa-heart"></i></button>
                            </div>
                        </div>
                    </div>
                    <div class="book-card">
                        <div class="book-cover">
                            <img src="/api/placeholder/180/270" alt="Livro 2">
                        </div>
                        <div class="book-details">
                            <h3>1984</h3>
                            <p class="author">George Orwell</p>
                            <div class="book-actions">
                                <button class="view-details-btn">Ver Detalhes</button>
                                <button class="favorite-btn"><i class="far fa-heart"></i></button>
                            </div>
                        </div>
                    </div>
                    <div class="book-card">
                        <div class="book-cover">
                            <img src="/api/placeholder/180/270" alt="Livro 3">
                        </div>
                        <div class="book-details">
                            <h3>Dom Casmurro</h3>
                            <p class="author">Machado de Assis</p>
                            <div class="book-actions">
                                <button class="view-details-btn">Ver Detalhes</button>
                                <button class="favorite-btn"><i class="far fa-heart"></i></button>
                            </div>
                        </div>
                    </div>
                    <div class="book-card">
                        <div class="book-cover">
                            <img src="/api/placeholder/180/270" alt="Livro 4">
                        </div>
                        <div class="book-details">
                            <h3>Harry Potter</h3>
                            <p class="author">J.K. Rowling</p>
                            <div class="book-actions">
                                <button class="view-details-btn">Ver Detalhes</button>
                                <button class="favorite-btn"><i class="far fa-heart"></i></button>
                            </div>
                        </div>
                    </div>
                    <div class="book-card">
                        <div class="book-cover">
                            <img src="/api/placeholder/180/270" alt="Livro 5">
                        </div>
                        <div class="book-details">
                            <h3>Cem Anos de Solidão</h3>
                            <p class="author">Gabriel García Márquez</p>
                            <div class="book-actions">
                                <button class="view-details-btn">Ver Detalhes</button>
                                <button class="favorite-btn"><i class="far fa-heart"></i></button>
                            </div>
                        </div>
                    </div>
                    <div class="book-card">
                        <div class="book-cover">
                            <img src="/api/placeholder/180/270" alt="Livro 6">
                        </div>
                        <div class="book-details">
                            <h3>A Metamorfose</h3>
                            <p class="author">Franz Kafka</p>
                            <div class="book-actions">
                                <button class="view-details-btn">Ver Detalhes</button>
                                <button class="favorite-btn"><i class="far fa-heart"></i></button>
                            </div>
                        </div>
                    </div>
                    <div class="book-card">
                        <div class="book-cover">
                            <img src="/api/placeholder/180/270" alt="Livro 7">
                        </div>
                        <div class="book-details">
                            <h3>O Pequeno Príncipe</h3>
                            <p class="author">Antoine de Saint-Exupéry</p>
                            <div class="book-actions">
                                <button class="view-details-btn">Ver Detalhes</button>
                                <button class="favorite-btn"><i class="far fa-heart"></i></button>
                            </div>
                        </div>
                    </div>
                    <div class="book-card">
                        <div class="book-cover">
                            <img src="/api/placeholder/180/270" alt="Livro 8">
                        </div>
                        <div class="book-details">
                            <h3>Crime e Castigo</h3>
                            <p class="author">Fiódor Dostoiévski</p>
                            <div class="book-actions">
                                <button class="view-details-btn">Ver Detalhes</button>
                                <button class="favorite-btn"><i class="far fa-heart"></i></button>
                            </div>
                        </div>
                    </div>
                    <div class="book-card">
                        <div class="book-cover">
                            <img src="/api/placeholder/180/270" alt="Livro 9">
                        </div>
                        <div class="book-details">
                            <h3>A Revolução dos Bichos</h3>
                            <p class="author">George Orwell</p>
                            <div class="book-actions">
                                <button class="view-details-btn">Ver Detalhes</button>
                                <button class="favorite-btn"><i class="far fa-heart"></i></button>
                            </div>
                        </div>
                    </div>
                    <div class="book-card">
                        <div class="book-cover">
                            <img src="/api/placeholder/180/270" alt="Livro 10">
                        </div>
                        <div class="book-details">
                            <h3>Orgulho e Preconceito</h3>
                            <p class="author">Jane Austen</p>
                            <div class="book-actions">
                                <button class="view-details-btn">Ver Detalhes</button>
                                <button class="favorite-btn"><i class="far fa-heart"></i></button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="pagination">
                    <button class="page-btn active">1</button>
                    <button class="page-btn">2</button>
                    <button class="page-btn">3</button>
                </div>
            </div>
        </section>

        <!-- Seção de Livros Regulares -->
        <section class="books-section">
            <h2>Livros Recomendados</h2>
            <div class="books-container">
                <div class="book-card">
                    <div class="book-cover">
                        <img src="/api/placeholder/180/270" alt="Livro Recomendado 1">
                    </div>
                    <div class="book-details">
                        <h3>Memórias Póstumas de Brás Cubas</h3>
                        <p class="author">Machado de Assis</p>
                        <div class="book-actions">
                            <button class="view-details-btn">Ver Detalhes</button>
                            <button class="favorite-btn"><i class="far fa-heart"></i></button>
                        </div>
                    </div>
                </div>
                <div class="book-card">
                    <div class="book-cover">
                        <img src="/api/placeholder/180/270" alt="Livro Recomendado 2">
                    </div>
                    <div class="book-details">
                        <h3>Grande Sertão: Veredas</h3>
                        <p class="author">João Guimarães Rosa</p>
                        <div class="book-actions">
                            <button class="view-details-btn">Ver Detalhes</button>
                            <button class="favorite-btn"><i class="far fa-heart"></i></button>
                        </div>
                    </div>
                </div>
                <div class="book-card">
                    <div class="book-cover">
                        <img src="/api/placeholder/180/270" alt="Livro Recomendado 3">
                    </div>
                    <div class="book-details">
                        <h3>Vidas Secas</h3>
                        <p class="author">Graciliano Ramos</p>
                        <div class="book-actions">
                            <button class="view-details-btn">Ver Detalhes</button>
                            <button class="favorite-btn"><i class="far fa-heart"></i></button>
                        </div>
                    </div>
                </div>
                <div class="book-card">
                    <div class="book-cover">
                        <img src="/api/placeholder/180/270" alt="Livro Recomendado 4">
                    </div>
                    <div class="book-details">
                        <h3>A Hora da Estrela</h3>
                        <p class="author">Clarice Lispector</p>
                        <div class="book-actions">
                            <button class="view-details-btn">Ver Detalhes</button>
                            <button class="favorite-btn"><i class="far fa-heart"></i></button>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <footer>
        <p>&copy; 2025 Biblioteca Virtual. Todos os direitos reservados.</p>
    </footer>

    <script src="/assets/js/pages/biblioteca.js"></script>
</body>

</html>