<?php
namespace Discursivamente\Controllers\Core;

class SearchController {
    private $pdo;
    
    public function __construct($pdo) {
        $this->pdo = $pdo;
    }
    
    public function index() {
        // Lógica para pesquisa na aplicação
        $query = trim($_GET['q'] ?? '');
        // Processamento da pesquisa, por exemplo:
        // $results = ...;
        include BASE_PATH . '/src/Views/core/search.php';
    }
    public function apiSearch() {
        $query = trim($_GET['q'] ?? '');
        // Lógica para pesquisa
        return ['success' => true, 'data' => []];
    }
    
}
