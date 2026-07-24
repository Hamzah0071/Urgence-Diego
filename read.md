# Urgences Antsiranana — Résumé du travail & cycle de vie de la session

## 1. Contexte du projet

Le projet **Urgences Antsiranana** est un site web (PHP + MySQL/MariaDB) qui centralise, pour la ville d'Antsiranana :

- les numéros et emplacements des services d'urgence (pharmacies, pompiers, police, hôpitaux, ambulances) ;
- les tours de garde des pharmacies ;
- un fil d'actualités alimenté de deux façons : automatiquement via des flux RSS de pages Facebook (`sources_articles`), et manuellement par des rédacteurs.

Trois rôles existent dans la table `role` : **Visiteur**, **Redacteur**, **Administrateur**.

## 2. Résumé des fonctionnalités mises en place

### 2.1 Espace Rédacteur — publication d'articles (`client/new-actualites.php`)

Le rédacteur ne publie jamais de média directement sur le site : il publie d'abord sa photo, vidéo ou audio sur la page Facebook de l'organisation, puis vient sur le site :

1. il retape le **titre** et une **description** de sa publication ;
2. il colle le **lien** de la publication Facebook correspondante ;
3. l'ensemble est enregistré dans la table `article`, avec `id_source = NULL` (pour le distinguer d'un article importé automatiquement par flux RSS) et **`statut = 'brouillon'`**.

Un onglet **Historique** liste tous les articles du rédacteur connecté (les siens uniquement, filtrés par `id_auteur`), avec la possibilité de les modifier ou de les supprimer. Modifier un article déjà publié le repasse automatiquement en `brouillon`, pour forcer une nouvelle validation.

### 2.2 Validation par l'administrateur (`admin/import_articles.php`)

Plutôt que de créer une page séparée, la validation a été intégrée à la page d'administration existante qui actualise déjà les flux RSS. Elle affiche désormais, sous le rapport d'import, la liste des articles rédacteur en attente (`statut = 'brouillon'`), avec pour chacun :

- l'auteur, un aperçu du contenu, la date, le lien Facebook ;
- un bouton **Valider** (`statut → 'publie'`, l'article devient visible sur le site public) ;
- un bouton **Refuser** (`statut → 'archive'`, l'article reste invisible du public, sans être supprimé).

Cliquer sur Valider/Refuser ne relance pas le téléchargement des flux RSS : une redirection interne (`?vue=validation`) sépare cette action de l'import proprement dit.

### 2.3 Page Profil (`client/profil.php`)

Affiche pour l'utilisateur connecté : prénom, nom, email, quartier et rôle, récupérés par une jointure `utilisateur` → `quartier` → `role`.

### 2.4 Menu conditionnel (`includes/header.php`)

Le rédacteur et le client (Visiteur) voient la même base de menus (Accueil, Articles, Service d'urgence, Carte), à une exception près : le rédacteur voit en plus le lien **Publier**, affiché uniquement si son rôle correspond au rédacteur.

## 3. Le cycle de vie de la session, de la connexion à la destruction

C'est le point le plus important à bien comprendre, car toutes les pages ci-dessus en dépendent.

### Étape 1 — Connexion (`login.php`)

L'utilisateur soumet son email et son mot de passe. Le script vérifie ces identifiants contre la table `utilisateur`. Si c'est correct :

```php
session_start();
$_SESSION['id_utilisateur'] = $user['id_utilisateur'];
```

**Un seul identifiant est stocké en session à ce stade : `id_utilisateur`.** C'est la graine à partir de laquelle tout le reste est reconstruit à chaque page.

### Étape 2 — Le fichier `includes/session.php`, inclus sur chaque page protégée

Chaque page qui nécessite d'être connecté commence par :

```php
require_once '../includes/session.php';
```

Ce fichier fait quatre choses, dans l'ordre, à **chaque chargement de page** :

1. **`session_start()`** — reprend la session déjà ouverte (grâce au cookie de session envoyé par le navigateur).
2. **Vérifie la présence de `$_SESSION['id_utilisateur']`** — si absent, l'utilisateur n'est pas connecté : redirection immédiate vers `login.php`.
3. **Recharge les informations de l'utilisateur depuis la base**, à chaque requête (et non une seule fois à la connexion) :

   ```php
   SELECT u.id_utilisateur, u.nom, u.email, u.id_role, r.nom_role
   FROM utilisateur u
   JOIN role r ON u.id_role = r.id_role
   WHERE u.id_utilisateur = :id_utilisateur
   ```

   Ce choix a un avantage important : si un administrateur change le rôle d'un utilisateur ou désactive son compte, le changement s'applique **immédiatement**, dès la page suivante, sans attendre que l'utilisateur se reconnecte. Si l'utilisateur n'existe plus (compte supprimé), la session est détruite et la personne est renvoyée vers `login.php`.

4. **Expose deux formes de la même information** :
   - `$current_user` — un tableau PHP disponible uniquement pendant l'exécution de la page courante (nom, email, rôle...) ;
   - `$_SESSION['id_role']` et `$_SESSION['nom_role']` — les mêmes informations de rôle, mais stockées en session, pour que d'autres fichiers (comme le header) puissent les lire directement sans refaire de requête ni redupliquer la logique.

### Étape 3 — Utilisation du rôle dans le reste du site

Une fois `session.php` exécuté, deux usages typiques du rôle apparaissent :

- **`includes/header.php`** lit uniquement `$_SESSION['id_role']` pour décider quels liens de menu afficher. Il ne fait aucune requête, aucune logique métier : il affiche, c'est tout.

  ```php
  $est_redacteur = ((int) ($_SESSION['id_role'] ?? null) === 2);
  ```

- **Les pages elles-mêmes** (comme `new-actualites.php`) font un contrôle d'accès strict avant d'exécuter la moindre action :

  ```php
  if ((int)($_SESSION['id_role'] ?? 0) !== 2) {
      header('Location: ../login.php');
      exit;
  }
  ```

### Étape 4 — Déconnexion et destruction (`logout.php`)

Quand l'utilisateur clique sur "Déconnexion", le script doit :

```php
session_start();
$_SESSION = [];        // vide toutes les variables de session
session_destroy();     // détruit les données de session côté serveur
header('Location: ../login.php');
exit;
```

À partir de ce moment, `$_SESSION['id_utilisateur']` n'existe plus. La prochaine fois que `includes/session.php` sera exécuté (par exemple si la personne essaie d'accéder directement à une URL protégée), l'étape 2 ci-dessus le détectera et renverra vers `login.php`.

### Schéma récapitulatif

```
login.php
   │  vérifie email/mot de passe
   │  $_SESSION['id_utilisateur'] = ID
   ▼
includes/session.php (relancé à CHAQUE page)
   │  session_start()
   │  si pas de id_utilisateur en session → redirection login.php
   │  sinon → relit l'utilisateur en base (nom, email, id_role, nom_role)
   │  → $current_user (local à la page)
   │  → $_SESSION['id_role'], $_SESSION['nom_role'] (partagés, ex: header.php)
   ▼
Pages protégées (home.php, new-actualites.php, profil.php, import_articles.php...)
   │  utilisent $_SESSION['id_role'] pour afficher/autoriser selon le rôle
   ▼
logout.php
   │  $_SESSION = []
   │  session_destroy()
   ▼
Retour à l'état "non connecté"
```

## 4. Points de vigilance identifiés pendant le travail

- Le bug initial du menu rédacteur venait du fait que `session.php` ne stockait le rôle que dans `$current_user` (une variable locale à chaque page), pas dans `$_SESSION`. Le header, qui lisait `$_SESSION['id_role']`, ne trouvait donc jamais rien. La correction a été d'ajouter `$_SESSION['id_role']` dans `session.php`, une fois pour toutes.
- La contrainte `UNIQUE` sur `lien_source` dans la table `article` empêche qu'un même lien Facebook soit utilisé deux fois — utile contre les doublons, mais à garder en tête si un jour plusieurs articles doivent pouvoir pointer vers la même publication.
- Le statut `brouillon` sert désormais de file d'attente de modération : un article n'apparaît sur le site public que lorsque son `statut` passe à `publie`, ce qui n'arrive que par une action explicite d'un administrateur.