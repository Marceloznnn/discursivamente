<?php

namespace Server;

use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;
use Repositories\SupportChatRepository;
use PDO;

class SupportServer implements MessageComponentInterface
{
    /** @var array<int, array{conn:ConnectionInterface, chatId:string|null, userId:int|null, isSupport:bool}> */
    protected array $clients = [];

    private PDO $pdo;
    private SupportChatRepository $repository;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
        $this->repository = new SupportChatRepository($pdo);
    }

    public function onOpen(ConnectionInterface $conn)
    {
        $this->clients[$conn->resourceId] = [
            'conn'      => $conn,
            'chatId'    => null,
            'userId'    => null,
            'isSupport' => false
        ];
        error_log("Suporte WS: nova conexão #{$conn->resourceId}");
    }

    public function onMessage(ConnectionInterface $from, $msg)
    {
        try {
            $data = json_decode($msg, true, 512, JSON_THROW_ON_ERROR);

            switch ($data['type'] ?? '') {
                case 'join':
                    $this->handleJoin($from, $data);
                    break;
                case 'message':
                    $this->handleMessage($from, $data);
                    break;
                case 'support-request':
                    $this->handleSupportRequest($from, $data);
                    break;
                default:
                    throw new \Exception('Tipo de mensagem desconhecido');
            }
        } catch (\Throwable $e) {
            error_log("Suporte WS erro: " . $e->getMessage());
            $from->send(json_encode([
                'type'    => 'error',
                'message' => 'Erro ao processar sua requisição'
            ]));
        }
    }

    protected function handleJoin(ConnectionInterface $conn, array $data): void
    {
        $this->clients[$conn->resourceId]['chatId']    = $data['chatId'] ?? null;
        $this->clients[$conn->resourceId]['userId']    = (int)($data['userId'] ?? 0);
        $this->clients[$conn->resourceId]['isSupport'] = (bool)($data['isSupport'] ?? false);

        error_log("Suporte WS: #{$conn->resourceId} entrou no chat '{$data['chatId']}'");
    }

    protected function handleMessage(ConnectionInterface $from, array $data): void
    {
        $info      = $this->clients[$from->resourceId];
        $chatId    = $info['chatId'];
        $userId    = $info['userId'];
        $isSupport = $info['isSupport'];
        $message   = trim($data['message'] ?? '');

        if (!$chatId || $userId <= 0 || $message === '') {
            throw new \Exception('Dados de mensagem inválidos');
        }

        // Salvar no banco
        $this->repository->saveMessage($chatId, $userId, $message);

        // Buscar nome e avatar do usuário
        $stmt = $this->pdo->prepare("SELECT name, avatar FROM users WHERE id = ?");
        $stmt->execute([$userId]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC) ?: ['name' => 'Desconhecido', 'avatar' => null];

        // Montar payload
        $payload = [
            'type'      => 'message',
            'chatId'    => $chatId,
            'userId'    => $userId,
            'userName'  => $user['name'],
            'avatar'    => $user['avatar'],
            'message'   => $message,
            'isSupport' => $isSupport,
            'timestamp' => date('c')
        ];

        // Enviar para todos do mesmo chat
        foreach ($this->clients as $client) {
            if ($client['chatId'] === $chatId) {
                $client['conn']->send(json_encode($payload));
            }
        }
    }

    protected function handleSupportRequest(ConnectionInterface $from, array $data): void
    {
        $chatId   = $data['chatId'] ?? null;
        $userId   = (int)($data['userId'] ?? 0);
        $userName = $data['userName'] ?? 'Usuário';

        if (!$chatId || $userId <= 0) {
            throw new \Exception('Dados inválidos na solicitação de suporte');
        }

        $payload = [
            'type'     => 'support-request',
            'chatId'   => $chatId,
            'userId'   => $userId,
            'userName' => $userName,
            'message'  => "Solicitação de atendimento ao suporte do usuário {$userName}",
            'timestamp'=> date('c')
        ];

        // Enviar a todos que são suporte
        foreach ($this->clients as $client) {
            if ($client['isSupport']) {
                $client['conn']->send(json_encode($payload));
            }
        }

        error_log("Suporte WS: Usuário {$userName} solicitou atendimento.");
    }

    public function onClose(ConnectionInterface $conn)
    {
        unset($this->clients[$conn->resourceId]);
        error_log("Suporte WS: conexão #{$conn->resourceId} fechada");
    }

    public function onError(ConnectionInterface $conn, \Exception $e)
    {
        error_log("Suporte WS erro fatal: " . $e->getMessage());
        $conn->close();
    }
}
