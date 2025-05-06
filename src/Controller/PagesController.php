<?php
namespace Controller;

class PagesController {
    private $twig;

    public function __construct($twig)
    {
        $this->twig = $twig;
    }

    public function about()
    {
        echo $this->twig->render('pages/about.twig');
    }

    public function terms()
    {
        echo $this->twig->render('pages/terms.twig');
    }

    public function contact()
    {
        echo $this->twig->render('pages/contact.twig');
    }

    public function cookies()
    {
        echo $this->twig->render('pages/cookies.twig');
    }

    public function privacy()
    {
        echo $this->twig->render('pages/privacy.twig');
    }
}
