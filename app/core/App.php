<?php

namespace App\Core;

use Symfony\Component\Yaml\Yaml;

class App
{
  private static array $definitions = [];
  private static array $instances = [];
  private static bool $initialized = false;

  public static function init(): void
  {
    if (self::$initialized) return;
    $yamlFile = __DIR__ . '/../config/services.yml';
    self::$definitions = Yaml::parseFile($yamlFile)['services'];
    self::$initialized = true;
  }

  public static function get(string $id): mixed
  {
    self::init();

    if (isset(self::$instances[$id])) {
      return self::$instances[$id];
    }

    if (!isset(self::$definitions[$id])) {
      throw new \Exception("Service $id non défini.");
    }

    $definition = self::$definitions[$id];
    $class = $definition['class'] ?? $id;

    $args = [];
    if (!empty($definition['arguments'])) {
      foreach ($definition['arguments'] as $arg) {
        if (is_string($arg) && str_starts_with($arg, '@')) {
          $argId = substr($arg, 1);
          $args[] = self::get($argId);
        } else {
          $args[] = $arg;
        }
      }
    }

    // Gestion des singletons via getInstance
    if (method_exists($class, 'getInstance')) {
      $instance = $class::getInstance(...$args);
    } else {
      $instance = new $class(...$args);
    }
    self::$instances[$id] = $instance;

    return $instance;
  }
}
