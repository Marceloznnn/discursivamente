<?php
namespace Discursivamente\Controllers\Community;

class CommunityController 
{
    public function index() 
    {
        require_once __DIR__ . '/../../views/community/comunicacao.php';
    }
    
    public function foruns() 
    {
        require_once __DIR__ . '/../../views/community/foruns.php';
    }
    
    public function clubeLivros() 
    {
        require_once __DIR__ . '/../../views/community/clube-livros.php';
    }
    
    public function grupo() 
    {
        require_once __DIR__ . '/../../views/community/grupo.php';
    }
    
    public function search()
    {
        $termo = filter_input(INPUT_GET, 'q', FILTER_SANITIZE_SPECIAL_CHARS);
        require_once __DIR__ . '/../../views/community/search.php';
    }

    public function view($id = null)
    {
        $id = filter_var($id, FILTER_VALIDATE_INT);
        if (!$id) {
            header('Location: /community');
            exit;
        }
        
        require_once __DIR__ . '/../../views/community/view.php';
    }
}