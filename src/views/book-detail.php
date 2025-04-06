<?php
// Supondo que BASE_PATH seja definida no arquivo de configuração ou no index.php
require_once BASE_PATH . '/src/views/partials/header.php';

// Simulação de dados do livro (em produção, esses dados viriam do banco de dados)
$livro = [
    'id' => 1,
    'titulo' => 'Memórias Póstumas de Brás Cubas',
    'autor' => 'Machado de Assis',
    'capa' => '/api/placeholder/300/450',
    'ano' => '1881',
    'paginas' => 256,
    'editora' => 'Companhia das Letras',
    'idioma' => 'Português',
    'isbn' => '9788574801919',
    'categoria' => 'Literário',
    'subcategoria' => 'Romance',
    'status' => 'Disponível',
    'sinopse' => 'Romance narrado por um defunto autor, que conta sua história após a morte, de forma irônica e questionando valores sociais da época. Brás Cubas, após morrer, decide narrar suas memórias, começando pelo seu fim e terminando no seu nascimento, revelando todas as contradições de sua personalidade e da sociedade brasileira do século XIX.',
    'biografia_autor' => 'Joaquim Maria Machado de Assis (1839-1908) foi um escritor brasileiro, considerado por muitos críticos, estudiosos, escritores e leitores o maior nome da literatura brasileira. Escreveu em praticamente todos os gêneros literários, sendo poeta, romancista, cronista, dramaturgo, contista, folhetinista, jornalista e crítico literário.',
    'avaliacoes' => [
        ['usuario' => 'João Silva', 'estrelas' => 5, 'comentario' => 'Obra-prima absoluta da literatura brasileira. A ironia e o estilo único de Machado tornam este livro atemporal.'],
        ['usuario' => 'Maria Souza', 'estrelas' => 4, 'comentario' => 'Um clássico que merece ser lido e relido. A narrativa não-linear é fascinante.'],
        ['usuario' => 'Pedro Santos', 'estrelas' => 5, 'comentario' => 'Leitura essencial para entender a literatura brasileira e o realismo psicológico.']
    ],
    'exemplares' => 3,
    'emprestados' => 0,
    'reservados' => 0,
    'tags' => ['Realismo', 'Literatura Brasileira', 'Século XIX', 'Clássico', 'Romance'],
    'citacoes_famosas' => [
        'Ao verme que primeiro roeu as frias carnes do meu cadáver dedico como saudosa lembrança estas memórias póstumas.',
        'Marcela amou-me durante quinze meses e onze contos de réis.',
        'Não tive filhos, não transmiti a nenhuma criatura o legado da nossa miséria.'
    ]
];

// Livros relacionados (também viriam do banco de dados em um ambiente real)
$livros_relacionados = [
    [
        'id' => 2,
        'titulo' => 'Dom Casmurro',
        'autor' => 'Machado de Assis',
        'capa' => '/api/placeholder/140/200',
        'ano' => '1899',
        'status' => 'Indisponível'
    ],
    [
        'id' => 3,
        'titulo' => 'Quincas Borba',
        'autor' => 'Machado de Assis',
        'capa' => '/api/placeholder/140/200',
        'ano' => '1891',
        'status' => 'Disponível'
    ],
    [
        'id' => 4,
        'titulo' => 'O Alienista',
        'autor' => 'Machado de Assis',
        'capa' => '/api/placeholder/140/200',
        'ano' => '1882',
        'status' => 'Disponível'
    ]
];

// Função para exibir estrelas de avaliação
function exibirEstrelas($quantidade) {
    $html = '';
    for ($i = 1; $i <= 5; $i++) {
        if ($i <= $quantidade) {
            $html .= '<i class="fas fa-star"></i>';
        } else {
            $html .= '<i class="far fa-star"></i>';
        }
    }
    return $html;
}
?>
<head>
    <link rel="stylesheet" href="/css/style.css">
    <link rel="stylesheet" href="/css/book-details.css">
</head>
<main>
    <div class="container">
        <!-- Breadcrumb de navegação -->
        <div class="breadcrumb">
            <a href="/">Início</a> &gt; 
            <a href="/categoria/<?php echo strtolower($livro['categoria']); ?>"><?php echo $livro['categoria']; ?></a> &gt; 
            <a href="/subcategoria/<?php echo strtolower($livro['subcategoria']); ?>"><?php echo $livro['subcategoria']; ?></a> &gt; 
            <span><?php echo $livro['titulo']; ?></span>
        </div>

        <!-- Seção principal com detalhes do livro -->
        <div class="book-details-container">
            <!-- Coluna esquerda - Capa e ações -->
            <div class="book-details-sidebar">
                <div class="book-cover-large">
                    <img src="<?php echo $livro['capa']; ?>" alt="<?php echo $livro['titulo']; ?>">
                </div>
                
                <div class="book-actions-panel">
                    <?php if ($livro['status'] == 'Disponível'): ?>
                        <button class="book-btn full"><i class="fas fa-bookmark"></i> Reservar</button>
                    <?php endif; ?>
                    <button class="book-btn outline full"><i class="fas fa-heart"></i> Adicionar aos Favoritos</button>
                    <button class="book-btn outline full"><i class="fas fa-share-alt"></i> Compartilhar</button>
                    <button class="book-btn outline full"><i class="fas fa-download"></i> Baixar Amostra</button>
                </div>
                
                <div class="book-availability">
                    <h4>Disponibilidade</h4>
                    <ul class="availability-list">
                        <li>
                            <span>Status:</span>
                            <span class="status-badge <?php echo strtolower($livro['status']) == 'disponível' ? 'available' : 'unavailable'; ?>">
                                <?php echo $livro['status']; ?>
                            </span>
                        </li>
                        <li>
                            <span>Total de exemplares:</span>
                            <span><?php echo $livro['exemplares']; ?></span>
                        </li>
                        <li>
                            <span>Emprestados:</span>
                            <span><?php echo $livro['emprestados']; ?></span>
                        </li>
                        <li>
                            <span>Reservados:</span>
                            <span><?php echo $livro['reservados']; ?></span>
                        </li>
                    </ul>
                </div>
            </div>
            
            <!-- Coluna direita - Informações detalhadas -->
            <div class="book-details-content">
                <div class="book-header">
                    <h1 class="book-title"><?php echo $livro['titulo']; ?></h1>
                    <h2 class="book-author">por <a href="/autor/<?php echo urlencode($livro['autor']); ?>"><?php echo $livro['autor']; ?></a></h2>
                    
                    <div class="book-rating">
                        <div class="stars">
                            <?php 
                            // Calcular média de avaliações
                            $total = 0;
                            foreach ($livro['avaliacoes'] as $avaliacao) {
                                $total += $avaliacao['estrelas'];
                            }
                            $media = count($livro['avaliacoes']) > 0 ? $total / count($livro['avaliacoes']) : 0;
                            
                            echo exibirEstrelas($media);
                            ?>
                        </div>
                        <span class="rating-value"><?php echo number_format($media, 1); ?></span>
                        <span class="reviews-count">(<?php echo count($livro['avaliacoes']); ?> avaliações)</span>
                    </div>
                </div>
                
                <!-- Tabs de navegação -->
                <div class="book-tabs">
                    <div class="tab active" data-tab="sinopse">Sinopse</div>
                    <div class="tab" data-tab="detalhes">Detalhes</div>
                    <div class="tab" data-tab="autor">Autor</div>
                    <div class="tab" data-tab="avaliacoes">Avaliações</div>
                    <div class="tab" data-tab="citacoes">Citações</div>
                </div>
                
                <!-- Conteúdo das tabs -->
                <div class="tab-content active" id="sinopse-content">
                    <div class="book-description-full">
                        <p><?php echo $livro['sinopse']; ?></p>
                    </div>
                    
                    <div class="book-tags">
                        <?php foreach ($livro['tags'] as $tag): ?>
                            <a href="/tag/<?php echo urlencode($tag); ?>" class="book-tag"><?php echo $tag; ?></a>
                        <?php endforeach; ?>
                    </div>
                </div>
                
                <div class="tab-content" id="detalhes-content">
                    <table class="book-details-table">
                        <tr>
                            <th>Título</th>
                            <td><?php echo $livro['titulo']; ?></td>
                        </tr>
                        <tr>
                            <th>Autor</th>
                            <td><?php echo $livro['autor']; ?></td>
                        </tr>
                        <tr>
                            <th>Editora</th>
                            <td><?php echo $livro['editora']; ?></td>
                        </tr>
                        <tr>
                            <th>Ano de publicação</th>
                            <td><?php echo $livro['ano']; ?></td>
                        </tr>
                        <tr>
                            <th>Páginas</th>
                            <td><?php echo $livro['paginas']; ?></td>
                        </tr>
                        <tr>
                            <th>Idioma</th>
                            <td><?php echo $livro['idioma']; ?></td>
                        </tr>
                        <tr>
                            <th>ISBN</th>
                            <td><?php echo $livro['isbn']; ?></td>
                        </tr>
                        <tr>
                            <th>Categoria</th>
                            <td><?php echo $livro['categoria']; ?></td>
                        </tr>
                        <tr>
                            <th>Subcategoria</th>
                            <td><?php echo $livro['subcategoria']; ?></td>
                        </tr>
                    </table>
                </div>
                
                <div class="tab-content" id="autor-content">
                    <div class="author-info">
                        <div class="author-image">
                            <img src="/api/placeholder/120/120" alt="<?php echo $livro['autor']; ?>">
                        </div>
                        <div class="author-bio">
                            <h3><?php echo $livro['autor']; ?></h3>
                            <p><?php echo $livro['biografia_autor']; ?></p>
                            <a href="/autor/<?php echo urlencode($livro['autor']); ?>" class="author-link">Ver todos os livros deste autor</a>
                        </div>
                    </div>
                </div>
                
                <div class="tab-content" id="avaliacoes-content">
                    <div class="reviews-summary">
                        <div class="average-rating">
                            <div class="big-rating"><?php echo number_format($media, 1); ?></div>
                            <div class="big-stars"><?php echo exibirEstrelas($media); ?></div>
                            <div class="total-reviews"><?php echo count($livro['avaliacoes']); ?> avaliações</div>
                        </div>
                        <div class="add-review">
                            <button class="book-btn"><i class="fas fa-pen"></i> Escrever avaliação</button>
                        </div>
                    </div>
                    
                    <div class="reviews-list">
                        <?php foreach ($livro['avaliacoes'] as $avaliacao): ?>
                            <div class="review-item">
                                <div class="review-header">
                                    <div class="reviewer-name"><?php echo $avaliacao['usuario']; ?></div>
                                    <div class="review-stars"><?php echo exibirEstrelas($avaliacao['estrelas']); ?></div>
                                </div>
                                <div class="review-content">
                                    <p><?php echo $avaliacao['comentario']; ?></p>
                                </div>
                                <div class="review-footer">
                                    <div class="review-helpful">
                                        <span>Esta avaliação foi útil?</span>
                                        <button class="helpful-btn"><i class="far fa-thumbs-up"></i> Sim</button>
                                        <button class="helpful-btn"><i class="far fa-thumbs-down"></i> Não</button>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                
                <div class="tab-content" id="citacoes-content">
                    <div class="quotes-list">
                        <?php foreach ($livro['citacoes_famosas'] as $citacao): ?>
                            <div class="quote-item">
                                <blockquote>
                                    <p><?php echo $citacao; ?></p>
                                </blockquote>
                                <div class="quote-actions">
                                    <button class="quote-btn"><i class="far fa-bookmark"></i> Salvar</button>
                                    <button class="quote-btn"><i class="far fa-copy"></i> Copiar</button>
                                    <button class="quote-btn"><i class="far fa-share-square"></i> Compartilhar</button>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Seção de livros relacionados -->
        <div class="related-books-section">
            <h2 class="section-title">Livros Relacionados</h2>
            <div class="related-books-grid">
                <?php foreach ($livros_relacionados as $livro_rel): ?>
                    <div class="related-book-card">
                        <div class="related-book-cover">
                            <img src="<?php echo $livro_rel['capa']; ?>" alt="<?php echo $livro_rel['titulo']; ?>">
                        </div>
                        <div class="related-book-info">
                            <h3 class="related-book-title">
                                <a href="/livro/<?php echo $livro_rel['id']; ?>"><?php echo $livro_rel['titulo']; ?></a>
                            </h3>
                            <p class="related-book-author"><?php echo $livro_rel['autor']; ?></p>
                            <p class="related-book-year"><?php echo $livro_rel['ano']; ?></p>
                            <div class="related-book-status status-<?php echo strtolower($livro_rel['status']) == 'disponível' ? 'available' : 'unavailable'; ?>">
                                <?php echo $livro_rel['status']; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
        
        <!-- Seção de recomendações personalizadas -->
        <div class="recommendations-section">
            <h2 class="section-title">Você também pode gostar</h2>
            <div class="recommendation-message">
                <i class="fas fa-info-circle"></i>
                <span>Estas recomendações são baseadas no seu histórico de leitura e preferências.</span>
            </div>
            <button class="book-btn outline view-more-btn">Ver mais recomendações</button>
        </div>
    </div>
</main>

<!-- JavaScript para as funcionalidades da página -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Funcionalidade das tabs
    const tabs = document.querySelectorAll('.book-tabs .tab');
    const tabContents = document.querySelectorAll('.tab-content');
    
    tabs.forEach(tab => {
        tab.addEventListener('click', function() {
            // Remover classe active de todas as tabs
            tabs.forEach(t => t.classList.remove('active'));
            // Adicionar classe active à tab clicada
            this.classList.add('active');
            
            // Esconder todos os conteúdos
            tabContents.forEach(content => content.classList.remove('active'));
            // Mostrar o conteúdo correspondente
            const tabId = this.getAttribute('data-tab');
            document.getElementById(`${tabId}-content`).classList.add('active');
        });
    });
    
    // Aqui podem ser adicionadas outras funcionalidades como:
    // - Sistema de avaliação
    // - Compartilhamento em redes sociais
    // - Favoritar livro
    // - Sistema de reserva
});
</script>

<?php require_once BASE_PATH . '/src/views/partials/footer.php'; ?>