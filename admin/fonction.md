admin_session.php : Gère la sécurité et vérifie que seul un administrateur peut accéder à ces pages.

admin_header.php, admin_sidebar.php, admin_footer.php : Composants réutilisables pour l'interface.
admin_index.php : Le tableau de bord principal avec des statistiques en temps réel.
admin_pharmacies.php : Gestion complète (Ajout, Modification, Suppression) des pharmacies.
admin_pompiers.php : Gestion des services de pompiers.
admin_police_gendarmerie.php : Gestion des forces de l'ordre.
admin_hopitaux.php : Gestion des services hospitaliers et ambulances.
admin_utilisateurs.php : Gestion des comptes utilisateurs et de leurs rôles.

Remarque importante sur les mots de passe : dans votre table, les hachages font 64 caractères hexadécimaux (ex: 15e2b0d3c33891eb...), ce qui correspond à du SHA-256 simple (sans sel), pas à password_hash() de PHP (qui produit du bcrypt, ~60 caractères commençant par $2y$). J'ai donc utilisé hash('sha256', ...) pour rester compatible avec vos données existantes et votre système de connexion actuel. Si votre admin_session.php ou une page de login utilise password_hash()/password_verify(), dites-le-moi — je devrai adapter ce fichier en conséquence, sinon les nouveaux utilisateurs ne pourront pas se connecter.

Fichier créé, lu un fichier
Fichier créé, lu un fichier

Ce qu'il faut vérifier/adapter de votre côté :

Hachage du mot de passe : j'utilise hash('sha256', ...) pour correspondre à vos hachages existants (64 caractères hex). Confirmez que c'est bien ce que votre page de login utilise pour password_verify — sinon dites-moi et je passe à password_hash().
Session utilisateur connecté : j'utilise $_SESSION['id_utilisateur'] pour empêcher l'auto-suppression/désactivation. Si votre admin_session.php stocke l'ID sous un autre nom (ex: $_SESSION['user_id']), il faudra ajuster cette ligne.
Onglets par rôle : Administrateur = bleu, Rédacteur = vert, Visiteur = gris (couleurs pleines, cohérentes avec admin_services.php).
Changement de rôle rapide : un <select> dans le tableau permet de changer le rôle d'un utilisateur directement, sans passer par le modal.

RSS TVM https://rss.app/feeds/YWDnoHaTjFT5BLvE.xml

RSS DS https://rss.app/feeds/la9wSCpXRhfVMaIn.xml

RSS ATSIRANANA  https://rss.app/feeds/kjzhHtJWKZM1wF1c.xml