<?php
namespace App\Services;

use App\Models\Community;

class CommunityService {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    /**
     * Cria uma nova comunidade.
     * Retorna true se for bem-sucedido ou false em caso de erro.
     */
    public function createCommunity(array $data) {
        // Pode incluir validações e regras de negócio específicas
        $communityModel = new Community($this->pdo);
        return $communityModel->create([
            'name'        => $data['name'],
            'description' => $data['description']
        ]);
    }

    /**
     * Atualiza os dados de uma comunidade existente.
     */
    public function updateCommunity($id, array $data) {
        $communityModel = new Community($this->pdo);
        return $communityModel->update($id, [
            'name'        => $data['name'],
            'description' => $data['description']
        ]);
    }

    /**
     * Remove uma comunidade.
     */
    public function deleteCommunity($id) {
        $communityModel = new Community($this->pdo);
        return $communityModel->delete($id);
    }
}
?>
