<?php
namespace App\Services;

use App\Models\User;

class AuthService {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    /**
     * Valida as credenciais do usuário.
     * Retorna o objeto usuário se as credenciais estiverem corretas ou false caso contrário.
     */
    public function login($email, $password) {
        $stmt = $this->pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch(\PDO::FETCH_OBJ);

        if ($user && password_verify($password, $user->password)) {
            return $user;
        }

        return false;
    }

    /**
     * Registra um novo usuário.
     * Retorna true se o registro for bem-sucedido ou false em caso de erro.
     */
    public function register(array $data) {
        // Validação dos dados pode ser adicionada aqui
        $userModel = new User($this->pdo);
        return $userModel->create([
            'name'     => $data['name'],
            'email'    => $data['email'],
            'password' => $data['password']
        ]);
    }
}
?>
