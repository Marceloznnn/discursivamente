<?php
// filepath: c:\xampp\htdocs\Discursivamente2.1\src\Controller\AdminController.php

namespace Controller;

use Infrastructure\Database\Connection;
use Repositories\UserRepository;
use Repositories\ConversationRepository;
use Repositories\MessageRepository;
use Repositories\FeedbackRepository;
use Repositories\EventRepository;
use Repositories\AuditLogRepository;
use App\Models\AuditLog;

class AdminController {
    private $twig;
    private $userRepository;
    private $conversationRepository;
    private $messageRepository;
    private $feedbackRepository;
    private $eventRepository;
    private $auditLogRepository;

    public function __construct($twig)
    {
        $this->twig = $twig;
        $connection = Connection::getInstance();

        $this->userRepository         = new UserRepository($connection);
        $this->conversationRepository = new ConversationRepository($connection);
        $this->messageRepository      = new MessageRepository($connection);
        $this->feedbackRepository     = new FeedbackRepository($connection);
        $this->eventRepository        = new EventRepository($connection);
    }

    /**
     * Registra ação no arquivo de log e, opcionalmente, no banco via AuditLogRepository.
     *
     * @param string $action      Ação realizada (ex: 'visualizou', 'criou', 'atualizou')
     * @param string $resource    Recurso afetado (ex: 'user', 'event')
     * @param int|null $resourceId ID do recurso, se houver
     * @param string|null $meta   Informação extra
     */


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

        $userCount         = count($this->userRepository->findAll());
        $conversationCount = count($this->conversationRepository->findAll());
        $pendingFeedbacks  = count($this->feedbackRepository->findByStatus('pending'));
        $upcomingEvents    = count($this->eventRepository->findAll());

        echo $this->twig->render('admin/index.twig', compact(
            'userCount',
            'conversationCount',
            'pendingFeedbacks',
            'upcomingEvents'
        ));
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
            null, // id
            null, // recovery_code
            null, // recovery_exp
            null, // avatar
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

        // upload de avatar
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

        $messages = $this->messageRepository->findByConversation($id);
        echo $this->twig->render('admin/conversations/view.twig', compact('conversation','messages'));
    }

    // === FEEDBACKS ===
    public function feedbacksList()
    {
        $this->checkAdminAccess();

        $feedbacks = $this->feedbackRepository->findAll();
        echo $this->twig->render('admin/feedbacks/index.twig', ['feedbacks' => $feedbacks]);
    }

    public function feedbackPending()
    {
        $this->checkAdminAccess();

        $feedbacks = $this->feedbackRepository->findByStatus('pending');
        echo $this->twig->render('admin/feedbacks/pending.twig', ['feedbacks' => $feedbacks]);
    }

    public function feedbackProcess(int $id)
    {
        $this->checkAdminAccess();

        $feedback = $this->feedbackRepository->findById($id);
        if (!$feedback) {
            $_SESSION['flash_message'] = ['type'=>'error','message'=>'Feedback não encontrado.'];
            header('Location: /admin/feedbacks');
            exit;
        }

        $status = $_POST['status'] ?? '';
        if (!in_array($status, ['resolved','rejected'])) {
            $_SESSION['flash_message'] = ['type'=>'error','message'=>'Status inválido.'];
            header('Location: /admin/feedbacks');
            exit;
        }

        $feedback->setStatus($status);
        $this->feedbackRepository->save($feedback);
        $_SESSION['flash_message'] = ['type'=>'success','message'=>'Feedback processado com sucesso.'];
        header('Location: /admin/feedbacks');
        exit;
    }

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

        echo $this->twig->render('admin/events/create.twig');
    }

    public function eventStore()
    {
        $this->checkAdminAccess();

        $title       = $_POST['title']       ?? '';
        $description = $_POST['description'] ?? '';
        $dateTime    = $_POST['date_time']   ?? '';
        $visibility  = $_POST['visibility']  ?? 'public';

        if (!$title || !$description || !$dateTime) {
            echo $this->twig->render('admin/events/create.twig', [
                'error' => 'Todos os campos são obrigatórios.',
                'event' => compact('title','description','dateTime','visibility')
            ]);
            return;
        }

        // upload de imagem
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
            $imageUrl
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
            header('Location: /admin/events');
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

    public function eventUpdate(int $id)
    {
        $this->checkAdminAccess();

        $title       = $_POST['title']       ?? '';
        $description = $_POST['description'] ?? '';
        $dateTime    = $_POST['date_time']   ?? '';
        $visibility  = $_POST['visibility']  ?? 'public';

        if (!$title || !$description || !$dateTime) {
            echo $this->twig->render('admin/events/create.twig', [
                'error' => 'Todos os campos são obrigatórios.',
                'event' => compact('id','title','description','dateTime','visibility')
            ]);
            return;
        }

        // upload de imagem
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

        // mantém imagem existente se nenhuma nova for enviada
        if ($imageUrl === null) {
            $existing = $this->eventRepository->findById($id);
            $imageUrl = $existing ? $existing->getImage() : null;
        }

        $event = new \App\Models\Event(
            $title,
            $description,
            $dateTime,
            $visibility,
            $imageUrl,
            $id
        );
        $this->eventRepository->save($event);

        $_SESSION['flash_message'] = ['type'=>'success','message'=>'Evento atualizado com sucesso.'];
        header('Location: /admin/events');
        exit;
    }

    public function eventDelete(int $id)
    {
        $this->checkAdminAccess();

        $this->eventRepository->delete($id);
        $_SESSION['flash_message'] = ['type'=>'success','message'=>'Evento excluído com sucesso.'];
        header('Location: /admin/events');
        exit;
    }
}
