<?php
//
//namespace App\Controller;
//
//use App\Service\CitoyenService;
//use App\Core\Abstract\AbstractController;
//
//class CitoyenController extends AbstractController
//{
//  private CitoyenService $citoyenService;
//
//  public function __construct()
//  {
//    parent::__construct();
//    $this->citoyenService = new CitoyenService();
//  }
//
//  public function findByCni(string $cni): void
//  {
//    $citoyen = $this->citoyenService->getCitoyenByCni($cni);
//
//    if ($citoyen) {
//      $this->renderJSON([
//        'data' => $citoyen->toArray(),
//        'statut' => 'success',
//        'code' => 200,
//        'message' => "Le numéro de carte d'identité a été retrouvé"
//      ], 200);
//    } else {
//      $this->renderJSON([
//        'data' => null,
//        'statut' => 'error',
//        'code' => 404,
//        'message' => "Le numéro de carte d'identité non retrouvé"
//      ], 404);
//    }
//  }
//
//  public function getAllCitoyens(): void
//  {
//    $citoyens = $this->citoyenService->getAllCitoyens();

//    if ($citoyens) {
//      $this->renderJSON([
//        'data' => array_map(fn($c) => $c->toArray(), $citoyens),
//        'statut' => 'success',
//        'code' => 200,
//        'message' => "Liste des citoyens récupérée avec succès"
//      ], 200);
//    } else {
//      $this->renderJSON([
//        'data' => [],
//        'statut' => 'error',
//        'code' => 404,
//        'message' => "Aucun citoyen trouvé"
//      ], 404);
//    }
//  }
//}


namespace App\Controller;

use App\Core\Abstract\AbstractController;
use App\Service\CitoyenService;

class CitoyenController extends AbstractController
{
  private CitoyenService $citoyenService;

  public function __construct(CitoyenService $citoyenService)
  {
    $this->citoyenService = $citoyenService;
  }



  public function getAllCitoyens(): void
  {
    $citoyens = $this->citoyenService->getAllCitoyens();

    if ($citoyens) {
      $this->renderJSON([
        'data' => array_map(fn($c) => $c->toArray(), $citoyens),
        'statut' => 'success',
        'code' => 200,
        'message' => "Liste des citoyens récupérée avec succès"
      ], 200);
    } else {
      $this->renderJSON([
        'data' => [],
        'statut' => 'error',
        'code' => 404,
        'message' => "Aucun citoyen trouvé"
      ], 404);
    }
  }


  public function index(): void
  {
    $data = $this->citoyenService->getAllCitoyens();
    var_dump($data);
  }
}
