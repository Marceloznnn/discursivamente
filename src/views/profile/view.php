<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perfil do Usuário | Discursivamente</title>
    <link rel="stylesheet" href="/assets/css/pages/perfil.css">
    <!-- Adicione um link para uma fonte como Roboto ou Inter se desejar -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <header>
        <div class="sidebar-header">
            <h1>Discursivamente</h1>
        </div>
        
        <div class="foto-perfil">
            <img src="placeholder-perfil.jpg" alt="Foto do Perfil">
            <button><i class="fas fa-camera"></i> <span>Alterar foto</span></button>
        </div>
        
        <!-- Menu de navegação principal -->
        <nav>
            <ul>
                <li><a href="perfil.html" class="active"><i class="fas fa-user"></i> <span>Perfil</span></a></li>
                <li><a href="compromissos.html"><i class="fas fa-calendar-alt"></i> <span>Compromissos</span></a></li>
                <li><a href="gerenciar-livros.html"><i class="fas fa-book"></i> <span>Gerenciar Livros</span></a></li>
                <li><a href="editar-perfil.html"><i class="fas fa-edit"></i> <span>Editar Perfil</span></a></li>
                <li><a href="seguranca.html"><i class="fas fa-shield-alt"></i> <span>Segurança</span></a></li>
                <li><a href="configuracao.html"><i class="fas fa-cog"></i> <span>Configuração</span></a></li>
                <li><a href="sair.html"><i class="fas fa-sign-out-alt"></i> <span>Sair</span></a></li>
            </ul>
        </nav>
    </header>

    <main>
        <!-- Seção de informações básicas do perfil -->
        <section id="info-perfil">
            <h2>Informações Pessoais</h2>
            
            <div class="dados-pessoais">
                <div style="--animation-order: 1">
                    <h3>Nome</h3>
                    <p>João da Silva</p>
                </div>
                
                <div style="--animation-order: 2">
                    <h3>Nome de usuário</h3>
                    <p>@joaosilva</p>
                </div>
                
                <div style="--animation-order: 3">
                    <h3>E-mail</h3>
                    <p>joao.silva@email.com</p>
                </div>
                
                <div style="--animation-order: 4">
                    <h3>Telefone</h3>
                    <p>(11) 98765-4321</p>
                </div>
                
                <div style="--animation-order: 5">
                    <h3>Data de nascimento</h3>
                    <p>15/05/1990</p>
                </div>
                
                <div style="--animation-order: 6">
                    <h3>Localização</h3>
                    <p>São Paulo, SP - Brasil</p>
                </div>
            </div>
        </section>

        <!-- Seção de estatísticas do usuário -->
        <section id="estatisticas">
            <h2>Minhas Estatísticas</h2>
            <div class="cards">
                <div class="card" style="--animation-order: 1">
                    <h3>Livros lidos</h3>
                    <p>42</p>
                </div>
                
                <div class="card" style="--animation-order: 2">
                    <h3>Compromissos</h3>
                    <p>5</p>
                    <small>pendentes</small>
                </div>
                
                <div class="card" style="--animation-order: 3">
                    <h3>Páginas lidas</h3>
                    <p>12.546</p>
                </div>
                
                <div class="card" style="--animation-order: 4">
                    <h3>Tempo de leitura</h3>
                    <p>187</p>
                    <small>horas</small>
                </div>
            </div>
        </section>
        
        <!-- Seção de livros em progresso -->
        <section id="livros-progresso">
            <h2>Livros em Progresso</h2>
            <div class="lista-livros">
                <div class="livro">
                    <img src="/api/placeholder/90/130" alt="Capa do livro">
                    <div class="info-livro">
                        <h3>Dom Casmurro</h3>
                        <p>Machado de Assis</p>
                        <progress value="65" max="100"></progress>
                        <div class="progresso-texto">
                            <span>Página 143 de 220</span>
                            <span>65%</span>
                        </div>
                    </div>
                </div>
                
                <div class="livro">
                    <img src="/api/placeholder/90/130" alt="Capa do livro">
                    <div class="info-livro">
                        <h3>O Cortiço</h3>
                        <p>Aluísio Azevedo</p>
                        <progress value="30" max="100"></progress>
                        <div class="progresso-texto">
                            <span>Página 72 de 240</span>
                            <span>30%</span>
                        </div>
                    </div>
                </div>
                
                <div class="livro">
                    <img src="/api/placeholder/90/130" alt="Capa do livro">
                    <div class="info-livro">
                        <h3>Memórias Póstumas de Brás Cubas</h3>
                        <p>Machado de Assis</p>
                        <progress value="15" max="100"></progress>
                        <div class="progresso-texto">
                            <span>Página 45 de 300</span>
                            <span>15%</span>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        
        <!-- Próximos compromissos -->
        <section id="proximos-compromissos">
            <h2>Próximos Compromissos</h2>
            <div class="lista-compromissos">
                <div class="compromisso">
                    <div class="data">
                        <span class="dia">15</span>
                        <span class="mes">ABR</span>
                    </div>
                    <div class="info-compromisso">
                        <h3>Clube do Livro</h3>
                        <p><i class="fas fa-clock"></i> 19:00 - 21:00</p>
                        <p><i class="fas fa-map-marker-alt"></i> Livraria Cultura</p>
                    </div>
                </div>
                
                <div class="compromisso">
                    <div class="data">
                        <span class="dia">22</span>
                        <span class="mes">ABR</span>
                    </div>
                    <div class="info-compromisso">
                        <h3>Palestra Literatura Brasileira</h3>
                        <p><i class="fas fa-clock"></i> 14:00 - 16:30</p>
                        <p><i class="fas fa-map-marker-alt"></i> Centro Cultural</p>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <footer>
        <p>© 2025 Discursivamente - Todos os direitos reservados</p>
        <nav>
            <ul>
                <li><a href="#">Sobre nós</a></li>
                <li><a href="#">Termos de uso</a></li>
                <li><a href="#">Privacidade</a></li>
                <li><a href="#">Contato</a></li>
            </ul>
        </nav>
    </footer>

    <script>
        // Script para animar os elementos ao carregar a página
        document.addEventListener('DOMContentLoaded', function() {
            // Código para animar elementos se necessário
            const cards = document.querySelectorAll('.card');
            cards.forEach((card, index) => {
                card.style.setProperty('--animation-order', index + 1);
            });
            
            const dadosPessoais = document.querySelectorAll('.dados-pessoais div');
            dadosPessoais.forEach((dado, index) => {
                dado.style.setProperty('--animation-order', index + 1);
            });
        });
    </script>
</body>
</html>