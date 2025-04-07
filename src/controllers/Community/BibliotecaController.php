<?php
namespace Discursivamente\Controllers\Community;

class BibliotecaController {
    private $pdo;
    
    public function __construct($pdo) {
        $this->pdo = $pdo;
    }
    
    public function index() {
        // Lógica para listar itens da biblioteca da comunidade
        include BASE_PATH . '/src/Views/community/biblioteca.php';
    }
}
