<?php

namespace App\Core;

use ReflectionClass;
use ReflectionException;
use Exception;

class SimpleContainer
{
  private $instances = [];
  private $bindings = [];

  // Lier une interface ou une classe abstraite à une implémentation concrète
  public function bind(string $abstract, string $concrete = null): void
  {
    $this->bindings[$abstract] = $concrete ?: $abstract;
  }

  // Résoudre une instance de classe avec ses dépendances
  public function get(string $className)
  {
    // Vérifier si une instance existe déjà (singleton)
    if (isset($this->instances[$className])) {
      return $this->instances[$className];
    }

    try {
      // Créer une ReflectionClass pour la classe demandée
      $reflectionClass = new ReflectionClass($className);

      // Vérifier si la classe est instanciable
      if (!$reflectionClass->isInstantiable()) {
        throw new Exception("La classe $className n'est pas instanciable");
      }

      // Récupérer le constructeur
      $constructor = $reflectionClass->getConstructor();

      // S'il n'y a pas de constructeur, instancier directement
      if (!$constructor) {
        $instance = $reflectionClass->newInstance();
        $this->instances[$className] = $instance;
        return $instance;
      }

      // Résoudre les paramètres du constructeur
      $dependencies = [];
      foreach ($constructor->getParameters() as $param) {
        $paramType = $param->getType();

        if ($paramType && !$paramType->isBuiltin()) {
          // Résoudre une classe ou une interface
          $dependencyClassName = $paramType->getName();
          if (isset($this->bindings[$dependencyClassName])) {
            $dependencyClassName = $this->bindings[$dependencyClassName];
          }
          $dependencies[] = $this->get($dependencyClassName); // Résolution récursive
        } elseif ($param->isDefaultValueAvailable()) {
          // Utiliser la valeur par défaut si disponible
          $dependencies[] = $param->getDefaultValue();
        } else {
          throw new Exception("Impossible de résoudre le paramètre {$param->getName()} de $className");
        }
      }

      // Instancier la classe avec les dépendances
      $instance = $reflectionClass->newInstanceArgs($dependencies);
      $this->instances[$className] = $instance;
      return $instance;
    } catch (ReflectionException $e) {
      throw new Exception("Erreur de réflexion : " . $e->getMessage());
    }
  }

  // Analyser le constructeur pour inspection (facultatif)
  public function analyzeConstructor(string $className): array
  {
    $result = [];
    try {
      $reflectionClass = new ReflectionClass($className);
      $constructor = $reflectionClass->getConstructor();

      if ($constructor) {
        foreach ($constructor->getParameters() as $param) {
          $paramName = $param->getName();
          $paramType = $param->getType();
          $result[] = [
            'name' => $paramName,
            'type' => $paramType ? $paramType->getName() : 'aucun',
            'optional' => $param->isOptional()
          ];
        }
      }
    } catch (ReflectionException $e) {
      throw new Exception("Erreur lors de l'analyse du constructeur : " . $e->getMessage());
    }
    return $result;
  }
}
