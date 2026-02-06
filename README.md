# 🏛️ STELA - Social Network

**Stela** est un réseau social thématique où les utilisateurs "s'incarnent" pour "graver" leurs pensées sur des "stèles". Ce projet est développé avec **Symfony**, **TailwindCSS** et **Docker**.

## Sommaire
- [Fonctionnalités](#-fonctionnalités)
- [Prérequis](#-prérequis)
- [Installation](#-installation)
- [Utilisation](#-utilisation)
- [Stack Technique](#-stack-technique)

---

## Fonctionnalités

Voici la liste des fonctionnalités implémentées dans le projet, classées par complexité :

### 🟢 Niveau Facile
- **Dates Relatives** : Affichage dynamique du temps écoulé depuis la publication (ex: "il y a 10 min", "1h", "2j") grâce à une extension Twig personnalisée (`ago`).
- **Tweets Populaires** : Algorithme de tri mettant en avant les publications ayant le plus d'engagement (Likes + Vues) dans la section "Tendances".

### 🟡 Niveau Intermédiaire
- **Ajout d'Images** : Possibilité d'uploader des images lors de la création d'un tweet ou d'une réponse (gestion via `VichUploader` et `Slugger`).
- **Système de Likes (Consécrations)** : Interaction en temps réel (AJAX) sans rechargement de page. Le compteur et l'état du bouton s'actualisent instantanément.
- **Système de Commentaires (Annotations)** : Possibilité de répondre aux tweets. Les réponses sont gérées comme des entités `Tweet` avec un lien `parentTweet`, affichées dans un onglet dédié sur le profil.
- **Statistiques de Vues** : Chaque affichage d'un tweet incrémente un compteur de vues (Contemplations), visible sur la carte du tweet. On ne peut pas ajouter une vue pour son propre tweet, avec un maximum d'une vue par utilisateur.

### 🔵 Autres Fonctionnalités (Core)
- **Authentification & Inscription** : Connexion sécurisée, hashage de mot de passe, contraintes de validation.
- **Fil d'Actualité (Feed)** :
    - Pagination performante (KnpPaginator).
    - Mélange intelligent des tweets des abonnements et de suggestions de contenu.
    - Tri antéchronologique.
- **Profil Utilisateur** : Édition du profil (Bio, Avatar), onglets séparés (Stèles / Annotations).
- **Internationalisation (i18n)** : Site entièrement traduit en Français et Anglais.

---

## Prérequis

Avant de commencer, assurez-vous d'avoir installé :
* [Docker](https://www.docker.com/) & Docker Compose
* [Git](https://git-scm.com/)

---

## Installation

Suivez ces étapes pour lancer le projet en local :

1.  **Cloner le projet**
    ```bash
    git clone https://github.com/coda-school/php-eval-skel-2025-3.git
    ```

2.  **Lancer les conteneurs Docker**
    ```bash
    docker compose up -d
    ```

3.  **Installer les dépendances PHP**
    ```bash
    docker compose exec php composer install
    ```

4.  **Configurer la Base de Données**
    ```bash
    # Création de la BDD
    docker compose exec php php bin/console doctrine:database:create

    # Exécution des migrations (Création des tables)
    docker compose exec php php bin/console doctrine:migrations:migrate
    ```

5.  **Charger les données de test (Fixtures)**
    *Cette étape est cruciale pour tester les "Tweets Populaires" et le Feed.*
    ```bash
    docker compose exec php php bin/console doctrine:fixtures:load --no-interaction
    ```

---

## Utilisation

Une fois l'installation terminée, accédez au site via :
👉 **http://localhost** (ou le port configuré dans votre docker-compose).

### Actions principales
1.  **Graver (Poster)** : Utilisez le formulaire en haut du fil d'actualité pour poster du texte et une image.
2.  **Explorer** : Visualisation des Stèles (Tweets) sur la page principale, ainsi que des rumeurs sur le côté droit.
3.  **Interagir** : Cliquez sur le cœur pour "Consacrer" (Liker) ou sur la bulle pour "Annoter" (Commenter).
4.  **Paramètres** : Cliquez sur votre avatar puis "Structure" pour changer la langue ou le thème.

---

## Stack Technique

* **Backend** : Symfony 7, PHP 8.3
* **Base de données** : PostgreSQL
* **Frontend** : Twig, TailwindCSS, JavaScript

---

*© 2025 Stela Corp. - Le Scribe attend votre vérité.*
