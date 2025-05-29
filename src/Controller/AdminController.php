<?php
// filepath: c:\xampp\htdocs\Discursivamente2.1\src\Controller\AdminController.php

namespace Controller;

use Infrastructure\Database\Connection;
use Repositories\UserRepository;
use Repositories\EventRepository;

class AdminController {
    private $twig;
    private $userRepository; 
    private $eventRepository;

    public function __construct($twig)
    {
        $this->twig = $twig;
        $connection = Connection::getInstance();

        $this->userRepository         = new UserRepository($connection);
        $this->eventRepository        = new EventRepository($connection);
    }

    private function checkAdminAccess()
    {
        if (!isset($_SESSION['user'])) {
            header('Location: /login');
            exit;
        }
        if ($_SESSION['user']['type'] !== 'admin') {
            header('Location: /');
            exit;
        }
    }

    // === DASHBOARD ===
    public function index()
    {
        $this->checkAdminAccess();

        echo $this->twig->render('admin/index.twig');
    }

    // === USUÁRIOS ===
    public function usersList()
    {
        $this->checkAdminAccess();

        $users = $this->userRepository->findAll();
        echo $this->twig->render('admin/users/index.twig', ['users' => $users]);
    }

    public function userView(int $id)
    {
        $this->checkAdminAccess();

        $user = $this->userRepository->findById($id);
        if (!$user) {
            $_SESSION['flash_message'] = ['type'=>'error','message'=>'Usuário não encontrado.'];
            header('Location: /admin/users');
            exit;
        }

        echo $this->twig->render('admin/users/view.twig', ['user' => $user]);
    }

    public function userCreate()
    {
        $this->checkAdminAccess();
        echo $this->twig->render('admin/users/create.twig');
    }

    public function userStore()
    {
        $this->checkAdminAccess();

        $name     = $_POST['name']     ?? '';
        $email    = $_POST['email']    ?? '';
        $password = $_POST['password'] ?? '';
        $type     = $_POST['type']     ?? 'user';
        $bio      = $_POST['bio']      ?? null;

        if (!$name || !$email || !$password) {
            echo $this->twig->render('admin/users/create.twig', [
                'error' => 'Nome, e-mail e senha são obrigatórios.',
                'user'  => compact('name','email','type','bio')
            ]);
            return;
        }

        $hash = password_hash($password, PASSWORD_DEFAULT);

        $user = new \App\Models\User(
            $name,
            $email,
            $hash,
            $type,
            null,
            null,
            null,
            null,
            date('Y-m-d H:i:s'),
            date('Y-m-d H:i:s'),
            $bio
        );
        $this->userRepository->save($user);

        $_SESSION['flash_message'] = ['type'=>'success','message'=>'Usuário criado com sucesso.'];
        header('Location: /admin/users');
        exit;
    }

    public function userEdit(int $id)
    {
        $this->checkAdminAccess();

        $user = $this->userRepository->findById($id);
        if (!$user) {
            $_SESSION['flash_message'] = ['type'=>'error','message'=>'Usuário não encontrado.'];
            header('Location: /admin/users');
            exit;
        }

        echo $this->twig->render('admin/users/edit.twig', ['user' => $user]);
    }

    public function userUpdate(int $id)
    {
        $this->checkAdminAccess();

        $user = $this->userRepository->findById($id);
        if (!$user) {
            $_SESSION['flash_message'] = ['type'=>'error','message'=>'Usuário não encontrado.'];
            header('Location: /admin/users');
            exit;
        }

        $name  = $_POST['name']  ?? '';
        $email = $_POST['email'] ?? '';
        $type  = $_POST['type']  ?? 'user';
        $bio   = $_POST['bio']   ?? '';

        if (!$name || !$email) {
            echo $this->twig->render('admin/users/edit.twig', [
                'user'  => $user,
                'error' => 'Nome e e-mail são obrigatórios.'
            ]);
            return;
        }

        $user->setName($name);
        $user->setEmail($email);
        $user->setType($type);
        $user->setBio($bio);

        if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] === UPLOAD_ERR_OK) {
            $uploadDir = __DIR__ . '/../../public/uploads/avatars/';
            if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);

            $fileName = time() . '_' . basename($_FILES['avatar']['name']);
            $filePath = $uploadDir . $fileName;

            if (move_uploaded_file($_FILES['avatar']['tmp_name'], $filePath)) {
                $user->setAvatar('/uploads/avatars/' . $fileName);
            }
        }

        $this->userRepository->save($user);

        $_SESSION['flash_message'] = ['type'=>'success','message'=>'Usuário atualizado com sucesso.'];
        header('Location: /admin/users');
        exit;
    }

    // === CONVERSAS ===
    /*
    public function conversationsList()
    {
        $this->checkAdminAccess();
        $conversations = $this->conversationRepository->findAll();
        echo $this->twig->render('admin/conversations/index.twig', ['conversations' => $conversations]);
    }

    public function conversationView(int $id)
    {
        $this->checkAdminAccess();
        $conversation = $this->conversationRepository->findById($id);
        if (!$conversation) {
            $_SESSION['flash_message'] = ['type'=>'error','message'=>'Conversa não encontrada.'];
            header('Location: /admin/conversations');
            exit;
        }
        echo $this->twig->render('admin/conversations/view.twig', compact('conversation'));
    }
    */

    // === EVENTOS ===
    public function eventsList()
    {
        $this->checkAdminAccess();
        $events = $this->eventRepository->findAll();
        echo $this->twig->render('admin/events/index.twig', ['events' => $events]);
    }

    public function eventCreate()
    {
        $this->checkAdminAccess();
        echo $this->twig->render('admin/events/form.twig', ['event' => null]);
    }

    public function eventStore()
    {
        $this->checkAdminAccess();

        $title            = $_POST['title'] ?? '';
        $description      = $_POST['description'] ?? '';
        $dateTime         = $_POST['date_time'] ?? '';
        $visibility       = $_POST['visibility'] ?? 'public';
        $isFeatured       = isset($_POST['is_featured']) ? 1 : 0;
        $featurePriority  = (int)($_POST['feature_priority'] ?? 0);

        if (!$title || !$description || !$dateTime) {
            echo $this->twig->render('admin/events/form.twig', [
                'error' => 'Todos os campos são obrigatórios.',
                'event' => compact('title','description','dateTime','visibility','isFeatured','featurePriority')
            ]);
            return;
        }

        $imageUrl = null;
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $uploadDir = __DIR__ . '/../../public/uploads/events/';
            if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);
            $fileName = time() . '_' . basename($_FILES['image']['name']);
            $filePath = $uploadDir . $fileName;
            if (move_uploaded_file($_FILES['image']['tmp_name'], $filePath)) {
                $imageUrl = '/uploads/events/' . $fileName;
            }
        }

        $event = new \App\Models\Event(
            $title,
            $description,
            $dateTime,
            $visibility,
            $imageUrl,
            $isFeatured,
            $featurePriority
        );
        $this->eventRepository->save($event);

        $_SESSION['flash_message'] = ['type'=>'success','message'=>'Evento criado com sucesso.'];
        header('Location: /admin/events');
        exit;
    }

    public function eventEdit(int $id)
    {
        $this->checkAdminAccess();
        $event = $this->eventRepository->findById($id);
        if (!$event) {
            $_SESSION['flash_message'] = ['type'=>'error','message'=>'Evento não encontrado.'];
            header('Location: /admin/events'); exit;
        }
        echo $this->twig->render('admin/events/form.twig', ['event' => $event]);
    }

    public function eventUpdate(int $id)
    {
        $this->checkAdminAccess();

        $event            = $this->eventRepository->findById($id);
        $title            = $_POST['title'] ?? '';
        $description      = $_POST['description'] ?? '';
        $dateTime         = $_POST['date_time'] ?? '';
        $visibility       = $_POST['visibility'] ?? 'public';
        $isFeatured       = isset($_POST['is_featured']) ? 1 : 0;
        $featurePriority  = (int)($_POST['feature_priority'] ?? 0);

        if (!$title || !$description || !$dateTime) {
            echo $this->twig->render('admin/events/form.twig', [
                'error' => 'Todos os campos são obrigatórios.',
                'event' => compact('id','title','description','dateTime','visibility','isFeatured','featurePriority')
            ]);
            return;
        }

        $imageUrl = null;
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $uploadDir = __DIR__ . '/../../public/uploads/events/';
            if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);
            $fileName = time() . '_' . basename($_FILES['image']['name']);
            $filePath = $uploadDir . $fileName;
            if (move_uploaded_file($_FILES['image']['tmp_name'], $filePath)) {
                $imageUrl = '/uploads/events/' . $fileName;
            }
        }
        if ($imageUrl === null) {
            $imageUrl = $event ? $event->getImage() : null;
        }

        $updatedEvent = new \App\Models\Event(
            $title,
            $description,
            $dateTime,
            $visibility,
            $imageUrl,
            $isFeatured,
            $featurePriority,
            $id
        );
        $this->eventRepository->save($updatedEvent);
        $_SESSION['flash_message'] = ['type'=>'success','message'=>'Evento atualizado com sucesso.'];
        header('Location: /admin/events'); exit;
    }

    public function eventDelete(int $id)
    {
        $this->checkAdminAccess();
        $this->eventRepository->delete($id);
        $_SESSION['flash_message'] = ['type'=>'success','message'=>'Evento excluído com sucesso.'];
        header('Location: /admin/events'); exit;
    }

    // === SUPORTE ADMIN ===
    public function supportChatsList()
    {
        $this->checkAdminAccess();
        $chats = $this->getSupportChatsWithLastMessage();
        echo $this->twig->render('admin/support/list.twig', ['chats' => $chats]);
    }

    public function supportChatView($chatId)
    {
        $this->checkAdminAccess();
        // Log para debug
        error_log('DEBUG supportChatView $chatId: ' . print_r($chatId, true));
        // Proteção extra: se vier um objeto, tenta extrair o chatId ou retorna erro amigável
        if (is_object($chatId)) {
            if (property_exists($chatId, 'chat_id')) {
                $chatId = $chatId->chat_id;
            } else {
                echo $this->twig->render('errors/500.twig', [
                    'message' => 'Erro interno: parâmetro de chat inválido.'
                ]);
                return;
            }
        }  
        $messages = $this->getSupportMessagesByChatId($chatId);
        echo $this->twig->render('admin/support/chat.twig', [
            'chatId'   => $chatId,
            'messages' => $messages
        ]);
    }

    public function supportChatReply($chatId)
    {
        $this->checkAdminAccess();
        $message = trim($_POST['message'] ?? '');
        if ($message !== '') {
            $adminId = $_SESSION['user']['id'];
            $this->saveSupportMessage($chatId, $adminId, $message, 'admin');
        }
        header("Location: /admin/support/chats/{$chatId}");
        exit;
    }

    public function supportChatClear($chatId)
    {
        $this->checkAdminAccess();
        $pdo = \Infrastructure\Database\Connection::getInstance();
        // Remove todas as mensagens do chat
        $stmt = $pdo->prepare("DELETE FROM support_messages WHERE chat_id = ?");
        $stmt->execute([$chatId]);
        $_SESSION['flash_message'] = ['type'=>'success','message'=>'Mensagens da conversa removidas com sucesso.'];
        header("Location: /admin/support/chats/{$chatId}");
        exit;
    }

    public function supportChatClose($chatId)
    {
        $this->checkAdminAccess();
        $pdo = \Infrastructure\Database\Connection::getInstance();
        // Marca o chat como encerrado (precisa de coluna status em support_chats ou similar)
        $stmt = $pdo->prepare("UPDATE support_chats SET status = 'closed' WHERE chat_id = ?");
        $stmt->execute([$chatId]);
        $_SESSION['flash_message'] = ['type'=>'success','message'=>'Atendimento encerrado com sucesso.'];
        header("Location: /admin/support/chats/{$chatId}");
        exit;
    }

    // Métodos auxiliares para buscar chats e mensagens
    private function getSupportChatsWithLastMessage()
    {
        $pdo = \Infrastructure\Database\Connection::getInstance();
        $sql = "SELECT chat_id, user_id, MAX(created_at) as last_time,
                       (SELECT message FROM support_messages WHERE chat_id = sc.chat_id ORDER BY created_at DESC LIMIT 1) as last_message,
                       (SELECT name FROM users WHERE id = sc.user_id) as user_name
                FROM support_messages sc
                GROUP BY chat_id, user_id
                ORDER BY last_time DESC";
        return $pdo->query($sql)->fetchAll(\PDO::FETCH_ASSOC);
    }

    private function getSupportMessagesByChatId($chatId)
    {
        // Garante que não é objeto
        if (is_object($chatId)) {
            if (property_exists($chatId, 'chat_id')) {
                $chatId = $chatId->chat_id;
            } else {
                return [];
            }
        }
        $pdo = \Infrastructure\Database\Connection::getInstance();
        $stmt = $pdo->prepare("SELECT sm.*, u.name as user_name FROM support_messages sm LEFT JOIN users u ON sm.user_id = u.id WHERE chat_id = ? ORDER BY created_at ASC");
        $stmt->execute([$chatId]);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    private function saveSupportMessage($chatId, $userId, $message, $sender = 'admin')
    {
        $pdo = \Infrastructure\Database\Connection::getInstance();
        $stmt = $pdo->prepare("INSERT INTO support_messages (chat_id, user_id, message, sender, created_at) VALUES (?, ?, ?, ?, NOW())");
        $stmt->execute([$chatId, $userId, $message, $sender]);
    }
}
