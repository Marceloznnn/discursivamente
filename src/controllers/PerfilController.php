<?php 
// src/controllers/PerfilController.php

class PerfilController {

    // Página principal de perfil (perfil.php)
    public function index() {
        require_once BASE_PATH . '/src/views/perfil/perfil.php';
    }
    
    // Página para editar informações pessoais (editar_perfil.php)
    public function editarPerfil() {
        require_once BASE_PATH . '/src/views/perfil/editar_perfil.php';
    }
    
    // Página para redefinir a senha (redefinir_senha.php)
    public function redefinirSenha() {
        require_once BASE_PATH . '/src/views/perfil/redefinir_senha.php';
    }
    
    // Página de configurações gerais (configuracao.php)
    public function configuracao() {
        require_once BASE_PATH . '/src/views/perfil/configuracao.php';
    }
    
    // Página de livros salvos (salvos.php)
    public function salvos() {
        require_once BASE_PATH . '/src/views/perfil/salvos.php';
    }
    
    // Página de livros favoritos (favoritos.php)
    public function favoritos() {
        require_once BASE_PATH . '/src/views/perfil/favoritos.php';
    }
    
    // Página de gerenciamento de livros (gerenciar_livros.php)
    public function gerenciarLivros() {
        require_once BASE_PATH . '/src/views/perfil/gerenciar_livros.php';
    }
    
    // Página de histórico de atividades (historico.php)
    public function historico() {
        require_once BASE_PATH . '/src/views/perfil/historico.php';
    }
}
?>
