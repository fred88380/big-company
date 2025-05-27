# My Big Company - Gestion des utilisateurs

Ce projet est une application web PHP/MySQL pour gérer les utilisateurs, employés et services d’une entreprise.

## Fonctionnalités principales

- Inscription et connexion sécurisées (mots de passe hashés)
- Ajout, modification et suppression d’utilisateurs
- Modification de l’email et du mot de passe séparément
- Limite de 5 utilisateurs maximum
- Interface d’administration (accès réservé à l’utilisateur ID 1)
- Navigation avec Bootstrap 5 et icônes Bootstrap Icons

## Installation

1. **Cloner le projet**
   ```bash
   git clone <url-du-repo>
   ```

2. **Configurer la base de données**
   - Crée une base `bigcompany` dans MySQL.
   - Exécute le script SQL pour créer la table `users` :
     ```sql
     CREATE TABLE users (
         id INT AUTO_INCREMENT PRIMARY KEY,
         email VARCHAR(255) NOT NULL UNIQUE,
         password VARCHAR(255) NOT NULL
     );
     ```

3. **Configurer les variables d’environnement**
   - Renomme le fichier `.env.example` en `.env` si besoin.
   - Renseigne tes identifiants MySQL dans `.env` :
     ```
     DB_HOST=localhost
     DB_NAME=bigcompany
     DB_USER=swades
     DB_PASS=ton_mot_de_passe
     DB_CHARSET=utf8
     ```

4. **Lancer le serveur local**
   - Place le dossier dans `htdocs` de XAMPP.
   - Accède à `http://localhost/projet/` dans ton navigateur.

## Structure du projet

- `index.html` : Page de connexion
- `register.html` : Page d’inscription
- `users.php` : Gestion des utilisateurs
- `edit_user_email.php` / `edit_user_password.php` : Modification email/mot de passe
- `admin.php` : Interface admin (ID 1)
- `includes/db.php` : Connexion à la base de données
- `.env` : Variables d’environnement (non versionné)
- `.gitignore` : Fichiers/dossiers à ignorer par Git

## Sécurité

- Les mots de passe sont stockés de façon sécurisée (hashés).
- Le fichier `.env` ne doit jamais être versionné.
- L’interface admin est réservée à l’utilisateur ID 1.

## Auteurs

- Projet réalisé par [Ton Nom] pour [Ton Organisation/Cours].

---