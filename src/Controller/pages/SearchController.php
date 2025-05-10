<?php

namespace Controller\pages;

use Infrastructure\Database\Connection;
use Repositories\UserRepository;
use Repositories\ClassroomRepository;

class SearchController
{
    private $twig;
    private $pdo;
    private $userRepo;
    private $classRepo;

    public function __construct($twig)
    {
        $this->twig      = $twig;
        $this->pdo       = Connection::getInstance();
        $this->userRepo  = new UserRepository($this->pdo);
        $this->classRepo = new ClassroomRepository($this->pdo);
    }

    /**
     * Exibe formulário de busca e resultados de usuários e turmas
     */
    public function search()
    {
        // Se não estiver logado, redireciona
        if (!isset($_SESSION['user'])) {
            header('Location: /login');
            exit;
        }

        $query = trim($_GET['q'] ?? '');

        // Inicializa arrays vazios
        $users   = [];
        $classes = [];

        if ($query !== '') {
            // Busca usuários: nome ou email contém termo
            $users = $this->userRepo->findAll();
            $users = array_filter($users, function($u) use ($query) {
                $needle = mb_strtolower($query);
                return mb_strpos(mb_strtolower($u->getName()), $needle) !== false
                    || mb_strpos(mb_strtolower($u->getEmail()), $needle) !== false;
            });

            // Busca turmas: somente abertas e ativas
            $rawClasses = $this->classRepo->findPublicAndActiveWithProfessorName();
            foreach ($rawClasses as $c) {
                if (stripos($c['nome'], $query) !== false) {
                    $classes[] = $c;
                }
            }
        }

        echo $this->twig->render('search/results.twig', [
            'query'   => $query,
            'users'   => $users,
            'classes' => $classes,
        ]);
    }
}