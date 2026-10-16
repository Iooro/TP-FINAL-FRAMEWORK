<?php
declare(strict_types=1);

require dirname(__DIR__) . '/vendor/autoload.php';

use App\Application;

$app = new Application();
$router = $app->router();

require dirname(__DIR__) . '/config/routes.php';

$app->run();