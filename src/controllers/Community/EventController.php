<?php
namespace App\Controllers\Community;

class EventController {
    private $pdo;
    
    public function __construct($pdo) {
        $this->pdo = $pdo;
    }
    
    // Lista os eventos
    public function index() {
        include BASE_PATH . '/src/views/community/event/index.php';
    }
    
    // Exibe os detalhes de um evento específico
    public function view($id) {
        include BASE_PATH . '/src/views/community/event/view.php';
    }
}
