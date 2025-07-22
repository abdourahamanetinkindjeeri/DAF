<?php

namespace App\Config;

require_once __DIR__ . '/../../vendor/autoload.php';

// $dotenv = \Dotenv\Dotenv::createImmutable(__DIR__ . '/../../');
// $dotenv->load();

if (file_exists(__DIR__ . '/../../.env')) {
  \Dotenv\Dotenv::createImmutable(__DIR__ . '/../../')->load();
}


define('DSN', $_ENV['DSN'] ?? 'mysql:host=localhost;dbname=maxitsa');
define('USER', $_ENV['DB_USER'] ?? 'root');
define('PASSWORD', $_ENV['DB_PASSWORD'] ?? '');
// define('TWILIO_SID', $_ENV['TWILIO_SID'] ?? '');
// define('TOKEN', $_ENV['TOKEN'] ?? '');
// define('PHONE', $_ENV['PHONE'] ?? '');
define('BASE_URL', $_ENV['BASE_URL'] ?? 'http://localhost:8080/');
// define('MESSAGING_SID', $_ENV['MESSAGING_SID']);
define('PRIVATE_KEY', $_ENV['PRIVATE_KEY']);
define('PUBLIC_KEY', $_ENV['PUBLIC_KEY']);
define('CLOUD_NAME', $_ENV['CLOUD_NAME']);



// dump_die(SID);
