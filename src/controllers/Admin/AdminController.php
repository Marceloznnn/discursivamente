<?php
namespace Discursivamente\Controllers\Admin;

class AdminController {
    public function apiGetStats() {
        return ['success' => true, 'data' => ['stats' => 'Dados de estatísticas']];
    }
    
    public function apiGetUsers() {
        return ['success' => true, 'data' => []];
    }
    
    public function apiUpdateUser() {
        return ['success' => true, 'message' => 'Usuário atualizado'];
    }
    
    public function apiDeleteUser() {
        return ['success' => true, 'message' => 'Usuário removido'];
    }
}
