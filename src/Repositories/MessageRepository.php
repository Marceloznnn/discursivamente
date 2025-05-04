<?php

namespace Repositories;

use App\Models\Message;
use PDO;

class MessageRepository
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /** Retorna todas as mensagens de uma conversa, em ordem cronológica */
    public function findByConversation(int $conversationId): array
    {
        $stmt = $this->pdo->prepare(
            'SELECT * FROM messages
             WHERE conversation_id = :convId
             ORDER BY created_at ASC'
        );
        $stmt->execute([':convId' => $conversationId]);
        $rows = $stmt->fetchAll();
        return array_map(fn($row) => $this->hydrate($row), $rows);
    }

    /** Busca mensagem por ID */
    public function findById(int $id): ?Message
    {
        $stmt = $this->pdo->prepare(
            'SELECT * FROM messages WHERE id = :id LIMIT 1'
        );
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch();
        return $row ? $this->hydrate($row) : null;
    }

    /** Insere ou atualiza uma mensagem */
    public function save(Message $msg): void
    {
        if ($msg->getId()) {
            $stmt = $this->pdo->prepare(
                'UPDATE messages
                 SET content       = :content,
                     content_type  = :contentType,
                     attachment_url= :attachmentUrl,
                     read_at       = :readAt,
                     updated_at    = NOW()
                 WHERE id = :id'
            );
            $stmt->execute([
                ':content'       => $msg->getContent(),
                ':contentType'   => $msg->getContentType(),
                ':attachmentUrl' => $msg->getAttachmentUrl(),
                ':readAt'        => $msg->getReadAt(),
                ':id'            => $msg->getId(),
            ]);
        } else {
            $stmt = $this->pdo->prepare(
                'INSERT INTO messages
                 (conversation_id, sender_id, content, content_type, attachment_url, created_at)
                 VALUES
                 (:convId, :senderId, :content, :contentType, :attachmentUrl, NOW())'
            );
            $stmt->execute([
                ':convId'       => $msg->getConversationId(),
                ':senderId'     => $msg->getSenderId(),
                ':content'      => $msg->getContent(),
                ':contentType'  => $msg->getContentType(),
                ':attachmentUrl'=> $msg->getAttachmentUrl(),
            ]);
        }
    }

    /** Marca uma mensagem como lida */
    public function markAsRead(int $id): void
    {
        $stmt = $this->pdo->prepare(
            'UPDATE messages SET read_at = NOW() WHERE id = :id'
        );
        $stmt->execute([':id' => $id]);
    }

    /** Remove uma mensagem */
    public function delete(int $id): void
    {
        $stmt = $this->pdo->prepare(
            'DELETE FROM messages WHERE id = :id'
        );
        $stmt->execute([':id' => $id]);
    }

    /** Converte linha de BD num objeto Message */
    private function hydrate(array $row): Message
    {
        return new Message(
            (int)$row['conversation_id'],
            (int)$row['sender_id'],
            $row['content'],
            $row['content_type'],
            $row['attachment_url'] ?? null,
            (int)$row['id'],
            $row['created_at'],
            $row['read_at'] ?? null
        );
    }
}
