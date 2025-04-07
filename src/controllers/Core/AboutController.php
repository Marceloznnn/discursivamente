<?php
namespace App\Controllers\Core;

class AboutController {
    // Exibe a página "Sobre o Projeto" ou "Quem Somos"
    public function index() {
        include BASE_PATH . '/src/views/core/about.php';
    }
}
