<?php

namespace App\Repository;

use App\Core\Abstract\AbstractRepository;
use App\Core\Database;
use App\Entity\Citoyen;

use function App\Config\dump_die;

class CitoyenRepository extends AbstractRepository
{

  protected string $table = 'utilisateur';

  public function __construct()
  {
    parent::__construct();
//    $this->db = Database::getInstance()->getConnection();
  }
  public function selectAll(): array
  {

    $stmt = $this->db->prepare("SELECT * FROM {$this->table}");
    $stmt->execute();

    $resultat = [];

    while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
      $resultat[] = Citoyen::toObject($row);
    }


    return $resultat;
  }

  public function findByCni(string $cni): ?Citoyen
  {
    $sql = "SELECT * FROM {$this->table} WHERE cni = :cni LIMIT 1";
    $stmt = $this->db->prepare($sql);
    $stmt->bindParam(':cni', $cni);
    $stmt->execute();
    $data = $stmt->fetch(\PDO::FETCH_ASSOC);

    return $data ? Citoyen::toObject($data) : null;
  }


  public function insert()
  {
    // TODO: Implement insert() method.
  }

  public function update()
  {
    // TODO: Implement update() method.
  }

  public function delete()
  {
    // TODO: Implement delete() method.
  }

  public function selectById()
  {
    // TODO: Implement selectById() method.
  }

  public function selectBy(array $filter)
  {
    // TODO: Implement selectBy() method.
  }
}
