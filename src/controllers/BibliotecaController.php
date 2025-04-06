<?php
// Caminho absoluto: /discursivamente/src/controllers/BibliotecaController.php

class BibliotecaController {
    private $pdo;

    // Recebe o objeto PDO no construtor
    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function index() {
        $title = "Biblioteca - Discursivamente";

        // Exemplo: Buscar todos os livros literários
        $stmt = $this->pdo->query("SELECT * FROM books WHERE category = 'literario'");
        $livros = $stmt->fetchAll();

        // Inclui a view e passa as variáveis necessárias
        require_once BASE_PATH . '/src/views/biblioteca.php';
    }
}
