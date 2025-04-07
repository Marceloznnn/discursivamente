<?php
$titulo = "Biblioteca";
require_once __DIR__ . '/../parciais/header.php';
?>

<div class="row">
    <div class="col-md-8">
        <h1 class="mb-4">Minha Biblioteca</h1>
        <p class="lead">Organize suas leituras, descubra novos livros e acompanhe seu progresso.</p>
        
        <ul class="nav nav-tabs mb-4">
            <li class="nav-item">
                <a class="nav-link active" href="/profile/biblioteca">Todos</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="/profile/biblioteca?filter=reading">Lendo</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="/profile/biblioteca?filter=read">Lidos</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="/profile/biblioteca?filter=to-read">Quero Ler</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="/profile/favoritos">Favoritos</a>
            </li>
        </ul>
        
        <div class="row">
            <?php for ($i = 1; $i <= 6; $i++): ?>
            <div class="col-md-4 mb-4">
                <div class="card h-100">
                    <div class="bg-secondary text-white d-flex justify-content-center align-items-center" style="height: 200px;">
                        <span>Capa do Livro</span>
                    </div>
                    <div class="card-body">
                        <h5 class="card-title">Título do Livro <?= $i ?></h5>
                        <p class="card-text">Autor do Livro</p>
                        <div class="d-flex justify-content-between">
                            <span class="badge bg-primary">Lendo</span>
                            <small>Progresso: 45%</small>
                        </div>
                    </div>
                    <div class="card-footer bg-white border-top-0">
                        <a href="/profile/biblioteca/book/<?= $i ?>" class="btn btn-sm btn-outline-primary">Detalhes</a>
                    </div>
                </div>
            </div>
            <?php endfor; ?>
        </div>
        
        <nav aria-label="Navegação de página">
            <ul class="pagination justify-content-center mt-4">
                <li class="page-item disabled">
                    <a class="page-link" href="#" tabindex="-1" aria-disabled="true">Anterior</a>
                </li>
                <li class="page-item active"><a class="page-link" href="#">1</a></li>
                <li class="page-item"><a class="page-link" href="#">2</a></li>
                <li class="page-item"><a class="page-link" href="#">3</a></li>
                <li class="page-item">
                    <a class="page-link" href="#">Próximo</a>
                </li>
            </ul>
        </nav>
    </div>
    
    <div class="col-md-4">
        <div class="card mb-4">
            <div class="card-header">
                Adicionar Livro
            </div>
            <div class="card-body">
                <form>
                    <div class="mb-3">
                        <label for="bookTitle" class="form-label">Título</label>
                        <input type="text" class="form-control" id="bookTitle" required>
                    </div>
                    <div class="mb-3">
                        <label for="bookAuthor" class="form-label">Autor</label>
                        <input type="text" class="form-control" id="bookAuthor" required>
                    </div>
                    <div class="mb-3">
                        <label for="bookStatus" class="form-label">Status</label>
                        <select class="form-select" id="bookStatus">
                            <option value="reading">Lendo</option>
                            <option value="read">Lido</option>
                            <option value="to-read">Quero Ler</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary">Adicionar</button>
                </form>
            </div>
        </div>
        
        <div class="card mb-4">
            <div class="card-header">
                Estatísticas de Leitura
            </div>
            <div class="card-body">
                <ul class="list-group list-group-flush">
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        Livros lidos
                        <span class="badge bg-primary rounded-pill">24</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        Lendo atualmente
                        <span class="badge bg-primary rounded-pill">3</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        Quero ler
                        <span class="badge bg-primary rounded-pill">12</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        Páginas lidas em 2023
                        <span class="badge bg-primary rounded-pill">5432</span>
                    </li>
                </ul>
            </div>
        </div>
        
        <div class="card">
            <div class="card-header">
                Gerenciar Biblioteca
            </div>
            <div class="card-body">
                <div class="list-group">
                    <a href="/profile/biblioteca/gerenciar" class="list-group-item list-group-item-action">Gerenciar Livros</a>
                    <a href="/profile/salvos" class="list-group-item list-group-item-action">Itens Salvos</a>
                    <a href="/profile/historico" class="list-group-item list-group-item-action">Histórico de Leitura</a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../parciais/footer.php'; ?>