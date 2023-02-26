# Bibliothèque Symfony 6.2
Projet d'étude réalisé avec Symfony 6.2 pour gérer une bibliothèque en ligne.

## Description
L'application permet de gérer les utilisateurs, les auteurs, les livres et les emprunts d'une bibliothèque en ligne.

## Fonctionnalités
- Création, édition et suppression d'utilisateurs
- Création, édition et suppression d'auteurs
- Création, édition et suppression de livres
- Gestion des emprunts de livres
## Configuration requise
- PHP 8.0 ou supérieur
- MySQL 5.7 ou supérieur
- Apache 2.4 ou supérieur
- Composer
## Installation
1. Clonez le projet depuis GitHub
2. Installez les dépendances avec la commande composer install
3. Configurez la base de données dans le fichier .env :
`code`
DATABASE_URL=mysql://user:password@localhost:port/bibliothèque
`code`
4. Créez la base de données avec la commande php bin/console doctrine:database:create
5. Migrez les schémas de base de données avec la commande php bin/console doctrine:migrations:migrate
6. Lancez le serveur de développement avec la commande symfony server:start
## Utilisation
1. Connectez-vous à l'application avec votre compte utilisateur ou créez-en un nouveau.
2. Ajoutez des auteurs et des livres à la bibliothèque.
3. Empruntez des livres et gérez les emprunts en cours.
4. Consultez les statistiques de la bibliothèque.
## Auteur
Antoine Mura

### License
Ce projet est sous license MIT License.
