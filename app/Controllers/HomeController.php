<?php

namespace App\Controllers;

use App\Container;
use App\Request;
use App\Response;

class HomeController
{
    private \App\view\TwigRenderer $view;

    public function __construct(Container $container)
    {
        $this->view = $container->get('view');
    }

    public function index(): Response
    {
        return Response::html($this->view->render('appContact/home.html.twig'));
    }
}