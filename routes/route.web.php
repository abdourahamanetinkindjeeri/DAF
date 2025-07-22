<?php

use App\Controller\CitoyenController;

$routes = [
  '/citoyen/id' => [
    'controller' => CitoyenController::class,
    'method' => 'findByCni'
  ],
  '/citoyens' => [
    'controller' => CitoyenController::class,
    'method' => 'getAllCitoyens'
  ],
  '/citoyen/{cni}' => [
    'controller' => CitoyenController::class,
    'method' => 'findByCni'
  ],
  // '/citoyens' => [
  //   'controller' => CitoyenController::class,
  //   'method' => 'index'
  // ],
];

return $routes;
