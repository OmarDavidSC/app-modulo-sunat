<?php

namespace App\Core;

class Router
{
    private array $routes = [];
    private string $prefix = '';

    /**
     * Attach group
     */
    public function attach(string $prefix, callable $callback)
    {
        $previousPrefix = $this->prefix;
        $this->prefix .= $prefix;
        $callback($this);
        $this->prefix = $previousPrefix;
    }

    /**
     * GET route
     */
    public function get($name, $path, $handler)
    {
        $fullPath = $this->prefix . $path;
        $this->routes['GET'][$fullPath] = [
            'name' => $name,
            'handler' => $handler
        ];
    }

    /**
     * POST route
     */
    public function post($name, $path, $handler)
    {
        $fullPath = $this->prefix . $path;
        $this->routes['POST'][$fullPath] = [
            'name' => $name,
            'handler' => $handler
        ];
    }

    /**
     * PUT route
     */
    public function put($name, $path, $handler)
    {
        $fullPath = $this->prefix . $path;
        $this->routes['PUT'][$fullPath] = [
            'name' => $name,
            'handler' => $handler
        ];
    }

    /**
     * DELETE route
     */
    public function delete($name, $path, $handler)
    {
        $fullPath = $this->prefix . $path;
        $this->routes['DELETE'][$fullPath] = [
            'name' => $name,
            'handler' => $handler
        ];
    }

    /**
     * Execute routes
     */
    public function run()
    {
        $method = $_SERVER['REQUEST_METHOD'];
        $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $basePath = dirname($_SERVER['SCRIPT_NAME']);

        if ($basePath !== '/') {
            $uri = str_replace($basePath, '', $uri);
        }

        $uri = $uri ?: '/';
        $routeFound = null;
        $params = [];

        if (isset($this->routes[$method])) {
            foreach ($this->routes[$method] as $routePath => $route) {
                $pattern = preg_replace('/\{([a-zA-Z0-9_]+)\}/', '(?P<$1>[^\/]+)', $routePath);
                $pattern = '#^' . $pattern . '$#';
                if (preg_match($pattern, $uri, $matches)) {
                    $routeFound = $route;
                    foreach ($matches as $key => $value) {
                        if (!is_numeric($key)) {
                            $params[$key] = $value;
                        }
                    }
                    break;
                }
            }
        }

        if (!$routeFound) {
            http_response_code(404);
            header('Content-Type: application/json');
            echo json_encode([
                'success' => false,
                'message' => 'Esta pagina no esta disponible en este momento.',
                'uri' => $uri
            ]);
            exit;
        }

        $controllerName = $routeFound['handler']['Controller'];
        $actionName = $routeFound['handler']['Action'];

        if (!class_exists($controllerName)) {
            die("No existe el controlador: {$controllerName}");
        }

        $controller = new $controllerName();
        if (!method_exists($controller, $actionName)) {
            die("No existe el método: {$actionName}");
        }

        $request = [
            'get' => $_GET,
            'post' => $_POST,
            'files' => $_FILES,
            'server' => $_SERVER,
            'body' => json_decode(file_get_contents('php://input'), true),
            'attributes' => $params
        ];

        $response = $controller->$actionName($request);

        if (is_array($response)) {
            header('Content-Type: application/json');
            echo json_encode($response, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        } else {
            echo $response;
        }
        exit;
    }
}
