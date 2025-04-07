<?php
namespace App\Services;

class SearchService {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    /**
     * Pesquisa livros com base em um termo.
     * Retorna um array de resultados encontrados.
     */
    public function searchBooks($term) {
        $term = "%" . $term . "%";
        $stmt = $this->pdo->prepare("SELECT * FROM books WHERE title LIKE ? OR author LIKE ?");
        $stmt->execute([$term, $term]);
        return $stmt->fetchAll(\PDO::FETCH_OBJ);
    }

    /**
     * Pesquisa comunidades com base em um termo.
     * Retorna um array de comunidades encontradas.
     */
    public function searchCommunities($term) {
        $term = "%" . $term . "%";
        $stmt = $this->pdo->prepare("SELECT * FROM communities WHERE name LIKE ? OR description LIKE ?");
        $stmt->execute([$term, $term]);
        return $stmt->fetchAll(\PDO::FETCH_OBJ);
    }
}
?>
