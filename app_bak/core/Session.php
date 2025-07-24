<?php

namespace App\Core;

class Session
{
  private static ?Session $instance = null;

  /**
   * Récupère l'instance unique de la session (Singleton)
   * @return Session L'instance unique de la session
   */
  public static function getInstance(): Session
  {
    if (self::$instance === null) {
      self::$instance = new Session();
    }
    return self::$instance;
  }
  private array $session = [];
  public function getSessionStatus(): bool
  {
    return session_status() === PHP_SESSION_NONE;
  }
  private function startSession(): void
  {
    if ($this->getSessionStatus()) {
      session_start();
    }
  }

  private function __construct()
  {
    $this->startSession();

    $this->session = &$_SESSION;
  }

  /**
   * Définit une valeur dans la session
   * @param string $key La clé de la session
   * @param mixed $data Les données à stocker
   * @return void
   */
  public function set(string $key, $data): void
  {
    $this->session[$key] = $data;
  }

  /**
   * Récupère une valeur de la session
   * @param string $key La clé de la session
   * @return mixed La valeur stockée ou null si la clé n'existe pas
   */
  public function get(string $key)
  {
    return $this->session[$key] ?? null;
  }

  /**
   * Supprime une clé de la session
   * @param string $key La clé à supprimer
   * @return bool True si la clé a été supprimée, false sinon
   */
  public function unset(string $key): bool
  {
    if (isset($this->session[$key])) {
      unset($this->session[$key]);
      return true;
    }
    return false;
  }

  /**
   * Vérifie si une clé existe dans la session
   * @param string $key La clé à vérifier
   * @return bool True si la clé existe, false sinon
   */
  public function isset(string $key): bool
  {
    return isset($this->session[$key]);
  }

  /**
   * Détruit une clé spécifique ou toute la session
   * @param string|null $key La clé à détruire (null pour détruire toute la session)
   * @return bool True si la destruction a réussi, false sinon
   */
  public function destroy(string|null $key = null): bool
  {
    if ($key !== null) {
      return $this->unset($key);
    } else {
      return session_destroy();
    }
  }
}


// namespace App\Core;

// class Session
// {
//     public function __construct()
//     {
//         if (session_status() === PHP_SESSION_NONE) {
//             session_start();
//         }
//         echo "Session instanciée avec succès!<br>";
//     }

//     public function get($key)
//     {
//         return $_SESSION[$key] ?? null;
//     }

//     public function set($key, $value)
//     {
//         $_SESSION[$key] = $value;
//     }
// }