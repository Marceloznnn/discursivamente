<?php

namespace Repositories;

use App\Models\Conversation;
use PDO;

class ConversationRepository
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /** Retorna todas as conversas, mais recentes primeiro */
    public function findAll(): array
    {
        $stmt = $this->pdo->query(
            'SELECT * FROM conversations ORDER BY updated_at DESC'
        );
        $rows = $stmt->fetchAll();
        return array_map(fn($row) => $this->hydrate($row), $rows);
    }

    /** Busca conversa por ID */
    public function findById(int $id): ?Conversation
    {
        $stmt = $this->pdo->prepare(
            'SELECT * FROM conversations WHERE id = :id LIMIT 1'
        );
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch();
        return $row ? $this->hydrate($row) : null;
    }

    /** Insere ou atualiza uma conversa */
    public function save(Conversation $conv): void
    {
        $participants = implode(',', $conv->getParticipantIds());

        if ($conv->getId()) {
            $stmt = $this->pdo->prepare(
                'UPDATE conversations
                 SET subject      = :subject,
                     participants = :participants,
                     updated_at   = NOW()
                 WHERE id = :id'
            );
            $stmt->execute([
                ':subject'      => $conv->getSubject(),
                ':participants' => $participants,
                ':id'           => $conv->getId(),
            ]);
        } else {
            $stmt = $this->pdo->prepare(
                'INSERT INTO conversations
                 (subject, created_by, participants, created_at, updated_at)
                 VALUES
                 (:subject, :createdBy, :participants, NOW(), NOW())'
            );
            $stmt->execute([
                ':subject'      => $conv->getSubject(),
                ':createdBy'    => $conv->getCreatedBy(),
                ':participants' => $participants,
            ]);
            // opcional: recuperar ID gerado
            // $convId = (int)$this->pdo->lastInsertId();
        }
    }

    /** Remove uma conversa (e cascata de mensagens) */
    public function delete(int $id): void
    {
        $stmt = $this->pdo->prepare(
            'DELETE FROM conversations WHERE id = :id'
        );
        $stmt->execute([':id' => $id]);
    }

    /** Converte linha de BD num objeto Conversation */
    private function hydrate(array $row): Conversation
    {
        $parts = $row['participants'] !== null
            ? array_map('intval', explode(',', $row['participants']))
            : [];

        return new Conversation(
            $row['subject'],
            (int)$row['created_by'],
            $parts,
            (int)$row['id'],
            $row['created_at'],
            $row['updated_at']
        );
    }
}
