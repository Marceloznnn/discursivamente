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

// Classes (Aluno)
$r->addRoute('GET', '/classes/aluno', function($twig) {
    AuthMiddleware::handle();
    (new ClassroomController($twig))->indexAluno();
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

// Gerenciamento de eventos (admin)
$r->addRoute('GET', '/admin/events', function($twig) {
    AuthMiddleware::handle();
    AdminMiddleware::handle();
    (new AdminController($twig))->eventsList();
});
$r->addRoute('GET', '/admin/events/create', function($twig) {
    AuthMiddleware::handle();
    AdminMiddleware::handle();
    (new AdminController($twig))->eventCreate();
});
$r->addRoute('POST', '/admin/events/store', function($twig) {
    AuthMiddleware::handle();
    AdminMiddleware::handle();
    (new AdminController($twig))->eventStore();
});
$r->addRoute('GET', '/admin/events/{id}/edit', function($twig, $_, $id) {
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
