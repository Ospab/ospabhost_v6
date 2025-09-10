<?php
class Router {
    private $routes = [];

    public function addRoute($path, $controller, $method) {
        $this->routes[$path] = ['controller' => $controller, 'method' => $method];
    }

    public function dispatch($uri) {
        // Убираем параметры запроса
        $path = parse_url($uri, PHP_URL_PATH);
        $path = trim($path, '/');
        
        if ($path === '') {
            $path = 'home';
        }

        // Ищем совпадение маршрута
        if (isset($this->routes[$path])) {
            $route = $this->routes[$path];
            $controllerName = $route['controller'];
            $methodName = $route['method'];
            
            // Подключаем и создаем контроллер
            require_once "../app/controllers/$controllerName.php";
            $controller = new $controllerName();
            
            // Вызываем метод
            if (method_exists($controller, $methodName)) {
                $controller->$methodName();
            } else {
                $this->show404();
            }
        } else {
            $this->show404();
        }
    }

    private function show404() {
        http_response_code(404);
        require_once "../app/views/errors/404.php";
        exit;
    }
}
?>