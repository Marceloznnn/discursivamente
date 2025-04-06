<?php
// Caminho absoluto: /discursivamente/src/controllers/QuemSomosController.php

class QuemSomosController {
    public function index() {
        $title = "Quem Somos - Discursivamente";
        require_once BASE_PATH . '/src/views/quem-somos.php';
    }
}
