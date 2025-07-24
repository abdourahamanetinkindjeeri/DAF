<?php

namespace App\Config;

require_once __DIR__ . '/../../vendor/autoload.php';

// $dotenv = \Dotenv\Dotenv::createImmutable(__DIR__ . '/../../');
// $dotenv->load();

if (file_exists(__DIR__ . '/../../.env')) {
  \Dotenv\Dotenv::createImmutable(__DIR__ . '/../../')->load();
}

// Configuration par défaut pour PostgreSQL
define('DSN', $_ENV['DSN'] ?? 'pgsql:host=localhost;port=55432;dbname=app_daf');
define('USER', $_ENV['DB_USER'] ?? 'appdaf');
define('PASSWORD', $_ENV['DB_PASSWORD'] ?? 'appdaf');
define('BASE_URL', $_ENV['BASE_URL'] ?? 'http://localhost:8000/');
define('PRIVATE_KEY', $_ENV['PRIVATE_KEY'] ?? 'default-private-key');
define('PUBLIC_KEY', $_ENV['PUBLIC_KEY'] ?? 'default-public-key');
define('CLOUD_NAME', $_ENV['CLOUD_NAME'] ?? 'default-cloud-name');



// dump_die(SID);
