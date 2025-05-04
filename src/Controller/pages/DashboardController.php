<?php

namespace Controller\pages;

use Repositories\DashboardRepository;
use Infrastructure\Database\Connection;

class DashboardController
{
    private $twig;
    private $dashboardRepository;

    public function __construct($twig)
    {
        $this->twig = $twig;
        $this->dashboardRepository = new DashboardRepository(Connection::getInstance());
    }

    public function professor()
    {
        $userId = $_SESSION['user']['id'];
        $dashboard = $this->dashboardRepository->getProfessorDashboard($userId);
        echo $this->twig->render('dashboard/professor.twig', ['dashboard' => $dashboard]);
    }

    public function aluno()
    {
        $userId = $_SESSION['user']['id'];
        $dashboard = $this->dashboardRepository->getAlunoDashboard($userId);
        echo $this->twig->render('dashboard/aluno.twig', ['dashboard' => $dashboard]);
    }
}