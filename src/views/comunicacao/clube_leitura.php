<?php
/**
 * Página do Clube de Leitura - Discursivamente
 * Localização: C:\xampp\htdocs\DISCURSIVAMENTE\src\views\comunicacao\clube_leitura.php
 * 
 * Esta página exibe e gerencia a área do clube de leitura, apresentando:
 * - Banner de boas-vindas
 * - Seção de introdução
 * - Livros em destaque
 * - Agendamento de encontros
 * - Área de depoimentos
 */

// Inclusão do header
require_once BASE_PATH . '/src/views/partials/header.php';
?>

<!-- Banner de Boas-Vindas -->
<section class="banner-clube-leitura">
    <div class="container">
        <div class="banner-content">
            <h1>Bem-vindo ao Clube de Leitura Discursivamente!</h1>
            <p>Um espaço para compartilhar ideias, descobrir novas obras e conectar-se com outros amantes da literatura.</p>
        </div>
    </div>
</section>

<!-- Seção de Introdução -->
<section class="introducao-clube">
    <div class="container">
        <div class="row">
            <div class="col-md-8 offset-md-2">
                <h2>Nosso Clube de Leitura</h2>
                <p>O Clube de Leitura Discursivamente é um espaço dedicado aos amantes da literatura que desejam expandir seus horizontes literários. Aqui, você poderá discutir obras literárias, trocar recomendações e participar de encontros presenciais ou virtuais com outros leitores apaixonados.</p>
                <p>Nossa missão é fomentar o hábito da leitura, promover debates enriquecedores e criar uma comunidade vibrante de leitores.</p>
            </div>
        </div>
    </div>
</section>

<!-- Área de Livros em Destaque -->
<section class="livros-destaque">
    <div class="container">
        <h2>Livros em Destaque</h2>
        <p class="section-description">Confira as obras que estão sendo discutidas ou recomendadas pelo nosso clube:</p>
        
        <div class="row">
            <!-- Card de Livro 1 -->
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="card livro-card">
                    <div class="card-img-container">
                        <img src="../../assets/images/livros/livro1.jpg" class="card-img-top" alt="Capa do livro">
                    </div>
                    <div class="card-body">
                        <h5 class="card-title">O Processo</h5>
                        <h6 class="card-subtitle mb-2 text-muted">Franz Kafka</h6>
                        <p class="card-text">Um romance que narra a história de Josef K., que acorda certa manhã e é preso e sujeito a um longo e incompreensível processo por um crime não especificado.</p>
                        <div class="livro-info">
                            <span><i class="fas fa-calendar"></i> 1925</span>
                            <span><i class="fas fa-file-alt"></i> 256 páginas</span>
                        </div>
                        <a href="#" class="btn btn-primary mt-3">Ver Detalhes</a>
                    </div>
                </div>
            </div>
            
            <!-- Card de Livro 2 -->
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="card livro-card">
                    <div class="card-img-container">
                        <img src="../../assets/images/livros/livro2.jpg" class="card-img-top" alt="Capa do livro">
                    </div>
                    <div class="card-body">
                        <h5 class="card-title">Cem Anos de Solidão</h5>
                        <h6 class="card-subtitle mb-2 text-muted">Gabriel García Márquez</h6>
                        <p class="card-text">A obra prima do realismo mágico que conta a história da família Buendía ao longo de sete gerações em Macondo.</p>
                        <div class="livro-info">
                            <span><i class="fas fa-calendar"></i> 1967</span>
                            <span><i class="fas fa-file-alt"></i> 417 páginas</span>
                        </div>
                        <a href="#" class="btn btn-primary mt-3">Ver Detalhes</a>
                    </div>
                </div>
            </div>
            
            <!-- Card de Livro 3 -->
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="card livro-card">
                    <div class="card-img-container">
                        <img src="../../assets/images/livros/livro3.jpg" class="card-img-top" alt="Capa do livro">
                    </div>
                    <div class="card-body">
                        <h5 class="card-title">1984</h5>
                        <h6 class="card-subtitle mb-2 text-muted">George Orwell</h6>
                        <p class="card-text">Um clássico da distopia que retrata um futuro totalitário onde o governo controla até mesmo os pensamentos dos cidadãos.</p>
                        <div class="livro-info">
                            <span><i class="fas fa-calendar"></i> 1949</span>
                            <span><i class="fas fa-file-alt"></i> 328 páginas</span>
                        </div>
                        <a href="#" class="btn btn-primary mt-3">Ver Detalhes</a>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="text-center mt-4">
            <a href="#" class="btn btn-success btn-lg">
                <i class="fas fa-plus-circle"></i> Adicionar Novo Livro
            </a>
        </div>
    </div>
</section>

<!-- Área de Agendamento de Encontros -->
<section class="encontros-clube">
    <div class="container">
        <h2>Próximos Encontros</h2>
        <p class="section-description">Participe de nossas reuniões e compartilhe suas impressões sobre as obras selecionadas:</p>
        
        <div class="row">
            <!-- Encontro 1 -->
            <div class="col-md-6 mb-4">
                <div class="card encontro-card">
                    <div class="card-body">
                        <div class="encontro-data">
                            <span class="dia">15</span>
                            <span class="mes">Abril</span>
                        </div>
                        <h5 class="card-title">Discussão: "O Processo" de Franz Kafka</h5>
                        <p class="card-text">Neste encontro, discutiremos os temas de burocracia, alienação e absurdo presentes na obra-prima de Kafka.</p>
                        <ul class="encontro-info">
                            <li><i class="fas fa-clock"></i> 19:00 - 21:00</li>
                            <li><i class="fas fa-map-marker-alt"></i> Sala de Reuniões - Prédio Principal</li>
                            <li><i class="fas fa-users"></i> 12 participantes confirmados</li>
                        </ul>
                        <a href="#" class="btn btn-primary">Participar do Encontro</a>
                    </div>
                </div>
            </div>
            
            <!-- Encontro 2 -->
            <div class="col-md-6 mb-4">
                <div class="card encontro-card">
                    <div class="card-body">
                        <div class="encontro-data">
                            <span class="dia">29</span>
                            <span class="mes">Abril</span>
                        </div>
                        <h5 class="card-title">Discussão: "Cem Anos de Solidão" de Gabriel García Márquez</h5>
                        <p class="card-text">Venha explorar o universo mágico de Macondo e os temas de solidão, tempo e destino nesta obra fundamental da literatura latino-americana.</p>
                        <ul class="encontro-info">
                            <li><i class="fas fa-clock"></i> 19:00 - 21:00</li>
                            <li><i class="fas fa-map-marker-alt"></i> Biblioteca Central</li>
                            <li><i class="fas fa-users"></i> 8 participantes confirmados</li>
                        </ul>
                        <a href="#" class="btn btn-primary">Participar do Encontro</a>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="text-center mt-4">
            <a href="#" class="btn btn-success btn-lg">
                <i class="fas fa-calendar-plus"></i> Agendar Novo Encontro
            </a>
        </div>
    </div>
</section>

<!-- Seção de Depoimentos e Comentários -->
<section class="depoimentos-clube">
    <div class="container">
        <h2>O Que Nossos Leitores Dizem</h2>
        <p class="section-description">Confira as opiniões e experiências compartilhadas pelos membros do nosso clube:</p>
        
        <div class="row">
            <!-- Depoimento 1 -->
            <div class="col-md-4 mb-4">
                <div class="card depoimento-card">
                    <div class="card-body">
                        <div class="depoimento-avatar">
                            <img src="../../assets/images/avatars/avatar1.jpg" alt="Foto do membro">
                        </div>
                        <h5 class="card-title">Ana Silva</h5>
                        <div class="depoimento-estrelas">
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                        </div>
                        <p class="card-text">"Participar do Clube de Leitura expandiu meus horizontes literários. As discussões são sempre ricas e as recomendações têm sido certeiras!"</p>
                        <div class="depoimento-livro">
                            <small>Sobre: <a href="#">"O Processo" de Franz Kafka</a></small>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Depoimento 2 -->
            <div class="col-md-4 mb-4">
                <div class="card depoimento-card">
                    <div class="card-body">
                        <div class="depoimento-avatar">
                            <img src="../../assets/images/avatars/avatar2.jpg" alt="Foto do membro">
                        </div>
                        <h5 class="card-title">Carlos Mendes</h5>
                        <div class="depoimento-estrelas">
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="far fa-star"></i>
                        </div>
                        <p class="card-text">"Os encontros são sempre enriquecedores e me ajudaram a compreender melhor as obras discutidas. Recomendo a todos os amantes da literatura!"</p>
                        <div class="depoimento-livro">
                            <small>Sobre: <a href="#">"1984" de George Orwell</a></small>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Depoimento 3 -->
            <div class="col-md-4 mb-4">
                <div class="card depoimento-card">
                    <div class="card-body">
                        <div class="depoimento-avatar">
                            <img src="../../assets/images/avatars/avatar3.jpg" alt="Foto do membro">
                        </div>
                        <h5 class="card-title">Juliana Ferreira</h5>
                        <div class="depoimento-estrelas">
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                        </div>
                        <p class="card-text">"O ambiente acolhedor e as discussões profundas fazem deste clube uma experiência única. Cada encontro é uma oportunidade de crescimento intelectual."</p>
                        <div class="depoimento-livro">
                            <small>Sobre: <a href="#">"Cem Anos de Solidão" de G. García Márquez</a></small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Formulário para Novo Comentário -->
        <div class="row mt-4">
            <div class="col-md-8 offset-md-2">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Compartilhe Sua Opinião</h5>
                        <form id="formDepoimento">
                            <div class="form-group mb-3">
                                <label for="livroComentario">Livro:</label>
                                <select class="form-control" id="livroComentario" required>
                                    <option value="">Selecione um livro</option>
                                    <option value="1">O Processo - Franz Kafka</option>
                                    <option value="2">Cem Anos de Solidão - Gabriel García Márquez</option>
                                    <option value="3">1984 - George Orwell</option>
                                </select>
                            </div>
                            <div class="form-group mb-3">
                                <label for="avaliacaoComentario">Avaliação:</label>
                                <div class="rating-select">
                                    <i class="far fa-star" data-value="1"></i>
                                    <i class="far fa-star" data-value="2"></i>
                                    <i class="far fa-star" data-value="3"></i>
                                    <i class="far fa-star" data-value="4"></i>
                                    <i class="far fa-star" data-value="5"></i>
                                </div>
                                <input type="hidden" id="avaliacaoComentario" value="0">
                            </div>
                            <div class="form-group mb-3">
                                <label for="textoComentario">Seu comentário:</label>
                                <textarea class="form-control" id="textoComentario" rows="4" required></textarea>
                            </div>
                            <button type="submit" class="btn btn-primary">Enviar Comentário</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Chamada para Ação -->
<section class="cta-section">
    <div class="container text-center">
        <h2>Junte-se ao Nosso Clube de Leitura!</h2>
        <p>Faça parte desta comunidade de leitores apaixonados e amplie seus horizontes literários.</p>
        <a href="#" class="btn btn-lg btn-primary">Tornar-se Membro</a>
    </div>
</section>

<!-- CSS Personalizado -->
<style>
    /* Estilos gerais */
    body {
        font-family: 'Roboto', sans-serif;
        color: #333;
    }
    
    section {
        padding: 60px 0;
    }
    
    h2 {
        margin-bottom: 20px;
        color: #1a237e;
        font-weight: 700;
    }
    
    .section-description {
        margin-bottom: 30px;
        font-size: 1.1rem;
        color: #555;
    }
    
    /* Banner de Boas-Vindas */
    .banner-clube-leitura {
        background: linear-gradient(rgba(0, 0, 0, 0.7), rgba(0, 0, 0, 0.7)), url('../../assets/images/banner-clube-leitura.jpg');
        background-size: cover;
        background-position: center;
        color: white;
        padding: 100px 0;
        text-align: center;
    }
    
    .banner-clube-leitura h1 {
        font-size: 3rem;
        font-weight: 700;
        margin-bottom: 20px;
    }
    
    .banner-clube-leitura p {
        font-size: 1.2rem;
        max-width: 800px;
        margin: 0 auto;
    }
    
    /* Seção de Introdução */
    .introducao-clube {
        background-color: #f9f9f9;
        text-align: center;
    }
    
    /* Cards de Livros */
    .livro-card {
        height: 100%;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        border: none;
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    }
    
    .livro-card:hover {
        transform: translateY(-10px);
        box-shadow: 0 15px 30px rgba(0,0,0,0.15);
    }
    
    .card-img-container {
        height: 250px;
        overflow: hidden;
    }
    
    .card-img-top {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    
    .livro-info {
        display: flex;
        justify-content: space-between;
        margin-top: 10px;
        color: #666;
    }
    
    /* Área de Encontros */
    .encontros-clube {
        background-color: #f5f5f5;
    }
    
    .encontro-card {
        border: none;
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    }
    
    .encontro-data {
        float: left;
        text-align: center;
        background-color: #1a237e;
        color: white;
        padding: 10px;
        border-radius: 5px;
        margin-right: 15px;
        width: 70px;
    }
    
    .encontro-data .dia {
        display: block;
        font-size: 1.8rem;
        font-weight: 700;
        line-height: 1;
    }
    
    .encontro-data .mes {
        display: block;
        font-size: 0.9rem;
        text-transform: uppercase;
    }
    
    .encontro-info {
        list-style: none;
        padding: 0;
        margin-top: 15px;
        margin-bottom: 20px;
    }
    
    .encontro-info li {
        margin-bottom: 8px;
    }
    
    .encontro-info li i {
        margin-right: 8px;
        color: #1a237e;
    }
    
    /* Depoimentos */
    .depoimento-card {
        height: 100%;
        border: none;
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        text-align: center;
    }
    
    .depoimento-avatar {
        width: 80px;
        height: 80px;
        border-radius: 50%;
        overflow: hidden;
        margin: 0 auto 15px;
        border: 3px solid #1a237e;
    }
    
    .depoimento-avatar img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    
    .depoimento-estrelas {
        color: #FFD700;
        margin-bottom: 10px;
    }
    
    .depoimento-livro {
        margin-top: 15px;
        font-style: italic;
    }
    
    /* Rating Select */
    .rating-select {
        font-size: 1.5rem;
        color: #ddd;
        cursor: pointer;
        margin-bottom: 10px;
    }
    
    .rating-select i {
        margin-right: 5px;
    }
    
    .rating-select i.fas {
        color: #FFD700;
    }
    
    /* CTA Section */
    .cta-section {
        background: linear-gradient(rgba(0, 0, 0, 0.8), rgba(0, 0, 0, 0.8)), url('../../assets/images/cta-background.jpg');
        background-size: cover;
        background-position: center;
        color: white;
        padding: 80px 0;
    }
    
    .cta-section h2 {
        color: white;
    }
    
    .cta-section .btn {
        margin-top: 20px;
        padding: 12px 30px;
        font-size: 1.1rem;
    }
    
    /* Botões e links */
    .btn-primary {
        background-color: #1a237e;
        border-color: #1a237e;
    }
    
    .btn-primary:hover {
        background-color: #0e1442;
        border-color: #0e1442;
    }
    
    .btn-success {
        background-color: #2e7d32;
        border-color: #2e7d32;
    }
    
    .btn-success:hover {
        background-color: #1b5e20;
        border-color: #1b5e20;
    }
    
    /* Responsividade */
    @media (max-width: 768px) {
        .banner-clube-leitura h1 {
            font-size: 2rem;
        }
        
        .banner-clube-leitura p {
            font-size: 1rem;
        }
        
        section {
            padding: 40px 0;
        }
    }
</style>

<!-- JavaScript para interatividade -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Script para seleção de estrelas na avaliação
        const stars = document.querySelectorAll('.rating-select i');
        const ratingInput = document.getElementById('avaliacaoComentario');
        
        stars.forEach(star => {
            star.addEventListener('mouseover', function() {
                const value = this.dataset.value;
                
                // Reset all stars
                stars.forEach(s => s.className = 'far fa-star');
                
                // Fill stars up to the hovered one
                for(let i = 0; i < value; i++) {
                    stars[i].className = 'fas fa-star';
                }
            });
            
            star.addEventListener('mouseout', function() {
                const currentRating = ratingInput.value;
                
                // Reset all stars
                stars.forEach(s => s.className = 'far fa-star');
                
                // Fill stars up to the current rating
                for(let i = 0; i < currentRating; i++) {
                    stars[i].className = 'fas fa-star';
                }
            });
            
            star.addEventListener('click', function() {
                const value = this.dataset.value;
                ratingInput.value = value;
                
                // Fill stars up to the clicked one
                stars.forEach((s, index) => {
                    s.className = index < value ? 'fas fa-star' : 'far fa-star';
                });
            });
        });
        
        // Validação e envio do formulário de depoimento
        const formDepoimento = document.getElementById('formDepoimento');
        
        formDepoimento.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const livro = document.getElementById('livroComentario').value;
            const avaliacao = document.getElementById('avaliacaoComentario').value;
            const texto = document.getElementById('textoComentario').value;
            
            if (!livro || avaliacao === '0' || !texto) {
                alert('Por favor, preencha todos os campos e faça sua avaliação.');
                return;
            }
            
            // Aqui você adicionaria o código para enviar o depoimento ao servidor
            alert('Comentário enviado com sucesso! Após aprovação, ele será exibido na página.');
            formDepoimento.reset();
            document.getElementById('avaliacaoComentario').value = '0';
            stars.forEach(s => s.className = 'far fa-star');
        });
    });
</script>

<?php
// Inclusão do footer
include_once "../../includes/footer.php";
?>