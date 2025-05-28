<?php
namespace Controller\pages;

use Infrastructure\Database\Connection;
use Repositories\EventRepository;
use App\Models\Event;

class EventsController
{
    private $twig;
    private EventRepository $repo;

    public function __construct($twig)
    { 
        $this->twig = $twig;
        $pdo         = Connection::getInstance();
        $this->repo  = new EventRepository($pdo);
    }

    // lista para usuários
    public function index()
    {
        $events = $this->repo->findAll();
        echo $this->twig->render('events/index.twig', compact('events'));
    }

    // form de criação
    public function create()
    {
        echo $this->twig->render('events/form.twig', ['event' => null]);
    }

    // armazena novo evento
    public function store()
    {
        // ler exatamente as mesmas chaves do AdminController
        $title           = $_POST['title']            ?? '';
        $description     = $_POST['description']      ?? '';
        $dateTime        = $_POST['date_time']        ?? '';  // <- unificado
        $visibility      = $_POST['visibility']       ?? 'public';
        $isFeatured      = isset($_POST['is_featured']);
        $featurePriority = (int)($_POST['feature_priority'] ?? 0);

        $event = new Event(
            $title,
            $description,
            $dateTime,
            $visibility,
            null,
            $isFeatured,
            $featurePriority
        );

        $this->repo->save($event);
        header('Location: /admin/events');
        exit;
    }

    // form de edição
    public function edit($id)
    {
        $event = $this->repo->findById($id);
        echo $this->twig->render('events/form.twig', compact('event'));
    }

    // atualiza
    public function update($id)
    {
        $event        = $this->repo->findById($id);
        $title        = $_POST['title']            ?? '';
        $description  = $_POST['description']      ?? '';
        $dateTime     = $_POST['date_time']        ?? '';  // <- unificado
        $visibility   = $_POST['visibility']       ?? 'public';
        $isFeatured   = isset($_POST['is_featured']);
        $featurePriority = (int)($_POST['feature_priority'] ?? 0);

        $event->setTitle($title);
        $event->setDescription($description);
        $event->setDateTime($dateTime);
        $event->setVisibility($visibility);
        $event->setIsFeatured($isFeatured);
        $event->setFeaturePriority($featurePriority);

        $this->repo->save($event);
        header('Location: /admin/events');
        exit;
    }

    // exclui
    public function delete($id)
    {
        $this->repo->delete($id);
        header('Location: /admin/events');
        exit;
    }

    // detalhes público
    public function show($id)
    {
        $event = $this->repo->findById($id);
        echo $this->twig->render('events/show.twig', compact('event'));
    }
}

