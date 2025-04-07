<?php
namespace Discursivamente\Controllers\Community;

class CommunitySearchController {
    private $pdo;
    
    public function __construct($pdo) {
        $this->pdo = $pdo;
    }
    
    public function index() {
        // Lógica para pesquisa específica de comunidades
        $query = trim($_GET['q'] ?? '');
        // Processamento da pesquisa para comunidades
        include BASE_PATH . '/src/Views/community/search.php';
    }
}
