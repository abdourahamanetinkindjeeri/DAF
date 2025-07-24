<?php

namespace App\Service\implements;

use App\Repository\CitoyenRepository;
use App\Entity\Citoyen;
use App\Service\CitoyenService;

class CitoyenServiceImpl implements CitoyenService
{
  private CitoyenRepository $citoyenRepository;

  public function __construct(CitoyenRepository $citoyenRepository)
  {
    $this->citoyenRepository = $citoyenRepository;
  }

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
