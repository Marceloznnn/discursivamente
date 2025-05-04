<?php
// filepath: c:\xampp\htdocs\Discursivamente2.1\src\Controller\AdminController.php

namespace Controller;

use Infrastructure\Database\Connection;
use Repositories\UserRepository;
use Repositories\ConversationRepository;
use Repositories\MessageRepository;
use Repositories\FeedbackRepository;
use Repositories\EventRepository;
use App\Models\AuditLog;

class AdminController {
    private $twig;
    private $userRepository;
    private $conversationRepository;
    private $messageRepository;
    private $feedbackRepository;
    private $auditLogRepository;
    private $eventRepository;
    
    // Função simples para registro de logs
    private function log($action, $resource, $resourceId = null, $meta = null) {
        $logFile = __DIR__ . '/../../logs/admin.log';
        $timestamp = date('Y-m-d H:i:s');
        $logMessage = "[$timestamp] $action $resource" . ($resourceId ? " ID: $resourceId" : "") . PHP_EOL;
        
        // Cria diretório de logs se não existir
        $logDir = dirname($logFile);
        if (!is_dir($logDir)) {
            mkdir($logDir, 0755, true);
        }
        
        file_put_contents($logFile, $logMessage, FILE_APPEND);
        
        // Adiciona na tabela de audit_logs se o usuário estiver logado
        if (isset($_SESSION['user']) && $_SESSION['user']['type'] === 'admin') {
            $adminId = $_SESSION['user']['id'];
            $auditLog = new AuditLog(
                $adminId,
                $action,
                $resource,
                $resourceId,
                $meta
            );
        }
    }

    public function __construct($twig)
    {
        $this->twig = $twig;
        $connection = Connection::getInstance();
        $this->userRepository = new UserRepository($connection);
        $this->conversationRepository = new ConversationRepository($connection);
        $this->messageRepository = new MessageRepository($connection);
        $this->feedbackRepository = new FeedbackRepository($connection);
        $this->eventRepository = new EventRepository($connection);
    }
    
    private function checkAdminAccess()
    {
        if (!isset($_SESSION['user'])) {
            header('Location: /login');
            exit;
        }
        
        if ($_SESSION['user']['type'] !== 'admin') {
            $this->log('acesso_negado', 'admin_panel', null, 'Usuário não é administrador');
            header('Location: /');
            exit;
        }
    }

    public function index()
    {
        $this->checkAdminAccess();
        $this->log('acessou', 'admin_dashboard');
        
        // Contagem de itens para dashboard
        $userCount = count($this->userRepository->findAll());
        $conversationCount = count($this->conversationRepository->findAll());
        $pendingFeedbacks = count($this->feedbackRepository->findByStatus('pending'));
        $upcomingEvents = count($this->eventRepository->findAll());
        
        echo $this->twig->render('admin/index.twig', [
            'userCount' => $userCount,
            'conversationCount' => $conversationCount,
            'pendingFeedbacks' => $pendingFeedbacks,
            'upcomingEvents' => $upcomingEvents
        ]);
    }
    
    // GERENCIAMENTO DE USUÁRIOS
    public function usersList()
    {
        $this->checkAdminAccess();
        $this->log('listou', 'users');
        
        $users = $this->userRepository->findAll();
        
        echo $this->twig->render('admin/users/index.twig', [
            'users' => $users
        ]);
    }
    
    public function userView($id)
    {
        $this->checkAdminAccess();
        $this->log('visualizou', 'user', $id);
        
        $user = $this->userRepository->findById($id);
        
        if (!$user) {
            $_SESSION['flash_message'] = ['type' => 'error', 'message' => 'Usuário não encontrado.'];
            header('Location: /admin/users');
            exit;
        }
        
        echo $this->twig->render('admin/users/view.twig', [
            'user' => $user
        ]);
    }
    
    public function userEdit($id)
    {
        $this->checkAdminAccess();
        $this->log('acessou_ediçao', 'user', $id);
        
        $user = $this->userRepository->findById($id);
        
        if (!$user) {
            $_SESSION['flash_message'] = ['type' => 'error', 'message' => 'Usuário não encontrado.'];
            header('Location: /admin/users');
            exit;
        }
        
        echo $this->twig->render('admin/users/edit.twig', [
            'user' => $user
        ]);
    }
    
    public function userUpdate($id)
    {
        $this->checkAdminAccess();
        
        $user = $this->userRepository->findById($id);
        
        if (!$user) {
            $_SESSION['flash_message'] = ['type' => 'error', 'message' => 'Usuário não encontrado.'];
            header('Location: /admin/users');
            exit;
        }
        
        $name = $_POST['name'] ?? '';
        $email = $_POST['email'] ?? '';
        $type = $_POST['type'] ?? 'user';
        $bio = $_POST['bio'] ?? '';
        
        if (!$name || !$email) {
            echo $this->twig->render('admin/users/edit.twig', [
                'user' => $user,
                'error' => 'Nome e email são obrigatórios.'
            ]);
            return;
        }
        
        $user->setName($name);
        $user->setEmail($email);
        $user->setType($type);
        $user->setBio($bio);
        
        // Upload de avatar, se enviado
        if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] === UPLOAD_ERR_OK) {
            $uploadDir = __DIR__ . '/../../public/uploads/avatars/';
            
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }
            
            $fileName = time() . '_' . basename($_FILES['avatar']['name']);
            $filePath = $uploadDir . $fileName;
            
            if (move_uploaded_file($_FILES['avatar']['tmp_name'], $filePath)) {
                $user->setAvatar('/uploads/avatars/' . $fileName);
            }
        }
        
        $this->userRepository->save($user);
        $this->log('atualizou', 'user', $id);
        
        $_SESSION['flash_message'] = ['type' => 'success', 'message' => 'Usuário atualizado com sucesso.'];
        header('Location: /admin/users');
        exit;
    }
    
    // GERENCIAMENTO DE CONVERSAS
    public function conversationsList()
    {
        $this->checkAdminAccess();
        $this->log('listou', 'conversations');
        
        $conversations = $this->conversationRepository->findAll();
        
        echo $this->twig->render('admin/conversations/index.twig', [
            'conversations' => $conversations
        ]);
    }
    
    public function conversationView($id)
    {
        $this->checkAdminAccess();
        $this->log('visualizou', 'conversation', $id);
        
        $conversation = $this->conversationRepository->findById($id);
        
        if (!$conversation) {
            $_SESSION['flash_message'] = ['type' => 'error', 'message' => 'Conversa não encontrada.'];
            header('Location: /admin/conversations');
            exit;
        }
        
        $messages = $this->messageRepository->findByConversation($id);
        
        echo $this->twig->render('admin/conversations/view.twig', [
            'conversation' => $conversation,
            'messages' => $messages
        ]);
    }
    
    // GERENCIAMENTO DE FEEDBACKS
    public function feedbacksList()
    {
        $this->checkAdminAccess();
        $this->log('listou', 'feedbacks');
        
        $feedbacks = $this->feedbackRepository->findAll();
        
        echo $this->twig->render('admin/feedbacks/index.twig', [
            'feedbacks' => $feedbacks
        ]);
    }
    
    public function feedbackPending()
    {
        $this->checkAdminAccess();
        $this->log('listou', 'feedbacks_pending');
        
        $feedbacks = $this->feedbackRepository->findByStatus('pending');
        
        echo $this->twig->render('admin/feedbacks/pending.twig', [
            'feedbacks' => $feedbacks
        ]);
    }
    
    public function feedbackProcess($id)
    {
        $this->checkAdminAccess();
        
        $feedback = $this->feedbackRepository->findById($id);
        
        if (!$feedback) {
            $_SESSION['flash_message'] = ['type' => 'error', 'message' => 'Feedback não encontrado.'];
            header('Location: /admin/feedbacks');
            exit;
        }
        
        $status = $_POST['status'] ?? null;
        
        if (!in_array($status, ['resolved', 'rejected'])) {
            $_SESSION['flash_message'] = ['type' => 'error', 'message' => 'Status inválido.'];
            header('Location: /admin/feedbacks');
            exit;
        }
        
        $feedback->setStatus($status);
        $this->feedbackRepository->save($feedback);
        $this->log('processou', 'feedback', $id, "Status alterado para: $status");
        
        $_SESSION['flash_message'] = ['type' => 'success', 'message' => 'Feedback processado com sucesso.'];
        header('Location: /admin/feedbacks');
        exit;
    }
    

    // Eventos: listagem
    public function eventsList()
    {
        $this->checkAdminAccess();
        $this->log('listou', 'events');
        $events = $this->eventRepository->findAll();
        echo $this->twig->render('admin/events/index.twig', ['events' => $events]);
    }

    // Eventos: criação
    public function eventCreate()
    {
        $this->checkAdminAccess();
        $this->log('acessou_criação', 'event');
        echo $this->twig->render('admin/events/create.twig');
    }


    // Eventos: exibir formulário de edição
    public function eventEdit(int $id)
    {
        $this->checkAdminAccess();
        $this->log('acessou_edição', 'event', $id);
        $event = $this->eventRepository->findById($id);
        if (!$event) {
            $_SESSION['flash_message'] = ['type'=>'error','message'=>'Evento não encontrado'];
            header('Location:/admin/events');
            exit;
        }
        echo $this->twig->render('admin/events/create.twig', [
            'event' => [
                'id'          => $event->getId(),
                'title'       => $event->getTitle(),
                'description' => $event->getDescription(),
                'dateTime'    => $event->getDateTime(),
                'visibility'  => $event->getVisibility(),
                'image'       => $event->getImage(),
            ]
        ]);
    }

    // Eventos: atualizar
    public function eventUpdate(int $id)
    {
        $this->checkAdminAccess();
        $title       = $_POST['title'] ?? '';
        $description = $_POST['description'] ?? '';
        $dateTime    = $_POST['date_time'] ?? '';
        $visibility  = $_POST['visibility'] ?? 'public';
        if (!$title || !$description || !$dateTime) {
            echo $this->twig->render('admin/events/create.twig', [
                'error' => 'Todos os campos são obrigatórios.',
                'event' => compact('id','title','description','dateTime','visibility'),
            ]);
            return;
        }
        // Tratar imagem
        $imageUrl = null;
        if (isset($_FILES['image']) && $_FILES['image']['error']===UPLOAD_ERR_OK) {
            $uploadDir = __DIR__ . '/../../public/uploads/events/';
            if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);
            $fileName = time() . '_' . basename($_FILES['image']['name']);
            $filePath = $uploadDir . $fileName;
            if (move_uploaded_file($_FILES['image']['tmp_name'], $filePath)) {
                $imageUrl = '/uploads/events/' . $fileName;
            }
        }
        if ($imageUrl === null) {
            $existing = $this->eventRepository->findById($id);
            $imageUrl = $existing ? $existing->getImage() : null;
        }
        $event = new \App\Models\Event(
            $title, $description, $dateTime, $visibility, $imageUrl, $id
        );
        $this->eventRepository->save($event);
        $this->log('atualizou', 'event', $id, "Título: $title");
        $_SESSION['flash_message'] = ['type'=>'success','message'=>'Evento atualizado com sucesso.'];
        header('Location:/admin/events'); exit;
    }

    // Eventos: excluir
    public function eventDelete(int $id)
    {
        $this->checkAdminAccess();
        $this->log('removeu', 'event', $id);
        $this->eventRepository->delete($id);
        $_SESSION['flash_message'] = ['type'=>'success','message'=>'Evento excluído com sucesso.'];
        header('Location:/admin/events'); exit;
    }

    public function eventStore()
    {
        $this->checkAdminAccess();
        
        $title = $_POST['title'] ?? '';
        $description = $_POST['description'] ?? '';
        $dateTime = $_POST['date_time'] ?? '';
        $visibility = $_POST['visibility'] ?? 'public';
        
        if (!$title || !$description || !$dateTime) {
            echo $this->twig->render('admin/events/create.twig', [
                'error' => 'Todos os campos são obrigatórios.',
                'event' => [
                    'title' => $title,
                    'description' => $description,
                    'dateTime' => $dateTime,
                    'visibility' => $visibility
                ]
            ]);
            return;
        }
        
        $imageUrl = null;
        
        // Upload de imagem
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $uploadDir = __DIR__ . '/../../public/uploads/events/';
            
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }
            
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
            $imageUrl
        );
        
        $this->eventRepository->save($event);
        $this->log('criou', 'event', null, "Título: $title");
        
        $_SESSION['flash_message'] = ['type' => 'success', 'message' => 'Evento criado com sucesso.'];
        header('Location: /admin/events');
        exit;
    }
        /**
     * Exibe o formulário de criação de um novo usuário
     */
    public function userCreate()
    {
        $this->checkAdminAccess();
        // renderiza src/Views/admin/users/create.twig
        echo $this->twig->render('admin/users/create.twig');
    }

    /**
     * Processa o POST de criação de usuário
     */
    public function userStore()
    {
        $this->checkAdminAccess();

        // lê dados do formulário
        $name     = $_POST['name']     ?? '';
        $email    = $_POST['email']    ?? '';
        $password = $_POST['password'] ?? '';
        $type     = $_POST['type']     ?? null;
        $bio      = $_POST['bio']      ?? null;

        // validações mínimas
        if (!$name || !$email || !$password) {
            echo $this->twig->render('admin/users/create.twig', [
                'error' => 'Nome, email e senha são obrigatórios.',
                'user'  => compact('name','email','type','bio')
            ]);
            return;
        }

        // hash da senha
        $hash = password_hash($password, PASSWORD_DEFAULT);

        // cria modelo de User (você deve ajustar o construtor conforme o seu)
        $user = new \App\Models\User(
            $name,
            $email,
            $hash,
            $type,
            null,      // id (novo)
            null,      // recovery_code
            null,      // recovery_exp
            null,      // avatar
            date('Y-m-d H:i:s'),
            date('Y-m-d H:i:s'),
            $bio
        );

        // salva no banco
        $this->userRepository->save($user);

        // log de auditoria
        $this->log('criou', 'user', $user->getId(), "Nome: $name");

        // flash e redireciona
        $_SESSION['flash_message'] = ['type'=>'success','message'=>'Usuário criado.'];
        header('Location: /admin/users');
        exit;
    }


}