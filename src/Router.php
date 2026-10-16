<?php

namespace App;

class Router
{
    /** @var array<string, array<string, callable>> */
    private array $routes = [];

    public function add(string $method, string $path, callable|array $handler): void
    {
        $method = strtoupper($method);
        $this->routes[$method][$path] = $handler;
    }

    public function get(string $path, callable|array $handler): void
    {
        $this->add('GET', $path, $handler);
    }

    public function post(string $path, callable|array $handler): void
    {
        $this->add('POST', $path, $handler);
    }



    public function resolve(Request $request, ?Container $container = null): Response
    {
        $method = $request->method();
        $uri    = $request->uri();

        if (!isset($this->routes[$method])) {
            return Response::html('<h1>404 — Page non trouvée</h1>', 404);
        }
        
        foreach ($this->routes[$method] as $pattern => $handler) {
            $regex = preg_replace('#\{(\w+)\}#', '(?P<$1>[^/]+)', $pattern);

            $regex = '#^' . $regex . '$#';

            if (preg_match($regex, $uri, $matches)) {
                $params = array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);

                if (is_array($handler)) {
                    [$controllerClass, $action] = $handler;
                    $controller = new $controllerClass($container);
                    $result = $controller->$action($request, $params);
                } else {
                    $result = $handler($request, $params);
                }

                if ($result instanceof Response) {
                    return $result;
                }
                return Response::html((string) $result);
            }
        }

        foreach ($this->routes as $registeredMethod => $routes) {
            if ($registeredMethod === $method) continue;
            foreach ($routes as $pattern => $_) {
                $regex = preg_replace('#\{(\w+)\}#', '(?P<$1>[^/]+)', $pattern);
                if (preg_match('#^' . $regex . '$#', $uri)) {
                    return Response::html(
                        '<h1>405 — Méthode non autorisée</h1>',
                        405
                    );
                }
            }
        }

        return Response::html('<h1>404 — Page non trouvée</h1>', 404);
    }
}
