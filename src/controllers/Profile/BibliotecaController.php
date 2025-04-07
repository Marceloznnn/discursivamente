<?php
namespace App\Controllers\Profile;

class BibliotecaController {
    private $pdo;
    
    public function __construct($pdo) {
        $this->pdo = $pdo;
    }
    
    // Exibe a biblioteca pessoal do usuário
    public function index() {
        include BASE_PATH . '/src/views/profile/biblioteca.php';
    }
    
    // Exibe os detalhes de um livro específico do perfil
    public function details($id) {
        include BASE_PATH . '/src/views/profile/book-detail.php';
    }
}
