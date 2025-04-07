<?php
namespace App\Models;

class Post {
    private $pdo;
    public $id;
    public $forum_id;
    public $user_id;
    public $content;
    public $created_at;
    
    public function __construct($pdo) {
        $this->pdo = $pdo;
    }
    
    // Busca um post pelo ID
    public function find($id) {
        $stmt = $this->pdo->prepare("SELECT * FROM posts WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(\PDO::FETCH_OBJ);
    }
    
    // Cria um novo post
    public function create(array $data) {
        $stmt = $this->pdo->prepare("INSERT INTO posts (forum_id, user_id, content, created_at) VALUES (?, ?, ?, NOW())");
        return $stmt->execute([$data['forum_id'], $data['user_id'], $data['content']]);
    }
    
    // Atualiza o conteúdo de um post
    public function update($id, array $data) {
        $stmt = $this->pdo->prepare("UPDATE posts SET content = ? WHERE id = ?");
        return $stmt->execute([$data['content'], $id]);
    }
    
    // Remove um post
    public function delete($id) {
        $stmt = $this->pdo->prepare("DELETE FROM posts WHERE id = ?");
        return $stmt->execute([$id]);
    }
}
?>
