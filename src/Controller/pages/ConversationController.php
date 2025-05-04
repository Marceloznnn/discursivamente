<?php

namespace Controller\pages;

use Infrastructure\Database\Connection;
use Repositories\ConversationRepository;
use Repositories\UserRepository;
use App\Models\Conversation;

class ConversationController
{
    private $twig;
    private $conversationRepository;
    private $userRepository;
    
    // Função simples para registro de logs
    private function log($message) {
        $logFile = __DIR__ . '/../../logs/conversation.log';
        $timestamp = date('Y-m-d H:i:s');
        $logMessage = "[$timestamp] $message" . PHP_EOL;
        
        // Cria diretório de logs se não existir
        $logDir = dirname($logFile);
        if (!is_dir($logDir)) {
            mkdir($logDir, 0755, true);
        }
        
        file_put_contents($logFile, $logMessage, FILE_APPEND);
    }

    public function __construct($twig)
    {
        $this->twig = $twig;
        $this->conversationRepository = new ConversationRepository(Connection::getInstance());
        $this->userRepository = new UserRepository(Connection::getInstance());
    }

    public function index()
    {
        $this->log("Página de listagem de conversas acessada");
        
        if (!isset($_SESSION['user'])) {
            header('Location: /login');
            exit;
        }
        
        $conversations = $this->conversationRepository->findAll();
        
        // Filtrar conversas que o usuário participa, a menos que seja admin
        if ($_SESSION['user']['type'] !== 'admin') {
            $userId = (int)$_SESSION['user']['id'];
            $conversations = array_filter($conversations, function($conv) use ($userId) {
                return in_array($userId, $conv->getParticipantIds()) || $conv->getCreatedBy() === $userId;
            });
        }
        
        echo $this->twig->render('conversations/index.twig', [
            'conversations' => $conversations
        ]);
    }
    
    public function view($id)
    {
        $this->log("Visualizando conversa ID: $id");
        
        if (!isset($_SESSION['user'])) {
            header('Location: /login');
            exit;
        }
        
        $conversation = $this->conversationRepository->findById($id);
        
        if (!$conversation) {
            $this->log("Conversa ID: $id não encontrada");
            header('Location: /conversations');
            exit;
        }
        
        // Verificar se o usuário tem permissão para ver esta conversa
        $userId = (int)$_SESSION['user']['id'];
        if ($_SESSION['user']['type'] !== 'admin' && 
            !in_array($userId, $conversation->getParticipantIds()) && 
            $conversation->getCreatedBy() !== $userId) {
            $this->log("Acesso negado: usuário ID: $userId não tem permissão para esta conversa");
            header('Location: /conversations');
            exit;
        }
        
        echo $this->twig->render('conversations/view.twig', [
            'conversation' => $conversation
        ]);
    }
    
    public function create()
    {
        $this->log("Página de criação de conversa acessada");
        
        if (!isset($_SESSION['user'])) {
            header('Location: /login');
            exit;
        }
        
        // Buscar usuários disponíveis para adicionar à conversa
        $users = $this->userRepository->findAll();
        
        echo $this->twig->render('conversations/create.twig', [
            'users' => $users
        ]);
    }
    
    public function store()
    {
        if (!isset($_SESSION['user'])) {
            header('Location: /login');
            exit;
        }
        
        $subject = $_POST['subject'] ?? '';
        $participants = isset($_POST['participants']) ? (array)$_POST['participants'] : [];
        
        $this->log("Tentativa de criação de conversa: '$subject' pelo usuário ID: " . $_SESSION['user']['id']);
        
        if (!$subject) {
            $this->log("Criação de conversa falhou: assunto em branco");
            
            $users = $this->userRepository->findAll();
            
            echo $this->twig->render('conversations/create.twig', [
                'error' => 'Por favor, forneça um assunto para a conversa.',
                'users' => $users
            ]);
            return;
        }
        
        // Adicionar o criador como participante se ainda não estiver na lista
        $creatorId = (int)$_SESSION['user']['id'];
        if (!in_array($creatorId, $participants)) {
            $participants[] = $creatorId;
        }
        
        // Converter IDs para inteiros
        $participants = array_map('intval', $participants);
        
        $conversation = new Conversation(
            $subject,
            $creatorId,
            $participants
        );
        
        $this->conversationRepository->save($conversation);
        $this->log("Conversa criada com sucesso pelo usuário ID: $creatorId");
        
        header('Location: /conversations');
        exit;
    }
    
    public function edit($id)
    {
        $this->log("Página de edição de conversa ID: $id acessada");
        
        if (!isset($_SESSION['user'])) {
            header('Location: /login');
            exit;
        }
        
        $conversation = $this->conversationRepository->findById($id);
        
        if (!$conversation) {
            $this->log("Edição falhou: conversa ID: $id não encontrada");
            header('Location: /conversations');
            exit;
        }
        
        // Verificar se o usuário tem permissão para editar esta conversa
        if ($_SESSION['user']['id'] != $conversation->getCreatedBy() && $_SESSION['user']['type'] != 'admin') {
            $this->log("Edição falhou: usuário ID: " . $_SESSION['user']['id'] . " não tem permissão");
            header('Location: /conversations');
            exit;
        }
        
        // Buscar usuários disponíveis para adicionar à conversa
        $users = $this->userRepository->findAll();
        
        echo $this->twig->render('conversations/edit.twig', [
            'conversation' => $conversation,
            'users' => $users
        ]);
    }
    
    public function update($id)
    {
        if (!isset($_SESSION['user'])) {
            header('Location: /login');
            exit;
        }
        
        $this->log("Tentativa de atualização da conversa ID: $id");
        
        $conversation = $this->conversationRepository->findById($id);
        
        if (!$conversation) {
            $this->log("Atualização falhou: conversa ID: $id não encontrada");
            header('Location: /conversations');
            exit;
        }
        
        // Verificar se o usuário tem permissão para editar esta conversa
        if ($_SESSION['user']['id'] != $conversation->getCreatedBy() && $_SESSION['user']['type'] != 'admin') {
            $this->log("Atualização falhou: usuário ID: " . $_SESSION['user']['id'] . " não tem permissão");
            header('Location: /conversations');
            exit;
        }
        
        $subject = $_POST['subject'] ?? '';
        $participants = isset($_POST['participants']) ? (array)$_POST['participants'] : [];
        
        if (!$subject) {
            $this->log("Atualização falhou: assunto em branco");
            
            $users = $this->userRepository->findAll();
            
            echo $this->twig->render('conversations/edit.twig', [
                'error' => 'Por favor, forneça um assunto para a conversa.',
                'conversation' => $conversation,
                'users' => $users
            ]);
            return;
        }
        
        // Adicionar o criador como participante se ainda não estiver na lista
        $creatorId = $conversation->getCreatedBy();
        if (!in_array($creatorId, $participants)) {
            $participants[] = $creatorId;
        }
        
        // Converter IDs para inteiros
        $participants = array_map('intval', $participants);
        
        $conversation->setSubject($subject);
        $conversation->setParticipantIds($participants);
        
        $this->conversationRepository->save($conversation);
        $this->log("Conversa ID: $id atualizada com sucesso");
        
        header('Location: /conversations');
        exit;
    }
    
    public function delete($id)
    {
        $this->log("Tentativa de exclusão da conversa ID: $id");
        
        if (!isset($_SESSION['user'])) {
            header('Location: /login');
            exit;
        }
        
        $conversation = $this->conversationRepository->findById($id);
        
        if (!$conversation) {
            $this->log("Exclusão falhou: conversa ID: $id não encontrada");
            header('Location: /conversations');
            exit;
        }
        
        // Verificar se o usuário tem permissão para excluir esta conversa
        if ($_SESSION['user']['id'] != $conversation->getCreatedBy() && $_SESSION['user']['type'] != 'admin') {
            $this->log("Exclusão falhou: usuário ID: " . $_SESSION['user']['id'] . " não tem permissão");
            header('Location: /conversations');
            exit;
        }
        
        $this->conversationRepository->delete($id);
        $this->log("Conversa ID: $id excluída com sucesso");
        
        header('Location: /conversations');
        exit;
    }
}