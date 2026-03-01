<?php
namespace App\Core;

/**
 * Router Class
 * Handles URL routing and dispatching
 */
class Router {
    private $routes = [];
    private $notFound;

    /**
     * Add GET route
     */
    public function get($path, $callback) {
        $this->addRoute('GET', $path, $callback);
    }

    /**
     * Add POST route
     */
    public function post($path, $callback) {
        $this->addRoute('POST', $path, $callback);
    }

    /**
     * Add route
     */
    private function addRoute($method, $path, $callback) {
        $this->routes[] = [
            'method' => $method,
            'path' => $path,
            'callback' => $callback
        ];
    }

    /**
     * Set 404 handler
     */
    public function notFound($callback) {
        $this->notFound = $callback;
    }

    /**
     * Dispatch request
     */
    public function dispatch() {
        $requestMethod = $_SERVER['REQUEST_METHOD'];
        $requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        
        // Remove base path if exists
        $scriptName = dirname($_SERVER['SCRIPT_NAME']);
        if ($scriptName !== '/') {
            $requestUri = str_replace($scriptName, '', $requestUri);
        }
        $requestUri = '/' . trim($requestUri, '/');

        foreach ($this->routes as $route) {
            if ($route['method'] === $requestMethod) {
                $pattern = $this->convertToRegex($route['path']);
                
                if (preg_match($pattern, $requestUri, $matches)) {
                    array_shift($matches);
                    return $this->executeCallback($route['callback'], $matches);
                }
            }
        }

        // 404 Not Found
        if ($this->notFound) {
            return call_user_func($this->notFound);
        }
        
        http_response_code(404);
        echo "404 - Page Not Found";
    }

    /**
     * Convert route path to regex pattern
     */
    private function convertToRegex($path) {
        $path = preg_replace('/\{([a-zA-Z0-9_]+)\}/', '([a-zA-Z0-9_-]+)', $path);
        return '#^' . $path . '$#';
    }

    /**
     * Execute callback
     */
    private function executeCallback($callback, $params = []) {
        if (is_callable($callback)) {
            return call_user_func_array($callback, $params);
        }

        if (is_string($callback)) {
            list($controller, $method) = explode('@', $callback);
            $controller = "App\\Controllers\\$controller";
            
            if (class_exists($controller)) {
                $controllerInstance = new $controller();
                if (method_exists($controllerInstance, $method)) {
                    return call_user_func_array([$controllerInstance, $method], $params);
                }
            }
        }

        throw new \Exception("Invalid callback");
    }
}
