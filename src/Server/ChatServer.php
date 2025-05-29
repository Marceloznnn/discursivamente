<?php

namespace Server;

use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;
use PDO;

class ChatServer implements MessageComponentInterface
{
    protected array $clients;
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->clients = [];
        $this->pdo = $pdo;
    }

    public function onOpen(ConnectionInterface $conn)
    {
        $this->clients[$conn->resourceId] = [
            'conn' => $conn,
            'courseId' => null,
            'userId' => null
        ];
        
        error_log("Nova conexão WebSocket: {$conn->resourceId}");
    }

    public function onMessage(ConnectionInterface $from, $msg)
    {
        try {
            $data = json_decode($msg, true);
            if (!$data) {
                throw new \Exception('Mensagem inválida');
            }

            switch ($data['type']) {
                case 'join':
                    $this->handleJoin($from, $data);
                    break;
                
                case 'message':
                    $this->handleMessage($from, $data);
                    break;
                
                default:
                    throw new \Exception('Tipo de mensagem desconhecido');
            }
        } catch (\Exception $e) {
            error_log("Erro no WebSocket: " . $e->getMessage());
            $from->send(json_encode([
                'type' => 'error',
                'message' => 'Erro ao processar mensagem'
            ]));
        }
    }

    protected function handleJoin(ConnectionInterface $conn, array $data)
    {
        $courseId = (int)$data['courseId'];
        $this->clients[$conn->resourceId]['courseId'] = $courseId;
        
        error_log("Cliente {$conn->resourceId} entrou no curso {$courseId}");
    }

    protected function handleMessage(ConnectionInterface $from, array $data)
    {
        $userId = (int)$data['userId'];
        $courseId = (int)$data['courseId'];
        $message = trim($data['message']);

        if (empty($message)) {
            throw new \Exception('Mensagem vazia');
        }

        // Busca informações do usuário
        $stmt = $this->pdo->prepare("SELECT name, avatar FROM users WHERE id = ?");
        $stmt->execute([$userId]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$user) {
            throw new \Exception('Usuário não encontrado');
        }

        // Salva a mensagem
        $stmt = $this->pdo->prepare(
            "INSERT INTO forum_messages (user_id, course_id, message) 
             VALUES (?, ?, ?)"
        );
        $stmt->execute([$userId, $courseId, $message]);

        // Prepara a mensagem para broadcast
        $messageData = [
            'userId' => $userId,
            'userName' => $user['name'],
            'avatar' => $user['avatar'],
            'message' => $message,
            'timestamp' => date('c')
        ];

        // Envia para todos os usuários do mesmo curso
        foreach ($this->clients as $client) {
            if ($client['courseId'] === $courseId) {
                $client['conn']->send(json_encode($messageData));
            }
        }
    }

    public function onClose(ConnectionInterface $conn)
    {
        $clientInfo = $this->clients[$conn->resourceId] ?? null;
        if ($clientInfo) {
            error_log("Cliente {$conn->resourceId} desconectou (Curso: {$clientInfo['courseId']})");
        }
        
        unset($this->clients[$conn->resourceId]);
    }

    public function onError(ConnectionInterface $conn, \Exception $e)
    {
        error_log("Erro WebSocket: " . $e->getMessage());
        $conn->close();
    }
}
 