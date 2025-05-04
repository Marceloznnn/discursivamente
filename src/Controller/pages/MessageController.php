<?php

namespace Controller\pages;

use Infrastructure\Database\Connection;
use Repositories\MessageRepository;
use App\Models\Message;

class MessageController
{
    private $twig;
    private $messageRepository;
    
    // Função simples para registro de logs
    private function log($message) {
        $logFile = __DIR__ . '/../../logs/message.log';
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
        $this->messageRepository = new MessageRepository(Connection::getInstance());
    }

    public function index($conversationId)
    {
        $this->log("Listando mensagens da conversa ID: $conversationId");
        
        if (!isset($_SESSION['user'])) {
            header('Location: /login');
            exit;
        }
        
        $messages = $this->messageRepository->findByConversation($conversationId);
        
        echo $this->twig->render('messages/index.twig', [
            'messages' => $messages,
            'conversationId' => $conversationId
        ]);
    }
    
    public function view($id)
    {
        $this->log("Visualizando mensagem ID: $id");
        
        if (!isset($_SESSION['user'])) {
            header('Location: /login');
            exit;
        }
        
        $message = $this->messageRepository->findById($id);
        
        if (!$message) {
            $this->log("Mensagem ID: $id não encontrada");
            header('Location: /conversations');
            exit;
        }
        
        // Marcar como lida
        $this->messageRepository->markAsRead($id);
        $this->log("Mensagem ID: $id marcada como lida");
        
        echo $this->twig->render('messages/view.twig', [
            'message' => $message
        ]);
    }
    
    public function create($conversationId)
    {
        $this->log("Página de criação de mensagem acessada para conversa ID: $conversationId");
        
        if (!isset($_SESSION['user'])) {
            header('Location: /login');
            exit;
        }
        
        echo $this->twig->render('messages/create.twig', [
            'conversationId' => $conversationId
        ]);
    }
    
    public function store()
    {
        if (!isset($_SESSION['user'])) {
            header('Location: /login');
            exit;
        }
        
        $conversationId = $_POST['conversation_id'] ?? null;
        $content = $_POST['content'] ?? '';
        $contentType = $_POST['content_type'] ?? 'text';
        $attachmentUrl = null;
        
        $this->log("Tentativa de envio de mensagem para conversa ID: $conversationId");
        
        if (!$conversationId || !$content) {
            $this->log("Envio de mensagem falhou: dados incompletos");
            echo $this->twig->render('messages/create.twig', [
                'error' => 'Preencha todos os campos obrigatórios.',
                'conversationId' => $conversationId
            ]);
            return;
        }
        
        // Processamento de anexo, se houver
        if (isset($_FILES['attachment']) && $_FILES['attachment']['error'] === UPLOAD_ERR_OK) {
            $uploadDir = __DIR__ . '/../../public/uploads/';
            
            // Garantir que o diretório existe
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }
            
            $fileName = time() . '_' . basename($_FILES['attachment']['name']);
            $filePath = $uploadDir . $fileName;
            
            if (move_uploaded_file($_FILES['attachment']['tmp_name'], $filePath)) {
                $attachmentUrl = '/uploads/' . $fileName;
                $this->log("Arquivo anexado com sucesso: $attachmentUrl");
            } else {
                $this->log("Falha ao anexar arquivo");
            }
        }
        
        $message = new Message(
            (int)$conversationId,
            (int)$_SESSION['user']['id'],
            $content,
            $contentType,
            $attachmentUrl
        );
        
        $this->messageRepository->save($message);
        $this->log("Mensagem criada com sucesso para conversa ID: $conversationId");
        
        header("Location: /conversations/$conversationId");
        exit;
    }
    
    public function delete($id)
    {
        $this->log("Tentativa de exclusão da mensagem ID: $id");
        
        if (!isset($_SESSION['user'])) {
            header('Location: /login');
            exit;
        }
        
        $message = $this->messageRepository->findById($id);
        
        if (!$message) {
            $this->log("Exclusão falhou: mensagem ID: $id não encontrada");
            header('Location: /conversations');
            exit;
        }
        
        // Verificar se o usuário tem permissão para excluir
        if ($message->getSenderId() != $_SESSION['user']['id'] && $_SESSION['user']['type'] != 'admin') {
            $this->log("Exclusão falhou: usuário ID: " . $_SESSION['user']['id'] . " não tem permissão");
            header('Location: /conversations/' . $message->getConversationId());
            exit;
        }
        
        $conversationId = $message->getConversationId();
        $this->messageRepository->delete($id);
        $this->log("Mensagem ID: $id excluída com sucesso");
        
        header("Location: /conversations/$conversationId");
        exit;
    }
}