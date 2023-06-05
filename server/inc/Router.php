<?php

namespace Inc;

class Router
{
    /**
     * Summary of routes
     * @var array
     */
    private $routes = [];
    /**
     * Summary of addRoute
     * @param mixed $method
     * @param mixed $path
     * @param mixed $controller
     * @param mixed $action
     * @return void
     */
    public function addRoute($method, $path, $controller, $action)
    {
        $this->routes[] = ['method' => $method, 'path' => $path, 'controller' => $controller, 'action' => $action];
    }
    public function route()
    {
        $requestMethod = $_SERVER['REQUEST_METHOD'];
        $baseUrl = $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
        foreach ($this->routes as $route) {
            $pattern = $this->preparePattern($route['path']);
            $matches = [];
            // Check if the request method and URI match the route
            if ($route['method'] === $requestMethod && preg_match($pattern, $baseUrl, $matches)) {
                // Remove the first match (full match)
                array_shift($matches);
                // Get the controller and action names
                $controllerName = $route['controller'];
                $actionName = $route['action'];
                // Create a new instance of the controller
                $controller = new $controllerName();
                if (array_key_exists('id', $matches)) {
                    call_user_func_array([$controller, $actionName], [$matches['id']]);
                } else {
                    call_user_func([$controller, $actionName]);
                }
                return;
            }
        }
        // Handle 404 Not Found
        http_response_code(404);
        echo '404 Not Found';
    }
    private function preparePattern($path)
    {
        // Escape forward slashes
        $pattern = str_replace('/', '\/', $path);
        // Convert placeholders {param} to regex capturing groups
        $pattern = preg_replace('/\{(\w+)\}/', '(?<$1>[^\/]+)', $pattern);
        // Add start and end delimiters and case-insensitive flag
        $pattern = "/^{$pattern}$/i";
        return $pattern;
    }
}
