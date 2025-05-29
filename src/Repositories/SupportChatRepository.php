<?php

namespace Repositories;

use PDO;

class SupportChatRepository
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Salva uma mensagem no chat de suporte
     */
    public function saveMessage(string $chatId, int $userId, string $message, string $sender = 'user'): void
    {
        $stmt = $this->pdo->prepare(
            "INSERT INTO support_messages (chat_id, user_id, message, sender, created_at)
             VALUES (:chat_id, :user_id, :message, :sender, NOW())"
        );

        $stmt->execute([
            'chat_id' => $chatId,
            'user_id' => $userId,
            'message' => $message,
            'sender'  => $sender
        ]);
    }

    /**
     * Retorna todas as mensagens de um usuário no chat de suporte
     */
    public function getMessagesByUserId(int $userId): array
    {
        $stmt = $this->pdo->prepare(
            "SELECT id,
                    user_id,
                    message,
                    sender,
                    created_at
             FROM support_messages
             WHERE user_id = :user_id
             ORDER BY created_at ASC"
        );

        $stmt->execute(['user_id' => $userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
