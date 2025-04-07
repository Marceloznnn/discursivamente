<?php
namespace Discursivamente\Routes;

use Discursivamente\Controllers\Auth\LoginController;
use Discursivamente\Controllers\Auth\RegisterController;
use Discursivamente\Controllers\Community\CommunityController;
use Discursivamente\Controllers\Community\ForumController;
use Discursivamente\Controllers\Core\SearchController;
use Discursivamente\Controllers\Profile\ProfileController;
use Discursivamente\Controllers\Admin\AdminController;
use Discursivamente\Middleware\AuthMiddleware;
use Discursivamente\Middleware\AdminMiddleware;

class ApiRouter
{
    private static $routes = [];
    
    public static function get($uri, $controller, $method, $middleware = null)
    {
        self::$routes['GET'][$uri] = [
            'controller' => $controller,
            'method' => $method,
            'middleware' => $middleware
        ];
    }
    
    public static function post($uri, $controller, $method, $middleware = null)
    {
        self::$routes['POST'][$uri] = [
            'controller' => $controller,
            'method' => $method,
            'middleware' => $middleware
        ];
    }
    
    public static function put($uri, $controller, $method, $middleware = null)
    {
        self::$routes['PUT'][$uri] = [
            'controller' => $controller,
            'method' => $method,
            'middleware' => $middleware
        ];
    }
    
    public static function delete($uri, $controller, $method, $middleware = null)
    {
        self::$routes['DELETE'][$uri] = [
            'controller' => $controller,
            'method' => $method,
            'middleware' => $middleware
        ];
    }
    
    public static function dispatch()
    {
        header('Content-Type: application/json');
        
        $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        // Remove o prefixo /api das URIs
        $uri = str_replace('/api', '', $uri);
        $method = $_SERVER['REQUEST_METHOD'];
        
        if (isset(self::$routes[$method][$uri])) {
            $route = self::$routes[$method][$uri];
            
            if ($route['middleware']) {
                $middlewareClass = $route['middleware'];
                $middleware = new $middlewareClass();
                
                $authResult = $middleware->handleApi();
                if (!$authResult['success']) {
                    echo json_encode([
                        'success' => false,
                        'message' => $authResult['message'],
                        'status' => 401
                    ]);
                    exit;
                }
            }
            
            $controller = new $route['controller']();
            $action = $route['method'];
            
            try {
                $result = $controller->$action();
                echo json_encode($result);
            } catch (\Exception $e) {
                echo json_encode([
                    'success' => false,
                    'message' => $e->getMessage(),
                    'status' => 500
                ]);
            }
            
            exit;
        }
        
        // Rota de API não encontrada
        echo json_encode([
            'success' => false,
            'message' => 'Endpoint not found',
            'status' => 404
        ]);
        http_response_code(404);
    }
}

// Rotas de autenticação API
ApiRouter::post('/auth/login', LoginController::class, 'apiLogin');
ApiRouter::post('/auth/register', RegisterController::class, 'apiRegister');
ApiRouter::post('/auth/logout', LoginController::class, 'apiLogout', AuthMiddleware::class);

// Rotas de API para busca
ApiRouter::get('/search', SearchController::class, 'apiSearch');

// Rotas de API para comunidades
ApiRouter::get('/communities', CommunityController::class, 'apiIndex');
ApiRouter::get('/communities/{id}', CommunityController::class, 'apiShow');
ApiRouter::post('/communities', CommunityController::class, 'apiStore', AuthMiddleware::class);
ApiRouter::put('/communities/{id}', CommunityController::class, 'apiUpdate', AuthMiddleware::class);
ApiRouter::delete('/communities/{id}', CommunityController::class, 'apiDestroy', AuthMiddleware::class);

// Rotas de API para fóruns
ApiRouter::get('/forums', ForumController::class, 'apiIndex');
ApiRouter::get('/forums/{id}', ForumController::class, 'apiShow');
ApiRouter::post('/forums', ForumController::class, 'apiStore', AuthMiddleware::class);
ApiRouter::put('/forums/{id}', ForumController::class, 'apiUpdate', AuthMiddleware::class);
ApiRouter::delete('/forums/{id}', ForumController::class, 'apiDestroy', AuthMiddleware::class);
ApiRouter::post('/forums/{id}/comments', ForumController::class, 'apiAddComment', AuthMiddleware::class);
ApiRouter::get('/forums/{id}/comments', ForumController::class, 'apiGetComments');

// Rotas de API para perfis de usuário
ApiRouter::get('/profile', ProfileController::class, 'apiGetProfile', AuthMiddleware::class);
ApiRouter::put('/profile', ProfileController::class, 'apiUpdateProfile', AuthMiddleware::class);
ApiRouter::get('/profile/favorites', ProfileController::class, 'apiGetFavorites', AuthMiddleware::class);
ApiRouter::post('/profile/favorites', ProfileController::class, 'apiAddFavorite', AuthMiddleware::class);
ApiRouter::delete('/profile/favorites/{id}', ProfileController::class, 'apiRemoveFavorite', AuthMiddleware::class);
ApiRouter::get('/profile/saved', ProfileController::class, 'apiGetSaved', AuthMiddleware::class);
ApiRouter::post('/profile/password', ProfileController::class, 'apiUpdatePassword', AuthMiddleware::class);

// Rotas de API para admin
ApiRouter::get('/admin/stats', AdminController::class, 'apiGetStats', AdminMiddleware::class);
ApiRouter::get('/admin/users', AdminController::class, 'apiGetUsers', AdminMiddleware::class);
ApiRouter::put('/admin/users/{id}', AdminController::class, 'apiUpdateUser', AdminMiddleware::class);
ApiRouter::delete('/admin/users/{id}', AdminController::class, 'apiDeleteUser', AdminMiddleware::class);

return ApiRouter::dispatch();
