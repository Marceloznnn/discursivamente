<?php
// Caminho: src/Core/Router.php
namespace Discursivamente\Core;

class Router {
    protected $routes = [];
    
    /**
     * Adiciona uma rota.
     * @param string $method Método HTTP (GET, POST, etc.)
     * @param string $uri URI da rota
     * @param array  $action Array contendo [NomeDaClasse, 'metodo']
     * @param array  $params (opcional) Parâmetros padrão para a rota
     */
    public function add($method, $uri, $action, $params = []) {
        $this->routes[] = [
            'method' => $method,
            'uri'    => $uri,
            'action' => $action,
            'params' => $params
        ];
    }
    
    /**
     * Procura e retorna a rota correspondente.
     * Sempre retorna um array com 3 elementos: [Controller, método, parâmetros]
     */
    public function resolve($method, $uri) {
        foreach ($this->routes as $route) {
            if ($route['method'] === $method && $route['uri'] === $uri) {
                $params = $route['params'] ?? [];
                return [$route['action'][0], $route['action'][1], $params];
            }
        }
        return null;
    }
    
    /**
     * Agrupa rotas com um prefixo (exemplo básico).
     */
    public function group($prefix, $callback) {
        // Essa implementação pode ser aprimorada conforme sua necessidade.
        $callback($this);
    }
}
