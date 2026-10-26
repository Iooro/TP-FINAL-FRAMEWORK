<?php

use App\Controllers\HomeController;
use App\Controllers\ContactController;

/** @var App\Router $router */

$router->get('/', [HomeController::class, 'index']);
$router->get('/contacts', [ContactController::class, 'contacts']);
$router->post('/contacts', [ContactController::class, 'add']);
$router->get('/contacts/update/{id}', [ContactController::class, 'edit']);
$router->post('/contacts/update/{id}', [ContactController::class, 'update']);
$router->get('/contacts/delete/{id}', [ContactController::class, 'delete']);
$router->get('/creer', [ContactController::class, 'creer']);
$router->post('/ajouter',[ContactController::class, 'ajouter']);