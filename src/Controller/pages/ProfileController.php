<?php
// filepath: c:\xampp\htdocs\Discursivamente2.1\src\Controller\pages\ProfileController.php

namespace Controller\pages;

use Services\CloudinaryService;
use Infrastructure\Database\Connection;
use Repositories\UserRepository;
use App\Models\User;

class ProfileController {
    private $twig;
    private $cloudinaryService;
    private $userRepository;
    
    public function __construct($twig)
    {
        $this->twig = $twig;
        $this->cloudinaryService = new CloudinaryService();
        $connection = Connection::getInstance();
        $this->userRepository = new UserRepository($connection);
    }
    
    /**
     * Verifica se o usuário está logado
     */
    private function checkAuthentication()
    {
        if (!isset($_SESSION['user'])) {
            header('Location: /login');
            exit;
        }
    }
    
    /**
     * Perfil do usuário
     */
    public function index()
    {
        $this->checkAuthentication();
        
        $userId = $_SESSION['user']['id'];
        $user = $this->userRepository->findById($userId);
        
        echo $this->twig->render('profile/index.twig', [
            'user' => $user
        ]);
    }
    
    /**
     * Formulário de edição de perfil
     */
    public function edit()
    {
        $this->checkAuthentication();
        
        $userId = $_SESSION['user']['id'];
        $user = $this->userRepository->findById($userId);
        
        echo $this->twig->render('profile/edit.twig', [
            'user' => $user
        ]);
    }
    
    /**
     * Processa a atualização do perfil
     */
    public function update()
    {
        $this->checkAuthentication();
        
        $userId = $_SESSION['user']['id'];
        $user = $this->userRepository->findById($userId);
        
        if (!$user) {
            $_SESSION['flash_message'] = ['type' => 'error', 'message' => 'Usuário não encontrado.'];
            header('Location: /profile');
            exit;
        }
        
        $name = $_POST['name'] ?? '';
        $bio = $_POST['bio'] ?? '';
        
        if (!$name) {
            echo $this->twig->render('profile/edit.twig', [
                'user' => $user,
                'error' => 'Nome é obrigatório.'
            ]);
            return;
        }
        
        $user->setName($name);
        $user->setBio($bio);
        
        // Upload de avatar para o Cloudinary
        if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] === UPLOAD_ERR_OK) {
            try {
                // Faz upload do arquivo para o Cloudinary
                $result = $this->cloudinaryService->upload(
                    $_FILES['avatar']['tmp_name'],
                    'avatars' // Pasta onde será armazenado no Cloudinary
                );
                
                // Se o upload foi bem-sucedido, atualiza o avatar do usuário
                if ($result) {
                    // Se o usuário já tinha um avatar, exclui o anterior
                    if ($user->getAvatar() && strpos($user->getAvatar(), 'cloudinary') !== false) {
                        // Extrai o public_id do avatar atual
                        $publicId = $this->extractPublicId($user->getAvatar());
                        if ($publicId) {
                            $this->cloudinaryService->deleteFile($publicId);
                        }
                    }
                    
                    // Atualiza o avatar para a nova URL do Cloudinary
                    $user->setAvatar($result['url']);
                }
            } catch (\Exception $e) {
                echo $this->twig->render('profile/edit.twig', [
                    'user' => $user,
                    'error' => 'Erro ao fazer upload do avatar: ' . $e->getMessage()
                ]);
                return;
            }
        }
        
        $this->userRepository->save($user);
        
        $_SESSION['user']['name'] = $user->getName();
        $_SESSION['user']['avatar'] = $user->getAvatar();
        
        $_SESSION['flash_message'] = ['type' => 'success', 'message' => 'Perfil atualizado com sucesso.'];
        header('Location: /profile');
        exit;
    }
    
    /**
     * Extrai o public_id do Cloudinary a partir da URL
     * 
     * @param string $url URL do Cloudinary
     * @return string|null Public ID ou null se não for possível extrair
     */
    private function extractPublicId($url)
    {
        // Exemplo de URL do Cloudinary:
        // https://res.cloudinary.com/cloud-name/image/upload/v1234567890/pasta/arquivo.jpg
        
        // Padrão para extrair o public_id
        $pattern = '/\/v\d+\/([^\/]+\/[^\.]+)/';
        
        if (preg_match($pattern, $url, $matches)) {
            return $matches[1];
        }
        
        return null;
    }
}