<?php
namespace App\Models;

class Forum {
    private $pdo;
    public $id;
    public $community_id;
    public $title;
    public $description;
    public $created_at;
    
    public function __construct($pdo) {
        $this->pdo = $pdo;
    }
    
    // Busca um fórum pelo ID
    public function find($id) {
        $stmt = $this->pdo->prepare("SELECT * FROM forums WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(\PDO::FETCH_OBJ);
    }
    
    // Cria um novo fórum
    public function create(array $data) {
        $stmt = $this->pdo->prepare("INSERT INTO forums (community_id, title, description, created_at) VALUES (?, ?, ?, NOW())");
        return $stmt->execute([$data['community_id'], $data['title'], $data['description']]);
    }
    
    // Atualiza os dados de um fórum
    public function update($id, array $data) {
        $stmt = $this->pdo->prepare("UPDATE forums SET title = ?, description = ? WHERE id = ?");
        return $stmt->execute([$data['title'], $data['description'], $id]);
    }
    
    // Remove um fórum
    public function delete($id) {
        $stmt = $this->pdo->prepare("DELETE FROM forums WHERE id = ?");
        return $stmt->execute([$id]);
    }
}
?>
