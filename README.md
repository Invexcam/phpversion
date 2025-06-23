# QR Code Generator PHP - Version Complète

Une réplique exacte de l'application Node.js/React en PHP pur avec interface d'administration complète.

## 🚀 Fonctionnalités

### Interface Utilisateur
- **Page d'accueil** avec statistiques en temps réel
- **Authentification** complète (inscription, connexion, réinitialisation)
- **Tableau de bord** utilisateur avec analytics
- **Générateur QR** avec support de multiples types de contenu
- **Gestion des QR codes** (création, modification, suppression)
- **Analytics détaillés** par QR code et utilisateur

### Interface d'Administration
- **Dashboard administrateur** avec vue d'ensemble
- **Gestion des utilisateurs** (création, modification, suppression)
- **Gestion des QR codes** de tous les utilisateurs
- **Surveillance des scans** en temps réel
- **Informations système** et paramètres
- **Contrôle d'accès** basé sur les rôles

### Types de QR Codes Supportés
- **URL/Site Web** - Redirection vers n'importe quel site
- **Texte** - Affichage de texte simple
- **Email** - Email pré-rempli avec sujet et corps
- **Téléphone** - Lancement d'appel automatique
- **SMS** - Message SMS pré-écrit
- **WiFi** - Partage de credentials WiFi
- **vCard** - Carte de visite numérique

### Analytics Avancés
- **Statistiques globales** par utilisateur
- **Géolocalisation** des scans
- **Types d'appareils** utilisés
- **Tendances temporelles** des scans
- **QR codes performants** avec classement
- **Activité récente** en temps réel

## 📁 Structure du Projet

```
php-version/
├── config/
│   ├── bootstrap.php          # Configuration générale
│   └── database.php          # Connexion base de données
├── controllers/
│   ├── AuthController.php    # Authentification
│   ├── QRController.php      # Gestion QR codes
│   ├── DashboardController.php # Tableau de bord
│   ├── AdminController.php   # Administration
│   └── PublicController.php  # Pages publiques
├── models/
│   ├── User.php              # Modèle utilisateur
│   ├── QRCode.php            # Modèle QR code
│   └── QRScan.php            # Modèle scan
├── views/
│   ├── layouts/base.php      # Template de base
│   ├── auth/                 # Vues authentification
│   ├── dashboard/            # Vues tableau de bord
│   ├── admin/                # Vues administration
│   └── public/               # Vues publiques
├── lib/
│   └── QRGenerator.php       # Générateur QR codes
├── routes/
│   └── web.php               # Définition des routes
├── .htaccess                 # Configuration Apache
├── composer.json             # Dépendances PHP
└── index.php                 # Point d'entrée
```

## 🛠 Installation

### Prérequis
- PHP 8.0+
- PostgreSQL
- Apache/Nginx avec mod_rewrite
- Extension GD pour la génération d'images

### Configuration Base de Données
L'application utilise la même base de données PostgreSQL que la version Node.js. Les variables d'environnement suivantes sont requises :

```env
PGHOST=localhost
PGPORT=5432
PGDATABASE=qr_generator
PGUSER=postgres
PGPASSWORD=votre_mot_de_passe
DATABASE_URL=postgresql://user:password@host:port/database
```

### Installation des Dépendances
```bash
cd php-version
composer install
```

### Configuration Apache
Le fichier `.htaccess` est déjà configuré pour :
- Redirection des routes vers `index.php`
- Headers de sécurité
- Protection des fichiers sensibles

### Structure de Base de Données
L'application utilise les mêmes tables que la version Node.js :
- `users` - Informations utilisateurs
- `qr_codes` - QR codes créés
- `qr_scans` - Historique des scans
- `sessions` - Sessions utilisateur

## 🔧 Configuration

### Sécurité
- Protection CSRF sur tous les formulaires
- Validation des entrées utilisateur
- Hashage sécurisé des mots de passe
- Headers de sécurité configurés
- Contrôle d'accès basé sur les rôles

### Routes Principales

#### Routes Publiques
- `GET /` - Page d'accueil
- `GET /login` - Connexion
- `GET /register` - Inscription
- `GET /s/{shortCode}` - Redirection QR code

#### Routes API
- `POST /api/auth/login` - Authentification
- `POST /api/auth/register` - Inscription
- `GET /api/qr-codes` - Liste QR codes utilisateur
- `POST /api/qr-codes` - Création QR code
- `PUT /api/qr-codes/{id}` - Modification QR code
- `DELETE /api/qr-codes/{id}` - Suppression QR code

#### Routes Administration
- `GET /admin` - Dashboard admin
- `GET /admin/users` - Gestion utilisateurs
- `GET /admin/qr-codes` - Gestion QR codes
- `POST /admin/users/create` - Création utilisateur
- `DELETE /admin/users/{id}` - Suppression utilisateur

## 🎨 Interface Utilisateur

### Technologies Frontend
- **Tailwind CSS** - Framework CSS utilitaire
- **Alpine.js** - Framework JavaScript léger
- **Chart.js** - Graphiques et analytics
- **Font Awesome** - Icônes

### Fonctionnalités Interface
- Design responsive mobile-first
- Interface moderne et intuitive
- Notifications toast en temps réel
- Modales pour création/édition
- Tableaux avec pagination
- Graphiques interactifs

## 🔐 Authentification et Autorisation

### Système d'Authentification
- Inscription avec validation email
- Connexion sécurisée
- Réinitialisation de mot de passe
- Sessions persistantes
- Protection CSRF

### Niveaux d'Accès
- **Utilisateur** : Gestion de ses propres QR codes
- **Administrateur** : Accès complet au système

## 📊 Analytics et Suivi

### Données Collectées
- Adresse IP du scan
- User Agent (type d'appareil)
- Géolocalisation (pays/ville)
- Horodatage précis
- QR code scanné

### Rapports Disponibles
- Vue d'ensemble des performances
- Tendances temporelles
- Répartition géographique
- Types d'appareils utilisés
- QR codes les plus performants

## 🚀 Déploiement

### Configuration Production
1. Configurer les variables d'environnement
2. Installer les dépendances avec `composer install --no-dev`
3. Configurer le serveur web (Apache/Nginx)
4. Sécuriser les permissions des fichiers
5. Configurer SSL/HTTPS

### Sécurité Production
- Masquer les erreurs PHP (`display_errors = Off`)
- Utiliser des secrets forts pour les sessions
- Configurer les headers de sécurité
- Limiter les permissions fichiers
- Surveiller les logs d'accès

## 🔧 Maintenance

### Logs et Monitoring
- Logs d'erreurs PHP dans les logs système
- Monitoring des performances base de données
- Surveillance de l'utilisation disque
- Alertes en cas d'erreur critique

### Sauvegarde
- Sauvegarde régulière de la base de données
- Sauvegarde des fichiers uploadés
- Tests de restauration périodiques

## 📈 Performance

### Optimisations Implémentées
- Requêtes SQL optimisées avec index
- Cache des sessions en base de données
- Génération QR codes optimisée
- Compression des images
- Minimisation des requêtes

### Recommandations
- Utiliser un cache Redis en production
- Optimiser les images avec WebP
- Mettre en place un CDN
- Configurer la compression gzip

## 🛡 Sécurité

### Mesures de Protection
- Validation stricte des entrées
- Protection contre l'injection SQL
- Protection CSRF sur tous les formulaires
- Hashage sécurisé des mots de passe
- Headers de sécurité configurés
- Protection contre le XSS

### Audit de Sécurité
- Tests de pénétration réguliers
- Mise à jour des dépendances
- Monitoring des vulnérabilités
- Logs de sécurité

## 📞 Support

### Fonctionnalités de Support
- Interface de contact intégrée
- Logs détaillés pour le debugging
- Documentation complète
- Code commenté et structuré

### Extensions Possibles
- Intégration d'API de géolocalisation
- Système de notifications email
- Export des données en CSV/PDF
- API REST complète
- Intégration avec des services tiers

---

Cette application PHP est une réplique complète et fonctionnelle de la version Node.js, avec des fonctionnalités supplémentaires d'administration et une architecture sécurisée pour un environnement de production.