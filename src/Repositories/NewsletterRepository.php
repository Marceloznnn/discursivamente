<?php 
namespace Repositories;

use App\Models\NewsletterSubscriber;
use PDO;

class NewsletterRepository
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function add(NewsletterSubscriber $subscriber): bool
    {
        $stmt = $this->pdo->prepare('
            INSERT INTO newsletter_subscribers 
            (email, pref_cursos, pref_eventos, pref_conteudos, created_at) 
            VALUES (?, ?, ?, ?, ?)
        ');
        return $stmt->execute([
            $subscriber->getEmail(),
            $subscriber->getPrefCursos() ? 1 : 0,
            $subscriber->getPrefEventos() ? 1 : 0,
            $subscriber->getPrefConteudos() ? 1 : 0,
            $subscriber->getCreatedAt()
        ]);
    }

    public function exists(string $email): bool
    {
        $stmt = $this->pdo->prepare('SELECT COUNT(*) FROM newsletter_subscribers WHERE email = ?');
        $stmt->execute([$email]);
        return $stmt->fetchColumn() > 0;
    }

    public function count(): int
    {
        $stmt = $this->pdo->query('SELECT COUNT(*) FROM newsletter_subscribers');
        return (int) $stmt->fetchColumn();
    }

    public function findAll(): array
    {
        $stmt = $this->pdo->query('SELECT * FROM newsletter_subscribers ORDER BY created_at DESC');
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
