<?php
class SearchController {
    private $pdo;
    
    public function __construct($pdo) {
        $this->pdo = $pdo;
    }
    
    public function index() {
        // Recupera o termo de pesquisa via GET
        $query = isset($_GET['q']) ? trim($_GET['q']) : '';
        
        $books = [];
        $categories = [];
        
        if (!empty($query)) {
            // Busca livros por título ou autor
            $stmt = $this->pdo->prepare("SELECT * FROM books WHERE title LIKE :q OR author LIKE :q");
            $stmt->execute(['q' => "%$query%"]);
            $books = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Busca categorias (gêneros ou subcategorias) pelo nome
            $stmt2 = $this->pdo->prepare("SELECT * FROM categories WHERE name LIKE :q");
            $stmt2->execute(['q' => "%$query%"]);
            $categories = $stmt2->fetchAll(PDO::FETCH_ASSOC);
        }
        
        // Exibe a view de pesquisa de livros
        require_once BASE_PATH . '/views/search.php';
    }
}
?>
