<?php
// /src/controllers/HomeController.php

class HomeController {
    public function index() {
        global $pdo; // Torna $pdo acessível neste método
        $title = "Página Inicial - Discursivamente";
        require_once BASE_PATH . '/src/views/home.php';
    }
}
?>
