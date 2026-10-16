<?php
declare(strict_types=1);

namespace App;

use Framework\view\TwigRenderer;

class Application
{
    private Container $container;
    private Router $router;

    public function __construct()
    {
        $this->container = new Container();
        $this->router = new Router();

        $this->container->set('view', fn() => new TwigRenderer());
        $this->container->set('router', fn() => $this->router);
    }

    public function container(): Container
    {
        return $this->container;
    }

    public function router(): Router
    {
        return $this->router;
    }

    public function run(): void
    {
        $request = Request::fromGlobals();
        $this->router->resolve($request, $this->container)->send();
    }
}