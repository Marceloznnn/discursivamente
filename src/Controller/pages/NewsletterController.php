<?php
namespace Controller\pages;

use App\Models\NewsletterSubscriber;
use Repositories\NewsletterRepository;
use Infrastructure\Database\Connection;

class NewsletterController
{
    private NewsletterRepository $newsletterRepo;

    public function __construct()
    {
        $pdo = Connection::getInstance();
        $this->newsletterRepo = new NewsletterRepository($pdo);
    }

    public function subscribe(): void
    {
        $email = trim($_POST['email'] ?? '');
        $prefCursos = isset($_POST['pref_cursos']);
        $prefEventos = isset($_POST['pref_eventos']);
        $prefConteudos = isset($_POST['pref_conteudos']);

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->respondJson(['success' => false, 'message' => 'E-mail inválido.']);
            return;
        }
        if ($this->newsletterRepo->exists($email)) {
            $this->respondJson(['success' => false, 'message' => 'Este e-mail já está cadastrado.']);
            return;
        }
        $subscriber = new NewsletterSubscriber($email, $prefCursos, $prefEventos, $prefConteudos);
        $ok = $this->newsletterRepo->add($subscriber);
        if ($ok) {
            $this->respondJson(['success' => true, 'message' => 'Inscrição realizada com sucesso!']);
        } else {
            $this->respondJson(['success' => false, 'message' => 'Erro ao salvar inscrição.']);
        }
    }

    private function respondJson(array $data): void
    {
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }
}
