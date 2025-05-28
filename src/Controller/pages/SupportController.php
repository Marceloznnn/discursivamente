<?php

namespace Controller\pages;

use Middleware\AuthMiddleware;
use PDO;
use Repositories\SupportChatRepository;
use Repositories\UserRepository;
use Twig\Environment;

class SupportController
{
    private Environment $twig;
    private SupportChatRepository $chatRepo;
    private UserRepository $userRepo;

    public function __construct(Environment $twig, PDO $pdo)
    {
        $this->twig     = $twig;
        $this->chatRepo = new SupportChatRepository($pdo);
        $this->userRepo = new UserRepository($pdo);
    }

    /**
     * Página do chat de suporte
     */
    public function index(): void
    {
        AuthMiddleware::handle();

        $currentUser = $_SESSION['user'] ?? null;
        if (!$currentUser) {
            $this->renderNotFound();
            return;
        }

        $userId   = (int)$currentUser['id'];
        $messages = $this->chatRepo->getMessagesByUserId($userId);

        echo $this->twig->render('public/support/chat.twig', [
            'currentUser' => $currentUser,
            'messages'    => $messages
        ]);
    }

    /**
     * Envia nova mensagem e responde automaticamente
     */
    public function post(): void
    {
        AuthMiddleware::handle();

        $currentUser = $_SESSION['user'] ?? null;
        $userId      = (int)($currentUser['id'] ?? 0);
        $message     = trim($_POST['message'] ?? '');

        if ($message === '') {
            $_SESSION['flash']['error'][] = "A mensagem não pode estar vazia.";
            $this->redirectToChat();
            return;
        }

        // Salvar mensagem do usuário
        $this->chatRepo->saveMessage($userId, $message, 'user');

        // Gerar resposta automática
        $autoResponse = $this->getBotResponse($message);
        $this->chatRepo->saveMessage($userId, $autoResponse, 'bot');

        $_SESSION['flash']['success'][] = "Mensagem enviada!";
        $this->redirectToChat();
    }

    /**
     * Solicita atendimento humano
     */
    public function requestHuman(): void
    {
        AuthMiddleware::handle();

        $currentUser = $_SESSION['user'] ?? null;
        $userId      = (int)($currentUser['id'] ?? 0);

        $this->chatRepo->saveMessage($userId, 'Usuário solicitou atendimento humano.', 'system');

        $_SESSION['flash']['success'][] = "Solicitação enviada. Aguarde um atendente.";
        $this->redirectToChat();
    }

    private function getBotResponse(string $message): string
    {
        $message = strtolower($message);

        if (str_contains($message, 'senha')) {
            return 'Para redefinir sua senha, acesse a área de login e clique em "Esqueci minha senha".';
        }

        if (str_contains($message, 'curso') || str_contains($message, 'inscrição')) {
            return 'Você pode ver os cursos disponíveis na página inicial.';
        }

        return 'Não encontrei uma resposta automática para isso. Caso queira, clique no botão abaixo para falar com um atendente.';
    }

    private function renderNotFound(): void
    {
        http_response_code(404); 
        echo $this->twig->render('errors/404.twig');
    }

    private function redirectToChat(): void
    {
        header("Location: /support/chat");
        exit;
    }
}
