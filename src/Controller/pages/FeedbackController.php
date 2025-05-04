<?php

namespace Controller\pages;

use Infrastructure\Database\Connection;
use Repositories\FeedbackRepository;
use App\Models\Feedback;

class FeedbackController
{
    private $twig;
    private $feedbackRepository;
    
    // Função simples para registro de logs
    private function log($message) {
        $logFile = __DIR__ . '/../../logs/feedback.log';
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
        $this->feedbackRepository = new FeedbackRepository(Connection::getInstance());
    }

    public function index()
    {
        $this->log("Página de listagem de feedbacks acessada");
        
        if (!isset($_SESSION['user'])) {
            header('Location: /login');
            exit;
        }
        
        // Verifica se o usuário é administrador
        if ($_SESSION['user']['type'] !== 'admin') {
            $this->log("Acesso negado: usuário não é administrador");
            header('Location: /');
            exit;
        }
        
        $feedbacks = $this->feedbackRepository->findAll();
        
        echo $this->twig->render('feedback/index.twig', [
            'feedbacks' => $feedbacks
        ]);
    }
    
    public function pending()
    {
        $this->log("Página de feedbacks pendentes acessada");
        
        if (!isset($_SESSION['user']) || $_SESSION['user']['type'] !== 'admin') {
            $this->log("Acesso negado: usuário não é administrador");
            header('Location: /');
            exit;
        }
        
        $feedbacks = $this->feedbackRepository->findByStatus('pending');
        
        echo $this->twig->render('feedback/pending.twig', [
            'feedbacks' => $feedbacks
        ]);
    }
    
    public function view($id)
    {
        $this->log("Visualizando feedback ID: $id");
        
        if (!isset($_SESSION['user'])) {
            header('Location: /login');
            exit;
        }
        
        $feedback = $this->feedbackRepository->findById($id);
        
        if (!$feedback) {
            $this->log("Feedback ID: $id não encontrado");
            header('Location: /feedback');
            exit;
        }
        
        // Verifica se o usuário é administrador ou o próprio autor do feedback
        if ($_SESSION['user']['type'] !== 'admin' && $_SESSION['user']['id'] != $feedback->getUserId()) {
            $this->log("Acesso negado: usuário não tem permissão para ver este feedback");
            header('Location: /');
            exit;
        }
        
        echo $this->twig->render('feedback/view.twig', [
            'feedback' => $feedback
        ]);
    }
    
    public function create()
    {
        $this->log("Página de criação de feedback acessada");
        
        if (!isset($_SESSION['user'])) {
            header('Location: /login');
            exit;
        }
        
        echo $this->twig->render('feedback/create.twig');
    }
    
    public function store()
    {
        if (!isset($_SESSION['user'])) {
            header('Location: /login');
            exit;
        }
        
        $message = $_POST['message'] ?? '';
        $rating = isset($_POST['rating']) ? (int)$_POST['rating'] : null;
        
        $this->log("Tentativa de envio de feedback pelo usuário ID: " . $_SESSION['user']['id']);
        
        if (!$message) {
            $this->log("Envio de feedback falhou: mensagem em branco");
            echo $this->twig->render('feedback/create.twig', [
                'error' => 'Por favor, forneça uma mensagem.'
            ]);
            return;
        }
        
        $feedback = new Feedback(
            (int)$_SESSION['user']['id'],
            $message,
            $rating,
            'pending'
        );
        
        $this->feedbackRepository->save($feedback);
        $this->log("Feedback enviado com sucesso pelo usuário ID: " . $_SESSION['user']['id']);
        
        echo $this->twig->render('feedback/success.twig', [
            'message' => 'Seu feedback foi enviado com sucesso. Obrigado pela sua contribuição!'
        ]);
    }
    
    public function updateStatus()
    {
        if (!isset($_SESSION['user']) || $_SESSION['user']['type'] !== 'admin') {
            $this->log("Tentativa não autorizada de alterar status de feedback");
            header('Location: /login');
            exit;
        }
        
        $id = $_POST['id'] ?? null;
        $status = $_POST['status'] ?? null;
        
        $this->log("Tentativa de alterar status do feedback ID: $id para: $status");
        
        if (!$id || !in_array($status, ['pending', 'resolved', 'rejected'])) {
            $this->log("Atualização de status falhou: dados inválidos");
            header('Location: /feedback');
            exit;
        }
        
        $feedback = $this->feedbackRepository->findById($id);
        
        if (!$feedback) {
            $this->log("Atualização de status falhou: feedback ID: $id não encontrado");
            header('Location: /feedback');
            exit;
        }
        
        $feedback->setStatus($status);
        $this->feedbackRepository->save($feedback);
        $this->log("Status do feedback ID: $id atualizado para: $status");
        
        header('Location: /feedback');
        exit;
    }
    
    public function delete($id)
    {
        $this->log("Tentativa de exclusão do feedback ID: $id");
        
        if (!isset($_SESSION['user'])) {
            header('Location: /login');
            exit;
        }
        
        $feedback = $this->feedbackRepository->findById($id);
        
        if (!$feedback) {
            $this->log("Exclusão falhou: feedback ID: $id não encontrado");
            header('Location: /feedback');
            exit;
        }
        
        // Verificar se o usuário tem permissão para excluir
        if ($_SESSION['user']['id'] != $feedback->getUserId() && $_SESSION['user']['type'] != 'admin') {
            $this->log("Exclusão falhou: usuário ID: " . $_SESSION['user']['id'] . " não tem permissão");
            header('Location: /feedback');
            exit;
        }
        
        $this->feedbackRepository->delete($id);
        $this->log("Feedback ID: $id excluído com sucesso");
        
        header('Location: /feedback');
        exit;
    }
}