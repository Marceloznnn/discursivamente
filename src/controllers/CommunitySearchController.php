<?php
class CommunitySearchController {
    private $pdo;
    
    public function __construct($pdo) {
        $this->pdo = $pdo;
    }
    
    public function index() {
        // Recupera o termo de pesquisa via GET
        $query = isset($_GET['q']) ? trim($_GET['q']) : '';
        
        $forums = [];
        $groups = [];
        $clubs = [];
        $events = [];
        
        if (!empty($query)) {
            // Busca fóruns
            $stmt1 = $this->pdo->prepare("SELECT * FROM forums WHERE title LIKE :q OR description LIKE :q");
            $stmt1->execute(['q' => "%$query%"]);
            $forums = $stmt1->fetchAll(PDO::FETCH_ASSOC);
            
            // Busca grupos de livros
            $stmt2 = $this->pdo->prepare("SELECT * FROM groups WHERE name LIKE :q OR description LIKE :q");
            $stmt2->execute(['q' => "%$query%"]);
            $groups = $stmt2->fetchAll(PDO::FETCH_ASSOC);
            
            // Busca clubes
            $stmt3 = $this->pdo->prepare("SELECT * FROM clubs WHERE name LIKE :q OR description LIKE :q");
            $stmt3->execute(['q' => "%$query%"]);
            $clubs = $stmt3->fetchAll(PDO::FETCH_ASSOC);
            
            // Busca eventos
            $stmt4 = $this->pdo->prepare("SELECT * FROM events WHERE title LIKE :q OR description LIKE :q");
            $stmt4->execute(['q' => "%$query%"]);
            $events = $stmt4->fetchAll(PDO::FETCH_ASSOC);
        }
        
        // Exibe a view de pesquisa de comunidades
        require_once BASE_PATH . '/views/community_search.php';
    }
}
?>
