<?php
//
//
//namespace App\Core;
//
//use function App\Config\dump_die;
//
//class Router
//{
//  static public function getURI(): ?string
//  {
//    $uri = parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH);
//    return $uri ?: null;
//  }
//
//  static public function findURIToArray(array $routes): bool|array
//  {
//    $uri = static::getURI();
//    foreach ($routes as $route => $params) {
//      if (strpos($route, '{cni}') !== false) {
//        $pattern = str_replace('{cni}', '([^/]+)', $route);
//        if (preg_match('#^' . $pattern . '$#', $uri, $matches)) {
//          $params['cni'] = $matches[1];
//          return $params;
//        }
//      } elseif ($uri === $route) {
//        return $params;
//      }
//    }
//    return false;
//  }
//
//  public static function resolve(array $routes): void
//  {
//    $route = static::findURIToArray($routes);
//    if ($route) {
//      error_log("Route trouvée : " . print_r($route, true));
//
//      if (!empty($route['middleware'])) {
//        foreach ($route['middleware'] as $middlewareClass) {
//
//          $middleware = new $middlewareClass();
//          if (method_exists($middleware, 'handle')) {
//            $middleware->handle();
//          } else {
//            throw new \Exception("Le middleware $middlewareClass doit implémenter la méthode handle.");
//          }
//        }
//      }
//
//      $class = $route['controller'] ?? null;
//      $action = $route['method'] ?? null;
//
//      $controller = new $class();
//      if (!method_exists($controller, $action)) {
//        throw new \Exception("La méthode $action n'existe pas dans le contrôleur $class.");
//      }
//
//      if (isset($route['cni'])) {
//        $controller->$action($route['cni']);
//      } else {
//        $controller->$action();
//      }
//    } else {
//      error_log("Route non trouvée pour : " . static::getURI());
//      die('Route non trouvée');
//    }
//  }
//}


namespace App\Core;

use App\Core\App;

class Router
{
    static public function getURI(): ?string
    {
        $uri = parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH);
        return $uri ?: null;
    }

    static public function findURIToArray(array $routes): bool|array
    {
        $uri = static::getURI();
        return $routes[$uri] ?? false;
    }

    public static function resolve(array $routes): void
    {
        $route = static::findURIToArray($routes);
        if ($route) {
            error_log("Route trouvée : " . print_r($route, true));

            // Instanciation et exécution des middlewares avec injection automatique
            if (!empty($route['middleware'])) {
                foreach ($route['middleware'] as $middlewareClass) {
                    // Instanciation via le container pour injection des dépendances
                    $middleware = App::resolve($middlewareClass);
                    if (method_exists($middleware, 'handle')) {
                        $middleware->handle();
                    } else {
                        throw new \Exception("Le middleware $middlewareClass doit implémenter la méthode handle.");
                    }
                }
            }

            $class = $route['controller'] ?? null;
            $action = $route['method'] ?? null;

            if (!$class || !$action) {
                throw new \Exception("Route mal configurée : contrôleur ou méthode manquant.");
            }

            // Instanciation du contrôleur avec injection automatique
            $controller = App::resolve($class);

            if (!method_exists($controller, $action)) {
                throw new \Exception("La méthode $action n'existe pas dans le contrôleur $class.");
            }

            // Appel de la méthode d'action (tu peux ajouter la gestion des paramètres si besoin)
            $controller->$action();
        } else {
            error_log("Route non trouvée pour : " . static::getURI());
            http_response_code(404);
            echo 'Route non trouvée';
            exit;
        }
    }
}
