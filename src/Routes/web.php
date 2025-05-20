<?php

use Controller\pages\HomeController;
use Controller\pages\AuthController;
use Controller\AdminController;
use Controller\pages\MessageController;
use Controller\pages\FeedbackController;
use Services\CloudinaryService;
use Middleware\AdminMiddleware;
use Middleware\AuthMiddleware;
use Middleware\GuestMiddleware;
use Controller\PagesController;
use Controller\pages\PublicCourseController;
use Repositories\CourseRepository;
use Repositories\MaterialRepository;
use Repositories\MaterialEntryRepository;
use Controller\pages\TeacherMaterialController;

// ==========================================
// Rotas públicas (sem autenticação)
// ==========================================

// Home e páginas gerais
$r->addRoute('GET', '/', function($twig) {
    (new HomeController($twig))->index();
});

// ==========================================
// Rotas para visitantes (não autenticados)
// ==========================================

// Login
$r->addRoute('GET', '/login', function($twig) {
    GuestMiddleware::handle();
    (new AuthController($twig))->login();
});
$r->addRoute('POST', '/login', function($twig) {
    GuestMiddleware::handle();
    (new AuthController($twig))->loginPost();
});

// Registro

$r->addRoute('GET', '/register', function($twig) {
    GuestMiddleware::handle();
    (new AuthController($twig))->register();
});

$r->addRoute('POST', '/register', function($twig) {
    GuestMiddleware::handle();
    (new AuthController($twig))->registerPost();
});

// Recuperação de senha (esqueci)
$r->addRoute('GET',  '/forgot-password',    function($twig) {
    GuestMiddleware::handle();
    (new AuthController($twig))->forgot();
});
$r->addRoute('POST', '/forgot-password',    function($twig) {
    GuestMiddleware::handle();
    (new AuthController($twig))->forgotPost();
});

// Redefinir senha
$r->addRoute('GET',  '/reset-password',     function($twig) {
    GuestMiddleware::handle();
    (new AuthController($twig))->reset();
});
$r->addRoute('POST', '/reset-password',     function($twig) {
    GuestMiddleware::handle();
    (new AuthController($twig))->resetPost();
});

// Checagem de e-mail por AJAX
$r->addRoute('POST', '/login/check-email', function($twig) {
    GuestMiddleware::handle();                   // só visitantes podem acessar
    (new \Controller\pages\AuthController($twig))
        ->checkEmail();
});

// Login com Google
$r->addRoute('GET', '/auth/google', function($twig) {
    (new \Controller\pages\AuthController($twig))->googleRedirect();
});

$r->addRoute('GET', '/auth/google/callback', function($twig) {
    (new \Controller\pages\AuthController($twig))->googleCallback();
});


// ==========================================
// Rotas para usuários autenticados
// ==========================================

// Logout
$r->addRoute('GET', '/logout', function($twig) {
    AuthMiddleware::handle();
    (new AuthController($twig))->logout();
});


// Exibe o perfil
$r->addRoute('GET', '/profile', function($twig) {
    AuthMiddleware::handle();
    (new \Controller\pages\ProfileController($twig))->index();
});

// Formulário de edição
$r->addRoute('GET', '/profile/edit', function($twig) {
    AuthMiddleware::handle();
    (new \Controller\pages\ProfileController($twig))->edit();
});
// Processa atualização (form POST)
$r->addRoute('POST', '/profile/update', function($twig) {
    AuthMiddleware::handle();
    (new \Controller\pages\ProfileController($twig))->update();
});

// Mensagens
$r->addRoute('POST', '/messages/store', function($twig) {
    AuthMiddleware::handle();
    (new MessageController($twig))->store();
});
$r->addRoute('GET', '/messages/{id}', function($twig, $_, $id) {
    AuthMiddleware::handle();
    (new MessageController($twig))->view($id);
});
$r->addRoute('GET', '/messages/{id}/delete', function($twig, $_, $id) {
    AuthMiddleware::handle();
    (new MessageController($twig))->delete($id);
});
$r->addRoute('POST', '/conversations/{conversationId}/messages/create', function($twig, $_, $conversationId) {
    AuthMiddleware::handle();
    (new MessageController($twig))->create($conversationId);
});

// Feedback (usuário)
$r->addRoute('GET', '/feedback', function($twig) {
    AuthMiddleware::handle();
    (new FeedbackController($twig))->create();
});
$r->addRoute('POST', '/feedback/store', function($twig) {
    AuthMiddleware::handle();
    (new FeedbackController($twig))->store();
});
$r->addRoute('GET', '/feedback/list', function($twig) {
    AuthMiddleware::handle();
    (new FeedbackController($twig))->index();
});
$r->addRoute('GET', '/feedback/{id}', function($twig, $_, $id) {
    AuthMiddleware::handle();
    (new FeedbackController($twig))->view($id);
});
$r->addRoute('GET', '/feedback/{id}/delete', function($twig, $_, $id) {
    AuthMiddleware::handle();
    (new FeedbackController($twig))->delete($id);
});
$r->addRoute('POST', '/feedback/update-status', function($twig) {
    AuthMiddleware::handle();
    (new FeedbackController($twig))->updateStatus();
});

// ==========================================
// Rotas para administradores
// ==========================================

// Painel Admin
$r->addRoute('GET', '/admin', function($twig) {
    AuthMiddleware::handle();
    AdminMiddleware::handle();
    (new AdminController($twig))->index();
});

// Gerenciamento de usuários
// — primeiro rotas de criação
$r->addRoute('GET','/admin/users/create', function($twig) {
    AuthMiddleware::handle();
    AdminMiddleware::handle();
    (new AdminController($twig))->userCreate();
});

$r->addRoute('POST', '/admin/users/store', function($twig) {
    AuthMiddleware::handle();
    AdminMiddleware::handle();
    (new AdminController($twig))->userStore();
});

// — listagem
$r->addRoute('GET', '/admin/users', function($twig) {
    AuthMiddleware::handle();
    AdminMiddleware::handle();
    (new AdminController($twig))->usersList();
});

// — visualizar
$r->addRoute('GET', '/admin/users/{id}', function($twig, $_, $id) {
    AuthMiddleware::handle();
    AdminMiddleware::handle();
    (new AdminController($twig))->userView($id);
});

// — editar
$r->addRoute('GET', '/admin/users/{id}/edit', function($twig, $_, $id) {
    AuthMiddleware::handle();
    AdminMiddleware::handle();
    (new AdminController($twig))->userEdit($id);
});

// — atualizar
$r->addRoute('POST', '/admin/users/{id}/update', function($twig, $_, $id) {
    AuthMiddleware::handle();
    AdminMiddleware::handle();
    (new AdminController($twig))->userUpdate($id);
});

// Gerenciamento de conversas (admin)
$r->addRoute('GET', '/admin/conversations', function($twig) {
    AuthMiddleware::handle();
    AdminMiddleware::handle();
    (new AdminController($twig))->conversationsList();
});
$r->addRoute('GET', '/admin/conversations/{id}', function($twig, $_, $id) {
    AuthMiddleware::handle();
    AdminMiddleware::handle();
    (new AdminController($twig))->conversationView($id);
});

// Gerenciamento de feedbacks (admin)
$r->addRoute('GET', '/admin/feedbacks', function($twig) {
    AuthMiddleware::handle();
    AdminMiddleware::handle();
    (new AdminController($twig))->feedbacksList();
});
$r->addRoute('GET', '/admin/feedbacks/pending', function($twig) {
    AuthMiddleware::handle();
    AdminMiddleware::handle();
    (new AdminController($twig))->feedbackPending();
});
$r->addRoute('GET', '/admin/feedbacks/{id}/process', function($twig, $_, $id) {
    AuthMiddleware::handle();
    AdminMiddleware::handle();
    (new AdminController($twig))->feedbackProcess($id);
});

// ==========================================
// Rotas de Eventos
// ==========================================

// --- Front‑end público ---
$r->addRoute('GET', '/events', function($twig) {
    (new \Controller\pages\EventsController($twig))->index();
});
$r->addRoute('GET', '/events/{id}', function($twig, $_, $id) {
    (new \Controller\pages\EventsController($twig))->show($id);
});

// --- Painel Administrativo ---
$r->addRoute('GET',  '/admin/events',           function($twig) {
    AuthMiddleware::handle();
    AdminMiddleware::handle();
    (new AdminController($twig))->eventsList();
});
$r->addRoute('GET',  '/admin/events/create',    function($twig) {
    AuthMiddleware::handle();
    AdminMiddleware::handle();
    (new AdminController($twig))->eventCreate();
});
$r->addRoute('POST', '/admin/events/store',     function($twig) {
    AuthMiddleware::handle();
    AdminMiddleware::handle();
    (new AdminController($twig))->eventStore();
});
$r->addRoute('GET',  '/admin/events/{id}/edit', function($twig, $_, $id) {
    AuthMiddleware::handle();
    AdminMiddleware::handle();
    (new AdminController($twig))->eventEdit($id);
});
$r->addRoute('POST', '/admin/events/{id}/update', function($twig, $_, $id) {
    AuthMiddleware::handle();
    AdminMiddleware::handle();
    (new AdminController($twig))->eventUpdate($id);
});
$r->addRoute('POST', '/admin/events/{id}/delete', function($twig, $_, $id) {
    AuthMiddleware::handle();
    AdminMiddleware::handle();
    (new AdminController($twig))->eventDelete($id);
});

// Aqui você usa diretamente o $r (RouteCollector)
$r->addRoute('GET', '/about', [PagesController::class, 'about']);
$r->addRoute('GET', '/terms', [PagesController::class, 'terms']);
$r->addRoute('GET', '/contact', [PagesController::class, 'contact']);
$r->addRoute('GET', '/cookies', [PagesController::class, 'cookies']);
$r->addRoute('GET', '/privacy', [PagesController::class, 'privacy']);

// Cursos do professor
$r->addRoute('GET', '/teacher/courses', function($twig, $pdo) {
    $cloud = new CloudinaryService();
    (new \Controller\pages\TeacherCourseController($twig, $pdo, $cloud))->index();
});
$r->addRoute('GET', '/teacher/courses/create', function($twig, $pdo) {
    $cloud = new CloudinaryService();
    (new \Controller\pages\TeacherCourseController($twig, $pdo, $cloud))->create();
});
$r->addRoute('POST','/teacher/courses', function($twig, $pdo) {
    $cloud = new CloudinaryService();
    (new \Controller\pages\TeacherCourseController($twig, $pdo, $cloud))->store();
});
$r->addRoute('GET', '/teacher/courses/{id}/edit', function($twig, $pdo, $id) {
    $cloud = new CloudinaryService();
    (new \Controller\pages\TeacherCourseController($twig, $pdo, $cloud))->edit((int)$id);
});
$r->addRoute('POST','/teacher/courses/{id}/update', function($twig, $pdo, $id) {
    $cloud = new CloudinaryService();
    (new \Controller\pages\TeacherCourseController($twig, $pdo, $cloud))->update((int)$id);
});
$r->addRoute('POST','/teacher/courses/{id}/delete', function($twig, $pdo, $id) {
    $cloud = new CloudinaryService();
    (new \Controller\pages\TeacherCourseController($twig, $pdo, $cloud))->destroy((int)$id);
});
// Materiais de um curso
$r->addRoute('GET', '/teacher/courses/{courseId}/materials', function($twig, $pdo, $courseId) {
    $cloud = new CloudinaryService();
    $materialRepo = new \Repositories\MaterialRepository($pdo);
    $courseRepo = new \Repositories\CourseRepository($pdo);
    $materialEntryRepo = new \Repositories\MaterialEntryRepository($pdo);
    (new \Controller\pages\TeacherMaterialController($twig, $materialRepo, $courseRepo, $cloud, $materialEntryRepo))
        ->index((int)$courseId);
});

$r->addRoute('GET', '/teacher/courses/{courseId}/materials/create', function($twig, $pdo, $courseId) {
    $cloud = new CloudinaryService();
    $materialRepo = new \Repositories\MaterialRepository($pdo);
    $courseRepo = new \Repositories\CourseRepository($pdo);
    $materialEntryRepo = new \Repositories\MaterialEntryRepository($pdo);
    (new \Controller\pages\TeacherMaterialController($twig, $materialRepo, $courseRepo, $cloud, $materialEntryRepo))
        ->create((int)$courseId);
});

$r->addRoute('POST', '/teacher/courses/{courseId}/materials', function($twig, $pdo, $courseId) {
    $cloud = new CloudinaryService();
    $materialRepo = new \Repositories\MaterialRepository($pdo);
    $courseRepo = new \Repositories\CourseRepository($pdo);
    $materialEntryRepo = new \Repositories\MaterialEntryRepository($pdo);
    (new \Controller\pages\TeacherMaterialController($twig, $materialRepo, $courseRepo, $cloud, $materialEntryRepo))
        ->store((int)$courseId);
});

$r->addRoute('POST', '/teacher/courses/{courseId}/materials/{id}/delete', function($twig, $pdo, $courseId, $id) {
    $cloud = new CloudinaryService();
    $materialRepo = new \Repositories\MaterialRepository($pdo);
    $courseRepo = new \Repositories\CourseRepository($pdo);
    $materialEntryRepo = new \Repositories\MaterialEntryRepository($pdo);
    (new \Controller\pages\TeacherMaterialController($twig, $materialRepo, $courseRepo, $cloud, $materialEntryRepo))
        ->destroy((int)$courseId, (int)$id);
});

// Exibe detalhe de um material específico (professor)
$r->addRoute('GET', '/teacher/courses/{courseId}/materials/{id}', function($twig, $pdo, $courseId, $id) {
    $cloud = new CloudinaryService();
    $materialRepo = new \Repositories\MaterialRepository($pdo);
    $courseRepo = new \Repositories\CourseRepository($pdo);
    $materialEntryRepo = new \Repositories\MaterialEntryRepository($pdo);
    (new \Controller\pages\TeacherMaterialController($twig, $materialRepo, $courseRepo, $cloud, $materialEntryRepo))
        ->show((int)$courseId, (int)$id);
});

// Novas rotas para entradas de material serão implementadas posteriormente com um novo controlador.

$r->addRoute(
    'GET',
    '/teacher/courses/{courseId}/modules/{moduleId}/materials/add',
    function($twig, $pdo, $courseId, $moduleId) {
        $cloud        = new CloudinaryService();
        $materialRepo = new MaterialRepository($pdo);
        $courseRepo   = new CourseRepository($pdo);
        $materialEntryRepo = new MaterialEntryRepository($pdo);
        (new TeacherMaterialController($twig, $materialRepo, $courseRepo, $cloud, $materialEntryRepo))
            ->addToModule((int)$courseId, (int)$moduleId);
    }
);

$r->addRoute(
    'POST',
    '/teacher/courses/{courseId}/modules/{moduleId}/materials/add',
    function($twig, $pdo, $courseId, $moduleId) {
        $cloud        = new CloudinaryService();
        $materialRepo = new MaterialRepository($pdo);
        $courseRepo   = new CourseRepository($pdo);
        $materialEntryRepo = new MaterialEntryRepository($pdo);
        (new TeacherMaterialController($twig, $materialRepo, $courseRepo, $cloud, $materialEntryRepo))
            ->storeToModule((int)$courseId, (int)$moduleId);
    }
);

// Comentários de um curso
$r->addRoute('GET', '/teacher/courses/{id}/comments', function($twig, $pdo, $id) {
    $cloud = new CloudinaryService();
    (new \Controller\pages\TeacherCourseController($twig, $pdo, $cloud))
        ->comments((int)$id);
});

$r->addRoute(
    'POST',
    '/courses/{courseId}/comments/{commentId}/delete',
    [\Controller\pages\PublicCourseController::class, 'deleteComment']
);


// Materiais de um curso continuam após...


// Rotas públicas para cursos e materiais
$r->addRoute('GET', '/courses', [PublicCourseController::class, 'index']);
$r->addRoute('GET', '/courses/{id:\d+}', [PublicCourseController::class, 'show']);
$r->addRoute('POST','/courses/{id:\d+}/comment', [PublicCourseController::class, 'storeComment']);

// Rota para listar todos os materiais de um curso (materials.twig)
$r->addRoute('GET', '/courses/{id:\d+}/materials', [PublicCourseController::class, 'listMaterials']);

// Rota para exibir um material específico (material.twig)
$r->addRoute(
    'GET',
    '/courses/{courseId:\d+}/materials/{materialId:\d+}',
    [PublicCourseController::class, 'showMaterial']
);
// Entrar no curso (torna participativo e libera materiais)
$r->addRoute('POST', '/courses/{id:\d+}/join',  [PublicCourseController::class, 'join']);

// Sair do curso (marca left_at e esconde materiais)
$r->addRoute('POST', '/courses/{id:\d+}/leave', [PublicCourseController::class, 'leave']);

// Marcar curso como concluído
$r->addRoute('POST', '/courses/{id:\d+}/complete', [PublicCourseController::class, 'complete']);
// Desmarcar curso como concluído
$r->addRoute('POST', '/courses/{id:\d+}/uncomplete', [PublicCourseController::class, 'uncomplete']);

// Progresso de material (AJAX)
$r->addRoute('POST', '/user/progress/toggle/{materialId:\d+}', [\Controller\pages\UserProgressController::class, 'toggle']);

// Detalhes do curso do professor
$r->addRoute('GET', '/teacher/courses/{id}', function($twig, $pdo, $id) {
    $cloud = new \Services\CloudinaryService();
    (new \Controller\pages\TeacherCourseController($twig, $pdo, $cloud))->show((int)$id);
});

// Rota para inscrição na newsletter
$r->addRoute('POST', '/newsletter/subscribe', [\Controller\pages\NewsletterController::class, 'subscribe']);