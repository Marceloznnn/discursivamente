<?php
namespace App\Models;

class Comment {
    private $pdo;
    public $id;
    public $post_id;
    public $user_id;
    public $content;
    public $created_at;
    
    public function __construct($pdo) {
        $this->pdo = $pdo;
    }
    
    // Busca um comentário pelo ID
    public function find($id) {
        $stmt = $this->pdo->prepare("SELECT * FROM comments WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(\PDO::FETCH_OBJ);
    }
    
    // Cria um novo comentário
    public function create(array $data) {
        $stmt = $this->pdo->prepare("INSERT INTO comments (post_id, user_id, content, created_at) VALUES (?, ?, ?, NOW())");
        return $stmt->execute([$data['post_id'], $data['user_id'], $data['content']]);
    }
    
    // Atualiza um comentário
    public function update($id, array $data) {
        $stmt = $this->pdo->prepare("UPDATE comments SET content = ? WHERE id = ?");
        return $stmt->execute([$data['content'], $id]);
    }
    
    // Remove um comentário
    public function delete($id) {
        $stmt = $this->pdo->prepare("DELETE FROM comments WHERE id = ?");
        return $stmt->execute([$id]);
    }
}
?>
