<?php
namespace App\Models;

class Community {
    private $pdo;
    public $id;
    public $name;
    public $description;
    public $created_at;
    
    public function __construct($pdo) {
        $this->pdo = $pdo;
    }
    
    // Busca uma comunidade pelo ID
    public function find($id) {
        $stmt = $this->pdo->prepare("SELECT * FROM communities WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(\PDO::FETCH_OBJ);
    }
    
    // Cria uma nova comunidade
    public function create(array $data) {
        $stmt = $this->pdo->prepare("INSERT INTO communities (name, description, created_at) VALUES (?, ?, NOW())");
        return $stmt->execute([$data['name'], $data['description']]);
    }
    
    // Atualiza os dados de uma comunidade
    public function update($id, array $data) {
        $stmt = $this->pdo->prepare("UPDATE communities SET name = ?, description = ? WHERE id = ?");
        return $stmt->execute([$data['name'], $data['description'], $id]);
    }
    
    // Remove uma comunidade
    public function delete($id) {
        $stmt = $this->pdo->prepare("DELETE FROM communities WHERE id = ?");
        return $stmt->execute([$id]);
    }
}
?>
