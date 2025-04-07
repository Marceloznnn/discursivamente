<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Informações - Perfil do Usuário</title>
    <!-- Font Awesome para ícones -->
     <link rel="stylesheet" href="/css/perfil.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
</head>
<body>
    <!-- Header principal fixo (já existente) -->
    <header class="header-principal">
        <h1>Biblioteca Virtual</h1>
    </header>
    
    <!-- Container que envolve sidebar e conteúdo com borda -->
    <div class="container-principal">
        <!-- Sidebar (header lateral) -->
        <aside class="sidebar">
            <div class="sidebar-header">
                <h2>Menu de Navegação</h2>
            </div>
            
            <ul class="menu-navigation">
                <li>
                    <a href="#">
                        <i class="fas fa-user"></i> Perfil do Usuário
                    </a>
                </li>
                <li>
                    <a href="#" class="active">
                        <i class="fas fa-edit"></i> Editar Informações
                    </a>
                </li>
                <li>
                    <a href="#">
                        <i class="fas fa-book"></i> Gerenciar Livros
                    </a>
                </li>
                <li>
                    <a href="#">
                        <i class="fas fa-star"></i> Avaliações e Feedbacks
                    </a>
                </li>
                <li>
                    <a href="#">
                        <i class="fas fa-history"></i> Histórico de Leitura
                    </a>
                </li>
                <li>
                    <a href="#">
                        <i class="fas fa-cog"></i> Configurações
                    </a>
                </li>
            </ul>
            
            <div class="logout-button">
                <button>
                    <i class="fas fa-sign-out-alt"></i> Sair
                </button>
            </div>
        </aside>
        
        <!-- Conteúdo principal -->
        <main class="conteudo-principal">
            <h1 class="titulo-pagina">
                <i class="fas fa-user-edit"></i> Editar Informações
            </h1>
            
            <!-- Mensagem de sucesso (inicialmente oculta) -->
            <div class="alert alert-success alert-hidden" id="success-alert">
                <i class="fas fa-check-circle"></i> Informações atualizadas com sucesso!
            </div>
            
            <!-- Mensagem de erro (inicialmente oculta) -->
            <div class="alert alert-danger alert-hidden" id="error-alert">
                <i class="fas fa-exclamation-circle"></i> Erro ao atualizar informações. Verifique os campos e tente novamente.
            </div>
            
            <div class="form-container">
                <!-- Seção: Perfil Básico -->
                <div class="form-section">
                    <h3 class="form-title">
                        <i class="fas fa-id-card"></i> Informações do Perfil
                    </h3>
                    
                    <div class="foto-perfil-container">
                        <img src="/api/placeholder/100/100" alt="Foto do Perfil" class="foto-perfil">
                        <div class="upload-foto">
                            <label for="foto" class="upload-btn">
                                <i class="fas fa-upload"></i> Alterar Foto
                            </label>
                            <input type="file" id="foto" class="form-control-file" accept="image/*">
                            <p style="margin-top: 5px; font-size: 0.8rem; color: #6c757d;">
                                Formatos aceitos: JPG, PNG. Tamanho máximo: 5MB
                            </p>
                        </div>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="nome">Nome Completo</label>
                            <input type="text" id="nome" class="form-control" value="João Silva">
                        </div>
                        <div class="form-group">
                            <label for="usuario">Nome de Usuário</label>
                            <input type="text" id="usuario" class="form-control" value="joao.silva">
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="bio">Biografia</label>
                        <textarea id="bio" class="form-control">Apaixonado por literatura clássica e ficção científica. Sempre em busca de novas histórias para explorar!</textarea>
                    </div>
                </div>
                
                <!-- Seção: Informações de Contato -->
                <div class="form-section">
                    <h3 class="form-title">
                        <i class="fas fa-address-card"></i> Informações de Contato
                    </h3>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="email">E-mail</label>
                            <input type="email" id="email" class="form-control" value="joao.silva@email.com">
                        </div>
                        <div class="form-group">
                            <label for="telefone">Telefone</label>
                            <input type="tel" id="telefone" class="form-control" value="(11) 98765-4321">
                        </div>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="data-nascimento">Data de Nascimento</label>
                            <input type="date" id="data-nascimento" class="form-control" value="1990-05-15">
                        </div>
                        <div class="form-group">
                            <label for="genero">Gênero</label>
                            <select id="genero" class="form-control">
                                <option value="masculino" selected>Masculino</option>
                                <option value="feminino">Feminino</option>
                                <option value="outro">Outro</option>
                                <option value="prefiro-nao-informar">Prefiro não informar</option>
                            </select>
                        </div>
                    </div>
                </div>
                
                <!-- Seção: Endereço -->
                <div class="form-section">
                    <h3 class="form-title">
                        <i class="fas fa-map-marker-alt"></i> Endereço
                    </h3>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="cep">CEP</label>
                            <input type="text" id="cep" class="form-control" value="04538-132">
                        </div>
                        <div class="form-group">
                            <label for="estado">Estado</label>
                            <select id="estado" class="form-control">
                                <option value="">Selecione...</option>
                                <option value="AC">Acre</option>
                                <option value="AL">Alagoas</option>
                                <option value="AP">Amapá</option>
                                <option value="AM">Amazonas</option>
                                <option value="BA">Bahia</option>
                                <option value="CE">Ceará</option>
                                <option value="DF">Distrito Federal</option>
                                <option value="ES">Espírito Santo</option>
                                <option value="GO">Goiás</option>
                                <option value="MA">Maranhão</option>
                                <option value="MT">Mato Grosso</option>
                                <option value="MS">Mato Grosso do Sul</option>
                                <option value="MG">Minas Gerais</option>
                                <option value="PA">Pará</option>
                                <option value="PB">Paraíba</option>
                                <option value="PR">Paraná</option>
                                <option value="PE">Pernambuco</option>
                                <option value="PI">Piauí</option>
                                <option value="RJ">Rio de Janeiro</option>
                                <option value="RN">Rio Grande do Norte</option>
                                <option value="RS">Rio Grande do Sul</option>
                                <option value="RO">Rondônia</option>
                                <option value="RR">Roraima</option>
                                <option value="SC">Santa Catarina</option>
                                <option value="SP" selected>São Paulo</option>
                                <option value="SE">Sergipe</option>
                                <option value="TO">Tocantins</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="cidade">Cidade</label>
                            <input type="text" id="cidade" class="form-control" value="São Paulo">
                        </div>
                        <div class="form-group">
                            <label for="bairro">Bairro</label>
                            <input type="text" id="bairro" class="form-control" value="Itaim Bibi">
                        </div>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="rua">Rua</label>
                            <input type="text" id="rua" class="form-control" value="Av. Brigadeiro Faria Lima">
                        </div>
                        <div class="form-group">
                            <label for="numero">Número</label>
                            <input type="text" id="numero" class="form-control" value="3477">
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="complemento">Complemento</label>
                        <input type="text" id="complemento" class="form-control" value="Torre Norte, Apto 42">
                    </div>
                </div>
                
                <!-- Seção: Preferências de Leitura -->
                <div class="form-section">
                    <h3 class="form-title">
                        <i class="fas fa-book-reader"></i> Preferências de Leitura
                    </h3>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="generos-favoritos">Gêneros Favoritos</label>
                            <select id="generos-favoritos" class="form-control" multiple size="5">
                                <option value="ficcao-cientifica" selected>Ficção Científica</option>
                                <option value="fantasia" selected>Fantasia</option>
                                <option value="literatura-classica" selected>Literatura Clássica</option>
                                <option value="suspense" selected>Suspense</option>
                                <option value="romance">Romance</option>
                                <option value="biografia">Biografia</option>
                                <option value="policial">Policial</option>
                                <option value="horror">Horror</option>
                                <option value="poesia">Poesia</option>
                                <option value="aventura">Aventura</option>
                            </select>
                            <p style="margin-top: 5px; font-size: 0.8rem; color: #6c757d;">
                                Pressione Ctrl (ou Cmd no Mac) para selecionar múltiplos gêneros
                            </p>
                        </div>
                        <div class="form-group">
                            <label for="autores-favoritos">Autores Favoritos</label>
                            <textarea id="autores-favoritos" class="form-control" placeholder="Adicione seus autores favoritos, separados por vírgula">Isaac Asimov, J.R.R. Tolkien, Jane Austen, Fyodor Dostoevsky, Agatha Christie</textarea>
                        </div>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="idioma-preferido">Idioma Preferido para Leitura</label>
                            <select id="idioma-preferido" class="form-control">
                                <option value="portugues" selected>Português</option>
                                <option value="ingles">Inglês</option>
                                <option value="espanhol">Espanhol</option>
                                <option value="frances">Francês</option>
                                <option value="alemao">Alemão</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="formato-preferido">Formato Preferido</label>
                            <select id="formato-preferido" class="form-control">
                                <option value="fisico" selected>Livro Físico</option>
                                <option value="ebook">E-book</option>
                                <option value="audiobook">Audiobook</option>
                                <option value="todos">Todos os formatos</option>
                            </select>
                        </div>
                    </div>
                </div>
                
                <!-- Botões do formulário -->
                <div class="form-buttons">
                    <button type="button" class="btn btn-secondary" id="btn-cancelar">Cancelar</button>
                    <button type="button" class="btn btn-primary" id="btn-salvar">Salvar Alterações</button>
                </div>
            </div>
        </main>
    </div>
    
    <script>
        // Script simples para demonstrar o feedback de sucesso ao salvar
        document.getElementById('btn-salvar').addEventListener('click', function() {
            // Simula o envio do formulário
            setTimeout(function() {
                // Exibe mensagem de sucesso
                document.getElementById('success-alert').classList.remove('alert-hidden');
                
                // Esconde a mensagem após 3 segundos
                setTimeout(function() {
                    document.getElementById('success-alert').classList.add('alert-hidden');
                }, 3000);
            }, 500);
        });
        
        // Botão cancelar redireciona para a página de perfil
        document.getElementById('btn-cancelar').addEventListener('click', function() {
            // Em um ambiente real, redirecionaria para a página de perfil
            alert('Operação cancelada. Redirecionando para o perfil...');
        });
    </script>
</body>
</html>