# Guide de déploiement simple - InvexQR

## Installation en 3 étapes

### Étape 1 : Téléchargement des fichiers
1. **Téléchargez** tout le contenu du dossier `php-version/` vers votre serveur web
2. **Placez** les fichiers dans le répertoire racine (généralement `/public_html/` ou `/www/`)

### Étape 2 : Configuration automatique
1. **Accédez** à `https://votre-domaine.com/install.php` dans votre navigateur
2. **Suivez** l'assistant d'installation qui va :
   - Vérifier la configuration PHP 8
   - Tester les extensions requises
   - Configurer la base de données
   - Créer les tables automatiquement
   - Définir les permissions

### Étape 3 : Finalisation
1. **Supprimez** le fichier `install.php` après installation
2. **Testez** votre application : `https://votre-domaine.com/`

## Prérequis serveur

### Configuration requise
- **PHP** : Version 8.0 ou supérieure
- **Base de données** : PostgreSQL 12+ (recommandé) ou MySQL 8.0+
- **Extensions PHP** : PDO, PDO_PGSQL, cURL, GD, JSON, mbstring
- **Apache** : mod_rewrite activé
- **Mémoire** : 256 MB minimum

### Variables d'environnement
L'installeur vous demandera :
- **Hôte** : localhost (généralement)
- **Port** : 5432 pour PostgreSQL
- **Nom de base** : invexqr_db (ou votre choix)
- **Utilisateur** : Votre utilisateur DB
- **Mot de passe** : Votre mot de passe DB

## Fonctionnalités installées

### Plan gratuit (par défaut)
- Jusqu'à 3 QR codes par utilisateur
- QR codes dynamiques basiques
- Analytics de base
- Authentification Firebase et Replit

### Plan premium (5€/mois)
- QR codes illimités
- Upload de logos personnalisés
- Analytics avancées
- Support prioritaire
- Export haute résolution

## Résolution de problèmes

### Erreur 500 - Serveur interne
1. Vérifiez les logs d'erreur Apache
2. Contrôlez les permissions des fichiers (644)
3. Vérifiez la configuration PHP

### Erreur 404 - Page non trouvée
1. Assurez-vous que mod_rewrite est activé
2. Vérifiez le fichier .htaccess
3. Contrôlez l'emplacement des fichiers

### Problème de base de données
1. Vérifiez les identifiants de connexion
2. Testez la connexion PostgreSQL
3. Contrôlez que l'extension PDO_PGSQL est installée

## Support
- **Email** : contact@invexqr.com
- **Documentation** : Consultez les fichiers README
- **Diagnostic** : Utilisez diagnostic.php si nécessaire

## Après installation
1. Créez votre premier compte administrateur
2. Testez la création d'un QR code
3. Configurez PayPal pour les abonnements premium
4. Personnalisez les paramètres selon vos besoins

L'installation complète prend généralement 5-10 minutes selon votre configuration serveur.