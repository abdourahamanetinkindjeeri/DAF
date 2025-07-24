<?php

// namespace App\Core;

// use Symfony\Component\Yaml\Yaml;
// use function App\Config\dump_die;
// use App\Core\Container;

// class App
// {
//   private static array $dependencies = [];
//   private static bool $initialized = false;

//   public static function init(): void
//   {
//     if (self::$initialized) return;

//     $configPath = __DIR__ . '/../config/services.yaml';

//     if (!file_exists($configPath)) {
//       throw new \Exception("Fichier services.yml introuvable à $configPath");
//     }

//     $yaml = Yaml::parseFile($configPath);
//     self::$dependencies = [];

//     foreach ($yaml as $group => $services) {
//       foreach ($services as $key => $service) {
//         self::$dependencies[$group][$key] = fn() => self::resolve($service);
//       }
//     }

//     self::$initialized = true;
//   }

//   public static function getDependency(string $key): mixed
//   {
//     self::init();

//     foreach (self::$dependencies as $group) {
//       if (isset($group[$key])) {
//         return $group[$key]();
//       }
//     }

//     throw new \Exception("Dependency '{$key}' not found");
//   }

//   public static function resolve(string $service): mixed
//   {
//     if (str_contains($service, '::')) {
//       [$class, $method] = explode('::', $service);
//       return call_user_func([$class, $method]);
//     }

//     // Utilise le container pour résoudre les dépendances
//     $container = new Container();
//     return $container->resolve($service);
//   }
// }


namespace App\Core;

use Symfony\Component\Yaml\Yaml;
use App\Core\SimpleContainer;

class App
{
  private static array $dependencies = [];
  private static bool $initialized = false;
  private static SimpleContainer $container;

  public static function init(): void
  {
    if (self::$initialized) return;

    // Initialiser le container
    self::$container = new SimpleContainer();

    $configPath = __DIR__ . '/../config/services.yaml';

    if (!file_exists($configPath)) {
      throw new \Exception("Fichier services.yaml introuvable à $configPath");
    }

    $yaml = Yaml::parseFile($configPath);
    self::$dependencies = [];

    foreach ($yaml as $group => $services) {
      foreach ($services as $abstract => $concrete) {
        // Bind dans le container
        self::$container->bind($abstract, $concrete);

        // Enregistrement des dépendances pour App::getDependency()
        self::$dependencies[$group][$abstract] = fn() => self::container()->get($abstract);
      }
    }

    self::$initialized = true;
  }

  public static function getDependency(string $key): mixed
  {
    self::init();

    foreach (self::$dependencies as $group) {
      if (isset($group[$key])) {
        return $group[$key](); // appel du closure
      }
    }

    throw new \Exception("Dépendance '{$key}' introuvable.");
  }

  public static function resolve(string $class): mixed
  {
    self::init();
    return self::container()->get($class);
  }

  public static function container(): SimpleContainer
  {
    return self::$container;
  }
}
