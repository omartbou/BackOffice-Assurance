# Backoffice de Courtage d'Assurance

Ce projet full-stack est conçu pour gérer les clients, leurs devis d'assurance, et leurs véhicules. Le backend est développé avec Symfony et API Platform, tandis que le frontend utilise Vue.js.

## Fonctionnalités

- Gestion des clients (Créer, Lire, Modifier, Supprimer)
- Gestion des devis d'assurance
- Gestion des véhicules
- Validation et gestion des erreurs côté frontend

## Prérequis

Assurez-vous d'avoir les éléments suivants installés :

- PHP 8.2 ou supérieur
- Symfony CLI
- Composer
- Node.js et npm
- MySQL ou tout autre base de données compatible avec Symfony

## Installation

### Backend (Symfony API Platform)

1. Cloner le dépôt :
   ```bash
   git clone https://github.com/omartbou/BackOffice-Assurance.git
   cd BackOffice-Assurance
   ```

2. Installer les dépendances :
   ```bash
   composer install
   ```

3. Configurer la base de données :

   Mettez à jour le fichier `.env` avec les informations de votre base de données :
   ```bash
   DATABASE_URL="mysql://root:@127.0.0.1:3306/assurance_db"
   ```

4. Exécuter les migrations :
   ```bash
   php bin/console doctrine:database:create
   php bin/console make:migration
   php bin/console doctrine:migrations:migrate
   ```




### Frontend (Vue.js)

1. Installer les dépendances :
   ```bash
   npm install
   npm install axios 
   npm install pinia
   ```

2. Démarrer le serveur de développement :
   ```bash
   npm run dev
   ```
   
3. Démarrer le serveur Symfony :
   ```bash
   symfony server:start
   ```
   Le projet sera disponible à l'adresse suivante : `http://localhost:8000/`

## Endpoints de l'API

Voici quelques endpoints clés de l'API :

- Clients
  - `GET /api/clients`: Récupérer tous les clients
  - `POST /api/clients/post`: Créer un nouveau client
  - `PUT /api/clients/edit/{id}`: Mettre à jour un client
  - `DELETE /api/clients/{id}`: Supprimer un client


- Devis
  - `GET /api/devis`: Récupérer tous les devis d'assurance
  - `POST /api/devis/post`: Créer un nouveau devis
  - `PUT /api/devis/edit/{id}`: Mettre à jour un devis
  - `DELETE /api/devis/delete/{id}`: Supprimer un devis

- Voitures
  - `GET /api/voitures`: Récupérer toutes les voitures
  - `POST /api/voitures/post`: Créer une nouvelle voiture
  - `PUT /api/voiture/edit/{id}`: Mettre à jour une voiture
  - `DELETE /api/voiture/delete/{id}`: Supprimer une voiture

## Remarques

- Validation : Le frontend inclut une validation basique des formulaires.
- Gestion des erreurs : Des mécanismes de gestion des erreurs sont mis en place côté frontend et backend.
- Base de données : L'application utilise une base de données MySQL par défaut, mais peut être configurée pour d'autres bases de données compatibles avec Symfony.

## Structure du Projet

### Backend (Symfony)

- `src/Entity/` : Contient les classes des entités PHP pour `Client`, `Devis`, et `Voiture`.
- `src/Repository/` : Contient les classes des repositories.
- `config/` : Fichiers de configuration pour Symfony et API Platform.
- `public/` : Répertoire public contenant le contrôleur frontal.

### Frontend (Vue.js)

- `src/components/` : Composants Vue pour afficher et gérer les données.
- `src/views/` : Vues principales pour les opérations CRUD.
  
 Si vous souhaitez apporter des modifications ou ajouter des informations supplémentaires, faites-le-moi savoir !
 
## Contact
 - `Email` :omar@bouziani.me
