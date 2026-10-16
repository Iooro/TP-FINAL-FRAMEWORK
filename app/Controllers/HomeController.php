<?php

use App\Container;
use App\Request;
use App\Response;

class HomeController
{
    private $view;

    public function __construct(Container $container)
    {
        $this->view = $container->get('view');
    }

    public function index(Request $request, array $params = []): Response
    {
        return Response::html($this->view->render('contact/home.html.twig'));
    }
}