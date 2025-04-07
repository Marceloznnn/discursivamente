<?php
namespace Discursivamente\Controllers\Core;

class QuemSomosController {
    public function index() {
        // Lógica para a página "Quem Somos" ou "Sobre"
        include BASE_PATH . '/src/Views/core/quem-somos.php';
    }
}
