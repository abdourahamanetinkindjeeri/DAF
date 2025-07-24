<?php

namespace App\Core;

use App\Translate\MessageErreur;

class Validator
{
  private array $errors = [];
  private array $donnees = [];

  // === Méthode principale de validation basée sur des règles ===
  public function valider(array $donnees, array $regles): bool
  {
    $this->errors = [];
    $this->donnees = $donnees;

    foreach ($regles as $champ => $reglesChamp) {
      foreach ($reglesChamp as $cle => $regle) {
        $nomRegle = is_int($cle) ? $regle : $cle;
        $parametre = is_int($cle) ? null : $regle;

        $methode = 'valider' . ucfirst($nomRegle);

        if (method_exists($this, $methode)) {
          $this->$methode($champ, $donnees[$champ] ?? null, $parametre);
        }
      }
    }

    return empty($this->errors);
  }

  public function getErrors(): array
  {
    return $this->errors;
  }

  // === Méthodes dynamiques pour le moteur de validation ===

  /**
   * Valide qu'un champ est requis (non vide)
   * Utilisée dynamiquement par le moteur de validation
   * @param string $field Nom du champ
   * @param mixed $value Valeur du champ
   * @param mixed $parameter Paramètre (non utilisé)
   */
  private function validerRequired(string $field, $value, $parameter): void
  {
    if (empty($value) && $value !== '0') {
      $this->errors[$field][] = str_replace('field', $field, MessageErreur::CHAMP_REQUIS->value);
      //      $this->errors[$field][] = "Le champ $field est requis.";
    }
  }

  /**
   * Valide qu'un champ est une adresse email valide
   * Utilisée dynamiquement par le moteur de validation
   * @param string $field Nom du champ
   * @param mixed $value Valeur du champ
   * @param mixed $parameter Paramètre (non utilisé)
   */
  private function validerEmail(string $field, $value, $parameter): void
  {
    if (!self::isEmail($value)) {
      $this->errors[$field][] = str_replace('field', $field, MessageErreur::EMAIL_INVALIDE->value);
      //      $this->errors[$field][] = "Le champ $field doit être une adresse email valide.";
    }
  }

  /**
   * Valide qu'un champ est un numéro de téléphone valide au Sénégal
   * Utilisée dynamiquement par le moteur de validation
   * @param string $field Nom du champ
   * @param mixed $value Valeur du champ
   * @param mixed $parameter Paramètre (non utilisé)
   */
  private function validerPhone(string $field, $value, $parameter): void
  {
    if (!self::isValidNumber($value)) {
      $this->errors[$field][] = str_replace(':field', $field, MessageErreur::NUMBER_INVALID->value);
      //      $this->errors[$field][] = "Le champ $field doit être un numéro de téléphone valide au Sénégal.";
    }
  }

  /**
   * Valide qu'un champ est une CNI valide au Sénégal
   * Utilisée dynamiquement par le moteur de validation
   * @param string $field Nom du champ
   * @param mixed $value Valeur du champ
   * @param mixed $parameter Paramètre (non utilisé)
   */
  private function validerCni(string $field, $value, $parameter): void
  {
    if (!self::isValidCNISenegal($value)) {
      $this->errors[$field][] = str_replace(':field', $field, MessageErreur::CNI_INVALIDE->value);
      //        $this->errors[$field][] = "Le champ $field doit être une CNI valide au Sénégal.";
    }
  }

  /**
   * Valide qu'un champ est unique en utilisant un callback
   * Utilisée dynamiquement par le moteur de validation
   * @param string $field Nom du champ
   * @param mixed $value Valeur du champ
   * @param callable $callback Fonction de vérification d'unicité
   */
  private function validerUnique(string $field, $value, callable $callback): void
  {
    if (!call_user_func($callback, $value)) {
      $this->errors[$field][] = "Le champ $field est déjà utilisé.";
    }
  }

  /**
   * Valide qu'un fichier uploadé est requis et valide
   * Utilisée dynamiquement par le moteur de validation
   * @param string $field Nom du champ
   * @param mixed $value Fichier uploadé
   * @param mixed $parameter Paramètre (non utilisé)
   */
  private function validerFile(string $field, $value, $parameter): void
  {
    // Vérifier si le fichier est requis et s'il a été uploadé
    if (empty($value) || !isset($value['tmp_name']) || empty($value['tmp_name']) || $value['error'] === UPLOAD_ERR_NO_FILE) {
      $this->errors[$field][] = "Le champ $field est requis.";
      return;
    }

    // Vérifier s'il y a une erreur d'upload
    if ($value['error'] !== UPLOAD_ERR_OK) {
      $this->errors[$field][] = "Erreur lors de l'upload du fichier $field.";
      return;
    }

    // Vérifier que le fichier est bien une image
    $allowedTypes = ['image/jpeg', 'image/jpg', 'image/png'];
    if (!in_array($value['type'], $allowedTypes)) {
      $this->errors[$field][] = "Le fichier $field doit être une image (JPG, PNG).";
      return;
    }

    // Vérifier la taille du fichier (max 5MB)
    if ($value['size'] > 5 * 1024 * 1024) {
      $this->errors[$field][] = "Le fichier $field ne doit pas dépasser 5MB.";
      return;
    }
  }

  // === Méthodes statiques réutilisables (utilisables aussi en dehors) ===

  public static function isEmpty(string $field): bool
  {
    return empty(trim($field));
  }

  public static function isEmail(string $email): bool
  {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
  }

  public static function isValidNumber(string $number): bool
  {
    $number = str_replace([' ', '-'], '', $number);
    return (bool)preg_match('/^(\+221|221)?7[05678][0-9]{7}$/', $number);
  }

  public static function isValidCNISenegal(string $cni): bool
  {
    $cni = strtoupper(str_replace(' ', '', $cni));
    return (bool)preg_match('/^(\d{13}|\d{4}[A-Z]\d{3}\d{4})$/', $cni);
  }

  public static function isUniqueRow(int $count): bool
  {
    return $count === 0;
  }

  // === Pour usage statique (si jamais requis dans certains contextes) ===
  private static array $staticErrors = [];

  public static function addError(string $field, string $message): void
  {
    self::$staticErrors[$field][] = $message;
  }

  public static function isValid(): bool
  {
    return count(self::$staticErrors) === 0;
  }

  public static function getStaticErrors(): array
  {
    return self::$staticErrors;
  }

  public static function resetErrors(): void
  {
    self::$staticErrors = [];
  }
}
