<?php

namespace App\Core\Abstract;

use App\Core\App;
use App\Core\Session;

abstract class AbstractController
{
  protected Session $session;
  protected string $layout = 'base';

  public function __construct()
  {
    $this->session = App::getDependency('session');
//    ('App\\Core\\Session');
  }

  protected function renderJSON(array $data, int $code = 200): void
  {
    http_response_code($code);
    header('Content-Type: application/json');
    echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    exit();
  }
}
