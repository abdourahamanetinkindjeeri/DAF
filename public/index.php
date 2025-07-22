<?php

use App\Core\App;

use function App\Config\dump_die;

require_once __DIR__ . '/../vendor/autoload.php';
require_once '../app/config/bootstrap.php';


\App\Core\Router::resolve(
  isset($routes) ? $routes : []
);
