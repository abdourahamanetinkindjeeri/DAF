<?php

namespace App\Migration;

require_once __DIR__ . '/schemas.php';
require_once 'vendor/autoload.php';
require_once 'app/config/env.php';

use PDO;
use App\Migration\SQLGenerator;

use function App\Config\dump_die;

function prompt(string $label, bool $hidden = false): string
{
  echo $label;
  // Si on n'est pas dans un terminal interactif, ne pas tenter de cacher le mot de passe
  if ($hidden && function_exists('posix_isatty') && !posix_isatty(STDIN)) {
    $hidden = false;
  }
  if ($hidden) {
    if (strncasecmp(PHP_OS, 'WIN', 3) === 0) {
      $vbscript = sys_get_temp_dir() . '/prompt_password.vbs';
      file_put_contents($vbscript, 'wscript.echo(InputBox("' . addslashes($label) . '", "", ""))');
      $command = "cscript //nologo " . escapeshellarg($vbscript);
      $password = rtrim(shell_exec($command));
      unlink($vbscript);
    } else {
      system('stty -echo');
      $password = rtrim(fgets(STDIN), "\n");
      system('stty echo');
      echo "\n";
    }
    return $password;
  }
  return rtrim(fgets(STDIN), "\n");
}



function askDatabaseCredentials(): array
{
  $env = [
    'DB_HOST' => getenv('DB_HOST') ?: '',
    'DB_PORT' => getenv('DB_PORT') ?: '',
    'DB_NAME' => getenv('DB_NAME') ?: '',
    'DB_USER' => getenv('DB_USER') ?: '',
    'DB_PASSWORD' => getenv('DB_PASSWORD') ?: '',
  ];

  $host = $env['DB_HOST'] ?: prompt("Hôte de la base de données (localhost): ");
  $host = $host !== '' ? $host : 'localhost';

  $port = $env['DB_PORT'] ?: prompt("Port (3306 pour MySQL, 5432 pour PostgreSQL): ");
  $port = $port !== '' ? $port : '5432';

  $dbName = $env['DB_NAME'] ?: prompt("Nom de la base de données: ");
  $user = $env['DB_USER'] ?: prompt("👤 Utilisateur de la base: ");
  $pass = $env['DB_PASSWORD'];
  if ($pass === '') {
    $pass = prompt("Mot de passe: ", function_exists('posix_isatty') && posix_isatty(STDIN));
  }

  $driver = detectDriver($port);

  return [
    'DB_HOST' => $host,
    'DB_PORT' => $port,
    'DB_NAME' => $dbName,
    'DB_USER' => $user,
    'DB_PASSWORD' => $pass,

    'BASE_URL' => BASE_URL,
    'PUBLIC_KEY' => PUBLIC_KEY,
    'PRIVATE_KEY' => PRIVATE_KEY,
    'CLOUD_NAME' => CLOUD_NAME,
    'DSN' => "$driver:host=$host;port=$port;dbname=$dbName"
  ];
}


function detectDriver(string $port): string
{
  return match ($port) {
    '3306' => 'mysql',
    '5432' => 'pgsql',
    default => 'pgsql',
  };
}

function writeEnvFile(array $config, string $path = __DIR__ . '/../.env'): void
{
  $lines = [];
  foreach ($config as $key => $value) {
    $lines[] = "$key=$value";
  }

  file_put_contents($path, implode(PHP_EOL, $lines) . PHP_EOL);
  echo "Fichier .env généré à : $path\n";
}

// --- PHASE 1 : Récupération des infos
$config = askDatabaseCredentials();
// dump_die($config);

writeEnvFile($config);

$dbName = $config['DB_NAME'];
$user = $config['DB_USER'];
$pass = $config['DB_PASSWORD'];
$host = $config['DB_HOST'];
$port = $config['DB_PORT'];
$driver = detectDriver($port);

// --- PHASE 2 : Connexion au SGBD pour créer la base
if ($driver === 'pgsql') {
  $defaultDbName = 'postgres';
  $dsnDefault = "pgsql:host=$host;dbname=$defaultDbName;port=$port";
} else {
  $defaultDbName = 'mysql';
  $dsnDefault = "mysql:host=$host;dbname=$defaultDbName;port=$port;charset=utf8mb4";
}
try {
  $pdo = new PDO($dsnDefault, $user, $pass);
  $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

  if ($driver === 'pgsql') {
    $stmt = $pdo->query("SELECT 1 FROM pg_database WHERE datname = " . $pdo->quote($dbName));
    if (!$stmt->fetch()) {
      $pdo->exec("CREATE DATABASE \"$dbName\";");
      echo "Base de données \"$dbName\" créée avec succès (PostgreSQL).\n";
    } else {
      echo "ℹLa base \"$dbName\" existe déjà.\n";
    }
  } else {
    $pdo->exec("CREATE DATABASE IF NOT EXISTS `$dbName` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;");
    echo "Base de données \"$dbName\" créée avec succès (MySQL).\n";
  }
} catch (\PDOException $e) {
  echo "Erreur de connexion ($driver) : " . $e->getMessage() . "\n";
  exit(1);
}

// --- PHASE 3 : Connexion à la vraie base
if ($driver === 'pgsql') {
  $dsn = "pgsql:host=$host;dbname=$dbName;port=$port";
} else {
  $dsn = "mysql:host=$host;dbname=$dbName;port=$port;charset=utf8mb4";
}

try {
  $pdo = new PDO($dsn, $user, $pass);
  $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

  // Utiliser le schéma depuis schemas.php
  $schemas = require __DIR__ . '/schemas.php';

  // 1. Générer les requêtes de création de tables (remplit $enumTypes)
  $tableSQLs = [];
  foreach ($schemas as $table => $columns) {
    if (method_exists('App\Migration\SQLGenerator', 'generateCreateTable')) {
      $tableSQLs[$table] = SQLGenerator::generateCreateTable($table, $columns, $driver);
    }
  }

  // 2. Générer les types ENUM (PostgreSQL uniquement)
  if ($driver === 'pgsql') {
    if (method_exists('App\Migration\SQLGenerator', 'generateEnumTypes')) {
      $enumQueries = SQLGenerator::generateEnumTypes();
      foreach ($enumQueries as $enumSQL) {
        $pdo->exec($enumSQL);
      }
    }
  }

  // 3. Créer les tables
  foreach ($tableSQLs as $table => $createTableSQL) {
    echo "Création de la table `$table` :\n$createTableSQL\n";
    $pdo->exec($createTableSQL);
    echo "Table `$table` créée avec succès.\n";
  }

  // Suppression de l'ALTER TABLE transaction pour 'statut' (déjà dans le schéma)

  echo "Toutes les tables ont été créées avec succès.\n";
} catch (\PDOException $e) {
  echo "Erreur PDO : " . $e->getMessage() . "\n";
  exit(1);
}
