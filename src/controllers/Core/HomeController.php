<?php
namespace Discursivamente\Controllers\Core;

class HomeController {
    // ...
    // Exibe a página inicial
    public function index() {
        include BASE_PATH . '/src/views/home/index.php';
    }
}
