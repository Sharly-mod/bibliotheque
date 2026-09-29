# Ma Bibliothèque

Application web PHP de gestion d'une bibliothèque : catalogue, auteurs,
emprunts, retours notés, messagerie entre membres et back-office.

## Fonctionnalités

- **Comptes** : inscription, connexion, déconnexion, espace personnel.
- **Catalogue** : livres et auteurs, recherche, fiche détaillée.
- **Emprunts** : reservation, suivi, retour, et notation du livre au retour
  (avec note par étoile et commentaire).
- **Messagerie** : envoi de messages entre utilisateurs.
- **Back-office** : gestion des utilisateurs, ajout et édition de livres,
  tableau de bord et statistiques.

## Stack

PHP procédural orienté objet, PDO et requêtes préparées MySQL, CSS sans
framework. Aucun gestionnaire de dépendances : le projet se déploie en
copiant les fichiers.

## Installation

1. Créer la base `bibliotheque` dans MySQL et y charger le schéma.
2. Copier `config.example.php` en `config.php` et renseigner les identifiants.
3. Pointer un serveur web (XAMPP, WAMP, ou le serveur intégré de PHP) sur le
   dossier du projet.

> Le schéma SQL n'est pas fourni avec ce dépôt. Les tables attendues sont
> celles utilisées par `books.php`, `auteurs.php`, `borrow.php`,
> `return_with_rating.php`, `send_message.php` et `admin_users.php`.

## Arborescence

Les scripts sont à plat dans la racine : `dashboard.php` (accueil),
`books.php`, `auteurs.php`, `add_book.php`, `edit_book.php`,
`book_details.php`, `borrow.php`, `return.php`, `return_with_rating.php`,
`mon_compte.php`, `send_message.php`, `delete_message.php`,
`admin_users.php`, `stats.php`, `login.php`, `register.php`, `logout.php`.
`navbar.php` fournit la navigation, incluse par chaque page.

## Licence

Projet de formation. Usage pédagogique.
