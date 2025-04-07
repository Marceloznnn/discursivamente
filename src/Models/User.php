<?php

namespace App\Models;

class User {
    private $pdo;
    public $id;
    public $name;
    public $email;
    public $password;
    public $profileImage = null; // Propriedade opcional para imagem do perfil

    /**
     * Construtor recebe o objeto PDO para as operações de banco.
     *
     * @param \PDO $pdo
     */
    public function __construct(\PDO $pdo) {
        $this->pdo = $pdo;
    }
    
    /**
     * Cria um novo usuário.
     *
     * @param array $data Dados do usuário (name, email, password e opcional profileImage).
     * @return bool Retorna true em caso de sucesso, false em caso de erro.
     */
    public function create(array $data): bool {
        try {
            // Verifica se profileImage foi fornecido, caso contrário, define como null.
            $profileImage = $data['profileImage'] ?? null;
            $stmt = $this->pdo->prepare("INSERT INTO users (name, email, password, profileImage) VALUES (?, ?, ?, ?)");
            $hashedPassword = password_hash($data['password'], PASSWORD_BCRYPT);
            if (!$stmt->execute([$data['name'], $data['email'], $hashedPassword, $profileImage])) {
                error_log("Erro ao criar usuário: " . implode(" - ", $stmt->errorInfo()));
                return false;
            }
            return true;
        } catch (\PDOException $e) {
            error_log("Erro ao criar usuário: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Encontra um usuário pelo email.
     *
     * @param string $email
     * @return bool Retorna true se o usuário for encontrado, false caso contrário.
     */
    public function findByEmail(string $email): bool {
        $stmt = $this->pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch(\PDO::FETCH_ASSOC);
        
        if ($user) {
            $this->id = $user['id'];
            $this->name = $user['name'];
            $this->email = $user['email'];
            $this->password = $user['password'];
            $this->profileImage = $user['profileImage'] ?? null;
            return true;
        }
        
        return false;
    }
    
    /**
     * Verifica se a senha fornecida corresponde à senha armazenada.
     *
     * @param string $password
     * @return bool
     */
    public function checkPassword(string $password): bool {
        return password_verify($password, $this->password);
    }
    
    /**
     * Atualiza as informações do usuário.
     *
     * @param array $data Dados para atualização.
     * @return bool
     */
    public function update(array $data): bool {
        try {
            // Se profileImage for fornecido no array de atualização, inclua-o na atualização.
            if (!empty($data['password'])) {
                $data['password'] = password_hash($data['password'], PASSWORD_BCRYPT);
                $stmt = $this->pdo->prepare("UPDATE users SET name = ?, email = ?, password = ?, profileImage = ? WHERE id = ?");
                $profileImage = $data['profileImage'] ?? $this->profileImage;
                return $stmt->execute([$data['name'], $data['email'], $data['password'], $profileImage, $this->id]);
            } else {
                $stmt = $this->pdo->prepare("UPDATE users SET name = ?, email = ?, profileImage = ? WHERE id = ?");
                $profileImage = $data['profileImage'] ?? $this->profileImage;
                return $stmt->execute([$data['name'], $data['email'], $profileImage, $this->id]);
            }
        } catch (\PDOException $e) {
            error_log("Erro ao atualizar usuário: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Exclui o usuário.
     *
     * @return bool
     */
    public function delete(): bool {
        try {
            $stmt = $this->pdo->prepare("DELETE FROM users WHERE id = ?");
            return $stmt->execute([$this->id]);
        } catch (\PDOException $e) {
            error_log("Erro ao excluir usuário: " . $e->getMessage());
            return false;
        }
    }
}
