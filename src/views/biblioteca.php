<?php
// Supondo que BASE_PATH esteja definida e o autoload/configuração já esteja no index.php
require_once BASE_PATH . '/src/views/partials/header.php';

// Observação: A conexão com o banco já foi realizada no controlador e os dados
// (ex.: $books) foram passados para esta view.
?>
<head>
    <link rel="stylesheet" href="/css/style.css">
</head>
<main>
    <div class="container">
        <div class="hero">
            <div class="hero-content">
                <h2>Biblioteca Digital</h2>
                <p>Explore nosso acervo com milhares de títulos em diversos formatos e categorias</p>
            </div>
        </div>

        <div class="search-section">
            <div class="search-bar">
                <input type="text" placeholder="Pesquisar por título, autor, tema...">
                <button><i class="fas fa-search"></i></button>
            </div>

            <div class="category-tabs">
                <div class="category-tab active" data-category="literario">Literário</div>
                <div class="category-tab" data-category="academico">Acadêmico</div>
                <div class="category-tab" data-category="todos">Todos os Livros</div>
            </div>

            <!-- Subcategorias Literárias -->
            <div class="subcategories active" id="subcategorias-literario">
                <button class="subcategory-btn active">Todos</button>
                <button class="subcategory-btn">Romance</button>
                <button class="subcategory-btn">Poesia</button>
                <button class="subcategory-btn">Contos</button>
                <button class="subcategory-btn">Drama</button>
                <button class="subcategory-btn">Ficção Científica</button>
                <button class="subcategory-btn">Fantasia</button>
                <button class="subcategory-btn">Suspense</button>
                <button class="subcategory-btn">Terror</button>
            </div>

            <!-- Subcategorias Acadêmicas -->
            <div class="subcategories" id="subcategorias-academico">
                <button class="subcategory-btn active">Todos</button>
                <button class="subcategory-btn">Filosofia</button>
                <button class="subcategory-btn">História</button>
                <button class="subcategory-btn">Sociologia</button>
                <button class="subcategory-btn">Psicologia</button>
                <button class="subcategory-btn">Direito</button>
                <button class="subcategory-btn">Ciências Exatas</button>
                <button class="subcategory-btn">Ciências Biológicas</button>
                <button class="subcategory-btn">Medicina</button>
                <button class="subcategory-btn">Educação</button>
            </div>
        </div>

        <div class="content-section">
            <div class="section-header">
                <h2 class="section-title">Livros Literários</h2>
                <div class="view-options">
                    <div class="view-btn active" title="Visualização em lista">
                        <i class="fas fa-list"></i>
                    </div>
                    <div class="view-btn" title="Visualização em grade">
                        <i class="fas fa-th-large"></i>
                    </div>
                </div>
            </div>

            <div class="book-list">
                <?php if (!empty($books)): ?>
                    <?php foreach ($books as $book): ?>
                        <div class="book-card">
                            <div class="book-cover">
                                <!-- Verifica se há imagem cadastrada, caso contrário usa um placeholder -->
                                <img src="<?= !empty($book['cover_image']) ? $book['cover_image'] : '/api/placeholder/140/200' ?>" alt="<?= htmlspecialchars($book['title']) ?>">
                            </div>
                            <div class="book-details">
                                <h3 class="book-title"><?= htmlspecialchars($book['title']) ?></h3>
                                <p class="book-author"><?= htmlspecialchars($book['author']) ?></p>
                                <div class="book-description">
                                    <?= nl2br(htmlspecialchars($book['description'])) ?>
                                </div>
                                <div class="book-meta">
                                    <span class="book-status <?= $book['status'] == 'Disponível' ? 'status-available' : 'status-unavailable' ?>">
                                        <?= $book['status'] ?>
                                    </span>
                                    <span><?= htmlspecialchars($book['year']) ?></span>
                                </div>
                                <div class="book-actions">
                                    <?php if ($book['status'] == 'Disponível'): ?>
                                        <button class="book-btn">Reservar</button>
                                    <?php endif; ?>
                                    <button class="book-btn outline">Detalhes</button>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p>Nenhum livro encontrado.</p>
                <?php endif; ?>
            </div>

            <div class="pagination">
                <div class="page-item"><i class="fas fa-chevron-left"></i></div>
                <div class="page-item active">1</div>
                <div class="page-item">2</div>
                <div class="page-item">3</div>
                <div class="page-item">4</div>
                <div class="page-item">5</div>
                <div class="page-item"><i class="fas fa-chevron-right"></i></div>
            </div>
        </div>
    </div>
</main>

<?php require_once BASE_PATH . '/src/views/partials/footer.php'; ?>
