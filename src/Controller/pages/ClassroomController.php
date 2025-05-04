<?php

namespace Controller\pages;

use Repositories\ClassroomRepository;
use Infrastructure\Database\Connection;

class ClassroomController
{
    private $twig;
    private $repo;

    public function __construct($twig)
    {
        $this->twig = $twig;
        $this->repo = new ClassroomRepository(Connection::getInstance());
    }

    // Professor: listar turmas
    public function indexProfessor()
    {
        $professorId = $_SESSION['user']['id'];
        $turmas = $this->repo->findByProfessor($professorId);
        echo $this->twig->render('classroom/professor/index.twig', ['turmas' => $turmas]);
    }

    // Aluno: listar turmas abertas ou convidado
    public function indexAluno()
    {
        $alunoId = $_SESSION['user']['id'];
        $turmas = $this->repo->findOpenOrInvitedForStudent($alunoId);
        echo $this->twig->render('classroom/aluno/index.twig', ['turmas' => $turmas]);
    }

    // Professor: criar turma (exemplo)
    public function create()
    {
        // Exemplo de renderização de formulário
        echo $this->twig->render('classroom/professor/create.twig');
    }

    // Professor: salvar turma (exemplo)
    public function store()
    {
        // Recebe dados do POST, valida e salva
        // ...
    }

    // Outros métodos: editar, arquivar, deletar, detalhes, etc.
}