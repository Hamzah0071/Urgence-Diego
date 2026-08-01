
Excellent choix. Pour un projet scolaire, LWS est parfait, économique et très formateur.
Voici la marche à suivre complète, étape par étape, depuis votre PC Windows pour configurer votre hébergement, importer vos fichiers et votre base de données SQL.
------------------------------
## Étape 1 : Commander l'hébergement

   1. Rendez-vous sur le site de LWS.
   2. Choisissez l'offre LWS Perso (généralement autour de 1,49 €/mois).
   3. Lors de la commande, LWS vous offre un nom de domaine gratuit (ex: monprojetsolaire.fr ou .com). Choisissez-en un disponible.
   4. Finalisez le paiement. Vous allez recevoir par e-mail vos identifiants de connexion (Espace Client, FTP, et MySQL). Conservez bien cet e-mail. [1, 2, 3, 4] 

------------------------------
## Étape 2 : Exporter votre base de données SQL (depuis votre Windows)

   1. Ouvrez votre outil local (WampServer, XAMPP ou Laragon).
   2. Allez sur votre phpMyAdmin local (http://localhost/phpmyadmin).
   3. Cliquez sur le nom de la base de données de votre projet dans la colonne de gauche.
   4. Cliquez sur l'onglet Exporter en haut, puis sur le bouton Exporter (le format par défaut est .sql).
   5. Enregistrez ce fichier sur votre bureau Windows. [5] 

------------------------------
## Étape 3 : Créer et importer la base de données sur LWS

   1. Connectez-vous à votre Espace Client LWS. [6] 
   2. Cliquez sur Gérer à côté de votre domaine, puis cherchez l'icône Base MySQL ou MySQL Backups.
   3. Cliquez sur Créer une base de données. LWS va vous générer :
   * Un nom de base de données (ex: lws_12345_prod)
      * Un utilisateur (ex: lws_12345)
      * Un mot de passe
      * Un serveur (ex: sql.lws.fr ou une adresse IP) [7] 
   4. Ouvrez le phpMyAdmin en ligne fourni par LWS (le lien est dans votre espace client).
   5. Sélectionnez votre nouvelle base vide à gauche, allez dans l'onglet Importer, choisissez le fichier .sql sauvegardé à l'étape 2, et validez.

------------------------------
## Étape 4 : Adapter votre code PHP (Gestion des identifiants)
Sur votre PC, ouvrez votre fichier de connexion à la base de données (souvent appelé connexion.php, db.php ou config via PDO).
Vous devez remplacer les identifiants locaux (localhost, root, pas de mot de passe) par ceux que LWS vient de vous donner :

try {
    // Remplacez par vos vraies informations LWS
    $db = new PDO('mysql:host=SERVEUR_LWS;dbname=NOM_DE_BASE_LWS;charset=utf8', 'UTILISATEUR_LWS', 'MOT_DE_PASSE_LWS');
} catch (Exception $e) {
    die('Erreur : ' . $e->getMessage());
}

Astuce GitHub : Si votre dépôt GitHub est public, ne poussez pas ce fichier avec les vrais mots de passe de LWS en ligne. Laissez des lignes vides ou utilisez un fichier .env ignoré par Git pour des raisons de sécurité.
------------------------------
## Étape 5 : Transférer les fichiers sur LWS via FTP
Puisque vous êtes sur Windows, la méthode la plus simple pour envoyer vos fichiers PHP est d'utiliser FileZilla.

   1. Téléchargez et installez gratuitement [FileZilla Client](https://filezilla-project.org/). [8, 9] 
   2. Ouvrez FileZilla et remplissez les cases tout en haut avec les informations de l'e-mail de LWS :
   * Hôte : l'adresse FTP fournie (ex: ftp.monprojetsolaire.fr)
      * Identifiant : votre utilisateur FTP
      * Mot de passe : votre mot de passe FTP
      * Port : 21 (ou laissez vide)
   3. Cliquez sur Connexion rapide.
   4. Dans la fenêtre de droite (le serveur en ligne), ouvrez le dossier nommé htdocs ou public_html (c'est le dossier public de votre site). Si vous voyez un fichier index.html par défaut de LWS, supprimez-le. [10] 
   5. Dans la fenêtre de gauche (votre PC Windows), cherchez le dossier de votre projet.
   6. Sélectionnez tous vos fichiers PHP, faites un clic droit, puis cliquez sur Envoyer. [11] 

Une fois le transfert terminé, ouvrez votre navigateur et tapez votre nom de domaine : votre site PHP/SQL d'école est en ligne !
------------------------------
Si vous le souhaitez, dites-moi :

* Quel outil utilisez-vous pour coder sous Windows (VS Code, Notepad++, etc.) ?
* Voulez-vous que l'on configure l'automatisation avec GitHub Actions pour éviter d'utiliser FileZilla à chaque modification ?

