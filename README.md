# AppDAF

Application web monolithique distribuée pour la gestion des citoyens, réalisée dans le cadre de l’apprentissage de la programmation orientée objet (POO) à l’ODC.

---

## Sommaire

- [AppDAF](#appdaf)
  - [Sommaire](#sommaire)
  - [Présentation](#présentation)
  - [Architecture](#architecture)
  - [Prérequis](#prérequis)
  - [Installation](#installation)
  - [Lancement de l’application](#lancement-de-lapplication)
  - [Base de données](#base-de-données)
    - [Migration](#migration)
    - [Seed (données de test)](#seed-données-de-test)
  - [Utilisation de l’API](#utilisation-de-lapi)
    - [Récupérer tous les citoyens](#récupérer-tous-les-citoyens)
    - [Récupérer un citoyen par CNI](#récupérer-un-citoyen-par-cni)
  - [Structure du projet](#structure-du-projet)
  - [Dépendances](#dépendances)
  - [Auteurs](#auteurs)

---

## Présentation

AppDAF est une application web permettant de gérer des citoyens (création, recherche, listing, etc.).  
Elle est conçue en PHP (POO), utilise PostgreSQL pour la base de données, et est orchestrée via Docker pour une distribution facile des composants (base, app, nginx).

---

## Architecture

- **Monolithe distribué** : tout le code applicatif est dans un seul projet, mais chaque composant (app PHP, base PostgreSQL, serveur Nginx) tourne dans son propre conteneur Docker.
- **Communication interne** : les conteneurs communiquent via le réseau Docker (ex : PHP ↔ PostgreSQL via le service `db`).

```
+-------------------+      +-------------------+      +-------------------+
|    Nginx (9080)   | ---> |   App PHP (fpm)   | ---> |   PostgreSQL (db) |
+-------------------+      +-------------------+      +-------------------+
```

---

## Prérequis

- Docker & Docker Compose
- PHP >= 8.0 (pour développement local hors Docker)
- Composer

---

## Installation

1. **Clone le dépôt :**

   ```bash
   git clone <url-du-repo>
   cd AppDAF
   ```

2. **Installe les dépendances PHP :**

   ```bash
   composer install
   ```

3. **Crée le fichier `.env` à la racine du projet :**
   ```env
   DSN=pgsql:host=db;port=5432;dbname=appdaf
   DB_USER=appdaf
   DB_PASSWORD=appdaf
   ```

---

## Lancement de l’application

1. **Démarre les conteneurs Docker :**

   ```bash
   docker-compose up -d
   ```

2. **Accède à l’application :**
   - Frontend/API : [http://localhost:9080](http://localhost:9080)

---

## Base de données

- **SGBD** : PostgreSQL (image officielle Docker)
- **Nom de la base** : `appdaf`
- **Utilisateur** : `appdaf`
- **Mot de passe** : `appdaf`
- **Port exposé** : `55432` (host) → `5432` (container)

### Migration

Pour créer la base et les tables :

```bash
php migrations/migration.php
```

### Seed (données de test)

Pour insérer des citoyens de test :

```bash
php migrations/Seeder.php
```

---

## Utilisation de l’API

### Récupérer tous les citoyens

- **GET** `/citoyens`
- **Réponse :**
  ```json
  {
    "data": [
      {
        "id": 1,
        "nom": "DIA",
        "prenom": "Moussa",
        "cni": "2020999999999",
        "lieu_naissance": "Dakar",
        "date_naissance": "1990-01-01",
        "url_copie_cni": "https://..."
      }
      // ...
    ],
    "statut": "success",
    "code": 200,
    "message": "Liste des citoyens récupérée avec succès"
  }
  ```

### Récupérer un citoyen par CNI

- **GET** `/citoyen/{cni}`
- **Exemple :** `/citoyen/2020999999999`
- **Réponse (succès) :**
  ```json
  {
    "data": {
      "id": 1,
      "nom": "DIA",
      "prenom": "Moussa",
      "cni": "2020999999999",
      "lieu_naissance": "Dakar",
      "date_naissance": "1990-01-01",
      "url_copie_cni": "https://..."
    },
    "statut": "success",
    "code": 200,
    "message": "Le numéro de carte d'identité a été retrouvé"
  }
  ```
- **Réponse (erreur) :**
  ```json
  {
    "data": null,
    "statut": "error",
    "code": 404,
    "message": "Le numéro de carte d'identité non retrouvé"
  }
  ```

---

## Structure du projet

- `app/` : cœur de l’application (config, core, middlewares, etc.)
- `src/` : code métier (controller, entity, repository, service)
- `migrations/` : scripts de migration et seed de la base
- `public/` : point d’entrée web (index.php)
- `routes/` : définition des routes
- `docker/` : configuration Docker et Nginx

---

## Dépendances

- `vlucas/phpdotenv` : gestion des variables d’environnement
- `symfony/yaml` : parsing YAML pour les services
- `twilio/sdk` : (optionnel) pour l’envoi de SMS
- `cloudinary/cloudinary_php` : (optionnel) pour la gestion des fichiers/images
- `ext-pdo` : extension PDO pour PHP

---

## Auteurs

- **abdourahamanetinkindjeeri99**  
  [abdourahamanetinkindjeeri99@gmail.com](mailto:abdourahamanetinkindjeeri99@gmail.com)

---

**Pour toute question ou bug, ouvre une issue ou contacte l’auteur.**
