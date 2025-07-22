<?php

namespace App\Core;

use PDO;

class Database extends Singleton
{
    private PDO $connection;

    protected function __construct()
    {

        try {
            $this->connection = new PDO(DSN, USER, PASSWORD);
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
