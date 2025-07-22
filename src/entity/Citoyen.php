<?php


namespace App\Entity;

use App\Core\abstract\AbstractEntity;

class Citoyen extends AbstractEntity
{
  private int $id;
  private string $nom;
  private string $prenom;
  private string $cni;
  private string $lieuNaissance;
  private string $dateNaissance;
  private ?string $urlCopieCni;

  public function __construct(
    string $nom,
    string $prenom,
    string $cni,
    string $lieuNaissance,
    string $dateNaissance,
    ?string $urlCopieCni = null
  ) {
    $this->nom = $nom;
    $this->prenom = $prenom;
    $this->cni = $cni;
    $this->lieuNaissance = $lieuNaissance;
    $this->dateNaissance = $dateNaissance;
    $this->urlCopieCni = $urlCopieCni;
  }

  // Getters
  public function getId(): int
  {
    return $this->id;
  }
  public function getNom(): string
  {
    return $this->nom;
  }
  public function getPrenom(): string
  {
    return $this->prenom;
  }
  public function getCni(): string
  {
    return $this->cni;
  }
  public function getLieuNaissance(): string
  {
    return $this->lieuNaissance;
  }
  public function getDateNaissance(): string
  {
    return $this->dateNaissance;
  }
  public function getUrlCopieCni(): ?string
  {
    return $this->urlCopieCni;
  }

  // Setters
  public function setNom(string $nom): void
  {
    $this->nom = $nom;
  }
  public function setPrenom(string $prenom): void
  {
    $this->prenom = $prenom;
  }
  public function setCni(string $cni): void
  {
    $this->cni = $cni;
  }
  public function setLieuNaissance(string $lieuNaissance): void
  {
    $this->lieuNaissance = $lieuNaissance;
  }
  public function setDateNaissance(string $dateNaissance): void
  {
    $this->dateNaissance = $dateNaissance;
  }
  public function setUrlCopieCni(?string $url): void
  {
    $this->urlCopieCni = $url;
  }

  public function toArray(): array
  {
    return [
      'id' => $this->id ?? null,
      'nom' => $this->nom,
      'prenom' => $this->prenom,
      'cni' => $this->cni,
      'lieu_naissance' => $this->lieuNaissance,
      'date_naissance' => $this->dateNaissance,
      'url_copie_cni' => $this->urlCopieCni,
    ];
  }

  static public function toObject(array $row): static
  {
    $instance = new self(
      nom: $row['nom'] ?? '',
      prenom: $row['prenom'] ?? '',
      cni: $row['cni'] ?? '',
      lieuNaissance: $row['lieu_naissance'] ?? '',
      dateNaissance: $row['date_naissance'] ?? '',
      urlCopieCni: $row['url_copie_cni'] ?? null
    );

    if (isset($row['id'])) {
      $instance->id = (int) $row['id'];
    }

    return $instance;
  }
}
