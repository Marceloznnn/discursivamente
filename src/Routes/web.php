<?php

use Controller\pages\HomeController;
use Controller\pages\AuthController;
use Controller\AdminController;
use Controller\pages\DashboardController;
use Controller\pages\ClassroomController;
use Controller\pages\ConversationController;
use Controller\pages\MessageController;
use Controller\pages\FeedbackController;
use Services\CloudinaryService;
use Middleware\AdminMiddleware;
use Middleware\AuthMiddleware;
use Middleware\GuestMiddleware;
use Controller\PagesController;
use Controller\pages\PublicCourseController;

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
    (new \Controller\pages\TeacherMaterialController($twig, $pdo, $cloud))
        ->index((int)$courseId);
});
$r->addRoute('GET', '/teacher/courses/{courseId}/materials/create', function($twig, $pdo, $courseId) {
    $cloud = new CloudinaryService();
    (new \Controller\pages\TeacherMaterialController($twig, $pdo, $cloud))
        ->create((int)$courseId);
});
$r->addRoute('POST','/teacher/courses/{courseId}/materials', function($twig, $pdo, $courseId) {
    $cloud = new CloudinaryService();
    (new \Controller\pages\TeacherMaterialController($twig, $pdo, $cloud))
        ->store((int)$courseId);
});
$r->addRoute('POST','/teacher/courses/{courseId}/materials/{id}/delete', function($twig, $pdo, $courseId, $id) {
    $cloud = new CloudinaryService();
    (new \Controller\pages\TeacherMaterialController($twig, $pdo, $cloud))
        ->destroy((int)$courseId, (int)$id);
});

// ... rotas index, create, store, edit, update já definidas

// Detalhes do curso
$r->addRoute('GET', '/teacher/courses/{id}', function($twig, $pdo, $id) {
    $cloud = new CloudinaryService();
    (new \Controller\pages\TeacherCourseController($twig, $pdo, $cloud))
        ->show((int)$id);
});

// Exclusão (via POST já existente)
// $r->addRoute('POST','/teacher/courses/{id}/delete', ... );

// (Opcional) Confirmação de exclusão via GET
$r->addRoute('GET', '/teacher/courses/{id}/delete', function($twig, $pdo, $id) {
    $cloud = new CloudinaryService();
    (new \Controller\pages\TeacherCourseController($twig, $pdo, $cloud))
        ->destroy((int)$id);
});

// Comentários de um curso
$r->addRoute('GET',    '/teacher/courses/{id}/comments', function($twig,$pdo,$id){
    $cloud = new CloudinaryService();
    (new \Controller\pages\TeacherCourseController($twig,$pdo,$cloud))
        ->comments((int)$id);
});

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