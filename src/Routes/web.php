<?php

use Controller\pages\HomeController;
use Controller\pages\AuthController;
use Controller\AdminController;
use Controller\pages\DashboardController;
use Controller\pages\ClassroomController;
use Controller\pages\ConversationController;
use Controller\pages\MessageController;
use Controller\pages\FeedbackController;
use Middleware\AdminMiddleware;
use Middleware\AuthMiddleware;
use Middleware\GuestMiddleware;
use Controller\PagesController;
use Controller\pages\CourseController;
use Controller\pages\CourseMemberController;

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

// ==========================================
// Rotas para usuários autenticados
// ==========================================

// Logout
$r->addRoute('GET', '/logout', function($twig) {
    AuthMiddleware::handle();
    (new AuthController($twig))->logout();
});

// Dashboard
$r->addRoute('GET', '/dashboard/professor', function($twig) {
    AuthMiddleware::handle();
    (new DashboardController($twig))->professor();
});
$r->addRoute('GET', '/dashboard/aluno', function($twig) {
    AuthMiddleware::handle();
    (new DashboardController($twig))->aluno();
});

// Classes (Professor)
$r->addRoute('GET', '/classes/professor', function($twig) {
    AuthMiddleware::handle();
    (new ClassroomController($twig))->indexProfessor();
});
$r->addRoute('GET', '/classes/professor/create', function($twig) {
    AuthMiddleware::handle();
    (new ClassroomController($twig))->create();
});
$r->addRoute('POST', '/classes/professor/store', function($twig) {
    AuthMiddleware::handle();
    (new ClassroomController($twig))->store();
});

$r->addRoute('GET', '/classes/professor/{id}/edit', function($twig, $_, $id) {
    AuthMiddleware::handle();
    (new ClassroomController($twig))->edit($id);
});

$r->addRoute('POST', '/classes/professor/{id}/update', function($twig, $_, $id) {
    AuthMiddleware::handle();
    (new ClassroomController($twig))->update($id);
});


// Classes (Aluno)
$r->addRoute('GET', '/classes/aluno', function($twig) {
    AuthMiddleware::handle();
    (new ClassroomController($twig))->indexAluno();
});

$r->addRoute('GET', '/classes/aluno/{id}/join', function($twig, $_, $id) {
    AuthMiddleware::handle();
    (new ClassroomController($twig))->join($id);
});

// Formulário de solicitação para aluno
$r->addRoute('GET', '/classes/aluno/{classroomId}/request', function($twig, $_pdo, $classroomId) {
    AuthMiddleware::handle();
    (new ClassroomController($twig))->showRequestForm($classroomId);
});
// Envio da solicitação
$r->addRoute('POST', '/classes/aluno/{classroomId}/request', function($twig, $_pdo, $classroomId) {
    AuthMiddleware::handle();
    (new ClassroomController($twig))->requestEnrollment($classroomId);
});

// Listar solicitações pendentes para professor
$r->addRoute('GET', '/classes/professor/{classroomId}/requests', function($twig, $_pdo, $classroomId) {
    AuthMiddleware::handle();
    (new ClassroomController($twig))->listRequests($classroomId);
});
// Aprovar solicitação
$r->addRoute('POST', '/classes/professor/{classroomId}/requests/{requestId}/approve', function($twig, $_pdo, $classroomId, $requestId) {
    AuthMiddleware::handle();
    (new ClassroomController($twig))->approveRequest($classroomId, $requestId);
});

// Página de resumo de solicitações para o professor
$r->addRoute('GET', '/classes/professor/requests', function($twig, $_pdo) {
    AuthMiddleware::handle();
    (new ClassroomController($twig))->manageRequestsOverview();
});


// Recusar solicitação
$r->addRoute('POST', '/classes/professor/{classroomId}/requests/{requestId}/reject', function($twig, $_pdo, $classroomId, $requestId) {
    AuthMiddleware::handle();
    (new ClassroomController($twig))->rejectRequest($classroomId, $requestId);
});

// lista atividades da turma (professor)
$r->addRoute('GET', '/classes/professor/{classroomId}/assignments', function($twig, $_pdo, $classroomId) {
    AuthMiddleware::handle();
    (new Controller\pages\ClassroomController($twig))->listAssignments((int)$classroomId);
});

// formulário de criação
$r->addRoute('GET', '/classes/professor/{classroomId}/assignments/create', function($twig, $_pdo, $classroomId) {
    AuthMiddleware::handle();
    (new Controller\pages\ClassroomController($twig))->showCreateAssignmentForm((int)$classroomId);
});

// grava nova atividade
$r->addRoute('POST', '/classes/professor/{classroomId}/assignments/store', function($twig, $_pdo, $classroomId) {
    AuthMiddleware::handle();
    (new Controller\pages\ClassroomController($twig))->storeAssignment((int)$classroomId);
});


// Rota para explorar turmas
$r->addRoute('GET', '/classes/explore', function($twig) {
    AuthMiddleware::handle();
    (new ClassroomController($twig))->explore();
});
$r->addRoute('GET', '/classes/aluno/{classroomId}/view', function($twig, $_pdo, $classroomId) {
    AuthMiddleware::handle();
    (new ClassroomController($twig))->view($classroomId);
});


$r->addRoute('GET', '/classes/professor/{classroomId}/assignments/{id}/edit', function($twig, $_pdo, $classroomId, $id) {
    AuthMiddleware::handle();
    (new ClassroomController($twig))->editAssignment((int)$classroomId, (int)$id);
});


$r->addRoute('POST', '/classes/professor/{classroomId}/assignments/{id}/update', function($twig, $_pdo, $classroomId, $id) {
    AuthMiddleware::handle();
    (new ClassroomController($twig))->updateAssignment((int)$classroomId, (int)$id);
});

// Ver detalhes de uma atividade
$r->addRoute(
    'GET',
    '/classes/professor/{classroomId}/assignments/{id}',
    function($twig, $_pdo, $classroomId, $id) {
        AuthMiddleware::handle();
        (new ClassroomController($twig))->showAssignment((int)$classroomId, (int)$id);
    }
);

// Ver detalhes de uma atividade (Aluno)
$r->addRoute('GET', '/classes/aluno/{classroomId}/assignments/{id}', function($twig, $_pdo, $classroomId, $id) {
    AuthMiddleware::handle();
    (new Controller\pages\StudentAssignmentController($twig))->showAssignmentAluno((int)$classroomId, (int)$id);
});

// Submeter resposta para uma atividade (Aluno)
$r->addRoute('POST', '/classes/aluno/{classroomId}/assignments/{id}/submit', function($twig, $_pdo, $classroomId, $id) {
    AuthMiddleware::handle();
    (new Controller\pages\StudentAssignmentController($twig))->submitAssignmentAluno((int)$classroomId, (int)$id);
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


// Conversas (usuário)
$r->addRoute('GET', '/conversations', function($twig) {
    AuthMiddleware::handle();
    (new ConversationController($twig))->index();
});
$r->addRoute('GET', '/conversations/create', function($twig) {
    AuthMiddleware::handle();
    (new ConversationController($twig))->create();
});
$r->addRoute('POST', '/conversations/store', function($twig) {
    AuthMiddleware::handle();
    (new ConversationController($twig))->store();
});
$r->addRoute('GET', '/conversations/{id}', function($twig, $_, $id) {
    AuthMiddleware::handle();
    (new ConversationController($twig))->view($id);
});
$r->addRoute('GET', '/conversations/{id}/edit', function($twig, $_, $id) {
    AuthMiddleware::handle();
    (new ConversationController($twig))->edit($id);
});
$r->addRoute('POST', '/conversations/{id}/update', function($twig, $_, $id) {
    AuthMiddleware::handle();
    (new ConversationController($twig))->update($id);
});
$r->addRoute('GET', '/conversations/{id}/delete', function($twig, $_, $id) {
    AuthMiddleware::handle();
    (new ConversationController($twig))->delete($id);
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
$r->addRoute('GET','/admin/users/create', function($twig, $_, $args = []) {
    AuthMiddleware::handle();
    AdminMiddleware::handle();
    (new AdminController($twig))->userCreate();
});

$r->addRoute('POST', '/admin/users/store', function($twig, $_, $args) {
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

// Rotas de Curso
$r->addRoute('GET', '/curso', function($twig) {
    AuthMiddleware::handle();
    (new CourseController($twig))->index();
});

$r->addRoute('GET', '/curso/create', function($twig) {
    AuthMiddleware::handle();
    (new CourseController($twig))->create();
});

$r->addRoute('POST', '/curso/store', function($twig) {
    AuthMiddleware::handle();
    (new CourseController($twig))->store();
});

$r->addRoute('GET', '/curso/{id}/edit', function($twig, $_, $id) {
    AuthMiddleware::handle();
    (new CourseController($twig))->edit((int)$id);
});

$r->addRoute('POST', '/curso/{id}/update', function($twig, $_, $id) {
    AuthMiddleware::handle();
    (new CourseController($twig))->update((int)$id);
});

$r->addRoute('POST', '/curso/{id}/delete', function($twig, $_, $id) {
    AuthMiddleware::handle();
    (new CourseController($twig))->delete((int)$id);
});

// ————————
// **A rota estática de pesquisa DEVE vir antes** da rota variável abaixo:
// ————————
$r->addRoute('GET', '/curso/search', function($twig) {
    AuthMiddleware::handle();
    (new CourseController($twig))->search();
});

// Rota variável que “pega” qualquer /curso/{id}
/* NÃO mova esta acima da /curso/search */
$r->addRoute('GET', '/curso/{id}', function($twig, $_, $id) {
    AuthMiddleware::handle();
    (new CourseController($twig))->show((int)$id);
});

// Rotas de Membros de Curso (Auth obrigatório)
$r->addRoute('GET', '/curso/{courseId}/membros', function($twig, $_, $courseId) {
    AuthMiddleware::handle();
    (new \Controller\pages\CourseMemberController($twig))->index((int)$courseId);
});

$r->addRoute('GET', '/curso/{courseId}/membros/create', function($twig, $_, $courseId) {
    AuthMiddleware::handle();
    (new \Controller\pages\CourseMemberController($twig))->addForm((int)$courseId);
});

$r->addRoute('POST', '/curso/{courseId}/membros', function($twig, $_, $courseId) {
    AuthMiddleware::handle();
    (new \Controller\pages\CourseMemberController($twig))->store((int)$courseId);
});

$r->addRoute('POST', '/curso/{courseId}/membros/{memberId}/delete', function($twig, $_, $courseId, $memberId) {
    AuthMiddleware::handle();
    (new \Controller\pages\CourseMemberController($twig))->delete((int)$courseId, (int)$memberId);
});
