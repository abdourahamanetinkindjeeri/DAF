<?php

namespace App\Core;

use ReflectionClass;
use ReflectionParameter;
use Exception;

class Container
{
  private array $bindings = [
    \App\Service\CitoyenService::class => \App\Service\implements\CitoyenServiceImpl::class,
    // Ajoute ici d'autres mappings si besoin
  ];

  public function resolve(string $class)
  {
    // Mapping interface -> implémentation
    if (isset($this->bindings[$class])) {
      $class = $this->bindings[$class];
    }
    // Vérifie si la classe existe
    if (!class_exists($class)) {
      throw new Exception("Classe $class introuvable");
    }

    $reflection = new ReflectionClass($class);

    // Si la classe n'a pas de constructeur, on l'instancie simplement
    $constructor = $reflection->getConstructor();
    if (is_null($constructor)) {
      return new $class();
    }

    // Résolution des dépendances
    $params = $constructor->getParameters();
    $dependencies = array_map(function (ReflectionParameter $param) {
      $type = $param->getType();

      if (!$type || $type->isBuiltin()) {
        throw new Exception("Impossible de résoudre le paramètre {$param->getName()}");
      }

      // Récursivement résoudre la classe dépendante
      return $this->resolve($type->getName());
    }, $params);

    return $reflection->newInstanceArgs($dependencies);
  }
}
