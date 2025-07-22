<?php

namespace App\Service\implements;

use App\Core\Singleton;
use App\Repository\CitoyenRepository;
use App\Entity\Citoyen;
use App\Service\CitoyenService;

class CitoyenServiceImpl extends Singleton implements CitoyenService
{
  private CitoyenRepository $citoyenRepository;

  public function __construct()
  {
      $this->citoyenRepository = CitoyenRepository::getInstance();  }

  /**
   * Retourne tous les citoyens
   * @return Citoyen[]
   */
  public function getAllCitoyens(): array
  {
    return $this->citoyenRepository->selectAll();
  }

  /**
   * Retourne un citoyen par son CNI
   * @param string $cni
   * @return Citoyen|null
   */
  public function getCitoyenByCni(string $cni): ?Citoyen
  {
    return $this->citoyenRepository->findByCni($cni);
  }

  // Ajoutez ici d'autres méthodes métier si besoin
}
