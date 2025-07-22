<?php

namespace App\Service;


use App\Entity\Citoyen;

interface CitoyenService
{
  public function getAllCitoyens(): array;
  public function getCitoyenByCni(string $cni): ?Citoyen;
}
