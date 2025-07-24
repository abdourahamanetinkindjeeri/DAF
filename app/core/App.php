<?php

namespace App\Core;

use Symfony\Component\Yaml\Yaml;
use function App\Config\dump_die;

class App
{
    private static array $dependencies = [];
    private static bool $initialized = false;

    public static function init(): void
    {
        if (self::$initialized) return;

        $configPath = __DIR__ . '/../config/services.yaml';

        if (!file_exists($configPath)) {
            throw new \Exception("Fichier services.yml introuvable à $configPath");
        }

        $yaml = Yaml::parseFile($configPath);
        self::$dependencies = [];

        foreach ($yaml as $group => $services) {
            foreach ($services as $key => $service) {
                self::$dependencies[$group][$key] = fn() => self::resolve($service);
            }
        }

        self::$initialized = true;
    }

    public static function getDependency(string $key): mixed
    {
        self::init();

        foreach (self::$dependencies as $group) {
            if (isset($group[$key])) {
                return $group[$key]();
            }
        }

        throw new \Exception("Dependency '{$key}' not found");
    }

    public static function resolve(string $service): mixed
    {
        if (str_contains($service, '::')) {
            [$class, $method] = explode('::', $service);
            return call_user_func([$class, $method]);
        }

        return new $service();
    }
}
