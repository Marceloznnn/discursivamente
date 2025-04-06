<?php 
// Caminho absoluto: C:\xampp\htdocs\DISCURSIVAMENTE\src\controllers\CommunicationController.php

class CommunicationController {
    // Página principal de comunicação (comunidade)
    public function index() {
        $title = "Comunicação - Discursivamente";
        require_once BASE_PATH . '/src/views/pages/comunidade/comunicacao.php';
    }
    
    // Método para renderizar a página de fóruns
    public function foruns() {
        $title = "Fóruns - Discursivamente";
        require_once BASE_PATH . '/src/views/pages/comunidade/foruns.php';
    }
    
    // Método para renderizar a página do clube de livros
    public function clubeLivros() {
        $title = "Clube de Livros - Discursivamente";
        require_once BASE_PATH . '/src/views/pages/comunidade/clube_livros.php';
    }
    
    // Método para renderizar a página de grupo
    public function grupo() {
        $title = "Grupo - Discursivamente";
        require_once BASE_PATH . '/src/views/pages/comunidade/grupo.php';
    }
}
?>
