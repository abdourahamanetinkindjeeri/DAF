<?php

namespace App\Core;

use App\Config\Abstract\Singleton;
use PDO;
use function App\Config\dump_die;


class Database
{
  private $connection;
  protected static $instance = null;

  public static function getInstance()
  {
    if (self::$instance === null) {
      self::$instance = new self();
    }
    return self::$instance;
  }
  protected function __construct()
  {
    try {
      $this->connection = new PDO(
        DSN,
        USER,
        PASSWORD
      );
      $this->connection->exec("SET NAMES 'UTF8'");
      $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch (\PDOException $e) {
      throw new \Exception("Erreur de connexion : " . $e->getMessage());
    }
  }

  public function getConnection(): PDO
  {
    return $this->connection;
  }
}
//
////$repo = Database::getInstance()->getConnection();


// namespace App\Core;

// class Database
// {
//     private static ?Database $instance = null;

//     private function __construct()
//     {
//         echo "Database instancié avec succès!<br>";
//     }

//     public static function getInstance(): self
//     {
//         if (self::$instance === null) {
//             self::$instance = new self();
//         }
//         return self::$instance;
//     }

//     public function getConnection()
//     {
//         return "Database connection";
//     }
// }