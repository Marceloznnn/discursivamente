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

        // Log inicial de debug
        error_log('===== ProfileController::update Iniciado =====');
        error_log('POST: ' . print_r($_POST, true));
        error_log('FILES: ' . print_r($_FILES, true));

        // 1) AÇÃO DE REMOÇÃO DE AVATAR
        if (!empty($_POST['remove_avatar']) && $_POST['remove_avatar'] === '1') {
            error_log('Remoção de avatar solicitada');
            if ($avatar = $user->getAvatar()) {
                error_log('Avatar atual para remoção: ' . $avatar);
                if (strpos($avatar, 'cloudinary') !== false) {
                    $publicId = $this->extractPublicId($avatar);
                    error_log('Public ID extraído para remoção: ' . $publicId);
                    if ($publicId) {
                        try {
                            $result = $this->cloudinaryService->deleteFile($publicId);
                            error_log('Resultado deleteFile: ' . print_r($result, true));
                        } catch (\Exception $e) {
                            error_log('Erro ao excluir avatar do Cloudinary: ' . $e->getMessage());
                        }
                    }
                }
                $user->setAvatar(null);
                error_log('Avatar setado como null no objeto User');
            }
            $this->userRepository->save($user);
            $_SESSION['user']['avatar'] = null;
            $_SESSION['flash_message']   = ['type' => 'success', 'message' => 'Avatar removido com sucesso.'];
            header('Location: /profile');
            exit;
        }

        // 2) AÇÃO DE UPLOAD DE NOVO AVATAR (antes de validar nome/bio)
        if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] === UPLOAD_ERR_OK) {
            error_log('Upload de avatar solicitado');

            // Excluir avatar antigo
            $oldAvatar = $user->getAvatar();
            if ($oldAvatar && strpos($oldAvatar, 'cloudinary') !== false) {
                $oldPublicId = $this->extractPublicId($oldAvatar);
                error_log('Public ID antigo para exclusão: ' . $oldPublicId);
                if ($oldPublicId) {
                    try {
                        $del = $this->cloudinaryService->deleteFile($oldPublicId);
                        error_log('Resultado deleteFile antigo: ' . print_r($del, true));
                    } catch (\Exception $e) {
                        error_log('Falha ao excluir avatar antigo: ' . $e->getMessage());
                    }
                }
            }

            // Upload do novo avatar
            try {
                $uploadResult = $this->cloudinaryService->upload(
                    $_FILES['avatar']['tmp_name'], 
                    'avatars'
                );
                error_log('Resultado upload: ' . print_r($uploadResult, true));
                if (!empty($uploadResult['url'])) {
                    $user->setAvatar($uploadResult['url']);
                    $this->userRepository->save($user);
                    $_SESSION['user']['avatar'] = $uploadResult['url'];
                    $_SESSION['flash_message']  = ['type' => 'success', 'message' => 'Avatar atualizado com sucesso.'];
                    header('Location: /profile');
                    exit;
                }
            } catch (\Exception $e) {
                error_log('Erro no upload do avatar: ' . $e->getMessage());
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
            error_log('Falha: nome vazio');
            echo $this->twig->render('profile/edit.twig', [
                'user'  => $user,
                'error' => 'Nome é obrigatório.'
            ]);
            return;
        }
        $user->setName($name);
        $user->setBio($bio);
        error_log('Nome e bio atualizados: ' . $name . ' / ' . $bio);

        // 4) SALVAR PERFIL COMPLETO
        $this->userRepository->save($user);
        $_SESSION['user']['name']   = $user->getName();
        $_SESSION['user']['avatar'] = $user->getAvatar();
        $_SESSION['flash_message']  = ['type' => 'success', 'message' => 'Perfil atualizado com sucesso.'];
        error_log('Perfil salvo e redirecionando para /profile');
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