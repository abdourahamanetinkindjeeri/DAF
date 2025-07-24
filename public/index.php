<?php

use App\Core\App;
use App\Core\Router;

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../app/config/bootstrap.php';

// Initialiser l'application avec SimpleContainer
App::init();

// Résoudre la route
$router = App::resolve(Router::class);
$router->resolve(isset($routes) ? $routes : []);
