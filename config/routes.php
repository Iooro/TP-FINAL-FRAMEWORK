<?php

use App\Controllers\HomeController;

/** @var App\Router $router */

$router->get('/', [HomeController::class, 'index']);