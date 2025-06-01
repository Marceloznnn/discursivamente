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
    
    private function checkAuthentication()
    {
        if (!isset($_SESSION['user'])) {
            header('Location: /login');
            exit;
        }
    }
    
    public function index()
    {
        $this->checkAuthentication();
        $userId = $_SESSION['user']['id'];
        $user = $this->userRepository->findById($userId);
        echo $this->twig->render('profile/index.twig', ['user' => $user]);
    }
    
    public function edit()
    {
        $this->checkAuthentication();
        $userId = $_SESSION['user']['id'];
        $user = $this->userRepository->findById($userId);
        echo $this->twig->render('profile/edit.twig', ['user' => $user]);
    }
    
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

        // 1) AÇÃO DE REMOÇÃO DE AVATAR
        if (!empty($_POST['remove_avatar']) && $_POST['remove_avatar'] === '1') {
            if ($avatar = $user->getAvatar()) {
                if (strpos($avatar, 'cloudinary') !== false) {
                    $publicId = $this->extractPublicId($avatar);
                    if ($publicId) {
                        try {
                            $result = $this->cloudinaryService->deleteFile($publicId);
                        } catch (\Exception $e) {
                            // Tratar erro de exclusão de avatar no Cloudinary se necessário
                        }
                    }
                }
                $user->setAvatar(null);
            }
            $this->userRepository->save($user);
            $_SESSION['user']['avatar'] = null;
            $_SESSION['flash_message']   = ['type' => 'success', 'message' => 'Avatar removido com sucesso.'];
            header('Location: /profile');
            exit;
        }

        // 2) AÇÃO DE UPLOAD DE NOVO AVATAR (antes de validar nome/bio)
        if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] === UPLOAD_ERR_OK) {

            // Excluir avatar antigo
            $oldAvatar = $user->getAvatar();
            if ($oldAvatar && strpos($oldAvatar, 'cloudinary') !== false) {
                $oldPublicId = $this->extractPublicId($oldAvatar);
                if ($oldPublicId) {
                    try {
                        $del = $this->cloudinaryService->deleteFile($oldPublicId);
                    } catch (\Exception $e) {
                        // Tratar erro de exclusão de avatar antigo se necessário
                    }
                }
            }

            // Upload do novo avatar
            try {
                $uploadResult = $this->cloudinaryService->upload(
                    $_FILES['avatar']['tmp_name'], 
                    'avatars'
                );
                if (!empty($uploadResult['url'])) {
                    $user->setAvatar($uploadResult['url']);
                    $this->userRepository->save($user);
                    $_SESSION['user']['avatar'] = $uploadResult['url'];
                    $_SESSION['flash_message']  = ['type' => 'success', 'message' => 'Avatar atualizado com sucesso.'];
                    header('Location: /profile');
                    exit;
                }
            } catch (\Exception $e) {
                echo $this->twig->render('profile/edit.twig', [
                    'user'  => $user,
                    'error' => 'Erro ao fazer upload do avatar: ' . $e->getMessage()
                ]);
                return;
            }
        }

        // 3) AÇÃO DE ATUALIZAÇÃO DE NOME E BIO
        $name = $_POST['name'] ?? '';
        $bio  = $_POST['bio']  ?? '';
        if (!$name) {
            echo $this->twig->render('profile/edit.twig', [
                'user'  => $user,
                'error' => 'Nome é obrigatório.'
            ]);
            return;
        }
        $user->setName($name);
        $user->setBio($bio);

        // 4) SALVAR PERFIL COMPLETO
        $this->userRepository->save($user);
        $_SESSION['user']['name']   = $user->getName();
        $_SESSION['user']['avatar'] = $user->getAvatar();
        $_SESSION['flash_message']  = ['type' => 'success', 'message' => 'Perfil atualizado com sucesso.'];
        header('Location: /profile');
        exit;
    }
    
    private function extractPublicId($url)
    {
        $path = parse_url($url, PHP_URL_PATH);
        $parts = explode('/', $path);

        $uploadIndex = array_search('upload', $parts);
        if ($uploadIndex === false) {
            return null;
        }

        $relevantParts = array_slice($parts, $uploadIndex + 1);
        if (isset($relevantParts[0]) && preg_match('/^v\d+$/', $relevantParts[0])) {
            array_shift($relevantParts);
        }

        $last = array_pop($relevantParts);
        $last = pathinfo($last, PATHINFO_FILENAME);
        $relevantParts[] = $last;

        return implode('/', $relevantParts);
    }
}