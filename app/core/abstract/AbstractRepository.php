<?php

namespace App\Core\Abstract;

use App\Core\App;

abstract class AbstractRepository
{
  protected string $table;
  protected \PDO $db;

  abstract public  function selectAll();
  abstract public  function insert();
  abstract public  function update();
  abstract public  function delete();
  abstract public  function selectById();
  abstract public  function selectBy(array $filter);

  public function countRow(string $colonne, mixed $value, $table): int
  {
    // Correction : on utilise une égalité simple pour le téléphone
    $sql = "SELECT COUNT(*) FROM {$table} WHERE {$colonne} = :value";
    $stmt = $this->db->prepare($sql);
    $stmt->bindValue(':value', $value);
    $stmt->execute();
    return (int) $stmt->fetchColumn() ?? 0;
  }

  public function getDb(): \PDO
  {
    return $this->db;
  }

  public function __construct()
  {
    $this->db = App::get('App\\Core\\Database')->getConnection();
  }
}
