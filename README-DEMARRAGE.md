# 🚀 Guide de Démarrage Rapide - QR Generator PHP

## Installation Express (5 minutes)

### 1. Prérequis
- PHP 8.0+ avec extensions : PDO, PDO_PGSQL, cURL, GD, JSON, mbstring
- PostgreSQL (ou accès à une base de données)

### 2. Configuration Base de Données

#### Option A: Base locale PostgreSQL
```bash
# Créer la base de données
createdb qr_generator

# Variables d'environnement
export PGHOST=localhost
export PGPORT=5432
export PGDATABASE=qr_generator
export PGUSER=postgres
export PGPASSWORD=votre_mot_de_passe
```

#### Option B: Base de données hébergée
```bash
# Exemple avec une base Supabase/Neon/Railway
export PGHOST=votre-host.com
export PGPORT=5432
export PGDATABASE=votre_db
export PGUSER=votre_user
export PGPASSWORD=votre_password
```

### 3. Installation Automatique
```bash
# Lancer l'installeur web
php -S localhost:8080 -t . server.php
# Puis aller sur http://localhost:8080/install.php
```

### 4. Démarrage
```bash
# Démarrage simple
php -S localhost:8080 -t . server.php

# Ou avec le script
chmod +x quick-start.sh
./quick-start.sh
```

### 5. Accès
- **Application**: http://localhost:8080
- **Admin**: http://localhost:8080/admin
- **Test**: http://localhost:8080/simple-test.php

## Comptes de Test

Après installation, utilisez ces comptes :

| Rôle | Email | Mot de passe | Accès |
|------|-------|--------------|-------|
| Admin | admin@qr-generator.com | admin123 | /admin |
| User | user@test.com | user123 | /dashboard |

## Fonctionnalités

### ✅ Interface Utilisateur
- Tableau de bord avec analytics
- Création QR codes multi-formats
- Gestion et personnalisation
- Suivi des scans en temps réel

### ✅ Types QR Supportés
- **URL** - Redirection vers sites web
- **Email** - Email pré-rempli
- **Téléphone** - Appel automatique
- **SMS** - Message pré-écrit
- **WiFi** - Partage credentials
- **vCard** - Carte de visite digitale
- **Texte** - Texte simple

### ✅ Administration
- Gestion utilisateurs
- Surveillance QR codes
- Analytics globaux
- Informations système

### ✅ Analytics
- Géolocalisation des scans
- Types d'appareils
- Tendances temporelles
- Export des données

## Résolution de Problèmes

### Erreur 500
```bash
# Vérifier les logs
tail -f /var/log/apache2/error.log

# Permissions
chmod 644 *.php
chmod 755 uploads logs cache
```

### Base de données
```bash
# Test de connexion
php -r "
try {
    \$pdo = new PDO('pgsql:host=localhost;dbname=qr_generator', 'user', 'pass');
    echo 'Connexion OK';
} catch(Exception \$e) {
    echo 'Erreur: ' . \$e->getMessage();
}
"
```

### Extensions manquantes
```bash
# Ubuntu/Debian
sudo apt install php-pdo php-pgsql php-curl php-gd php-json php-mbstring

# CentOS/RHEL
sudo yum install php-pdo php-pgsql php-curl php-gd php-json php-mbstring
```

## Structure du Projet

```
php-version/
├── config/           # Configuration
├── controllers/      # Contrôleurs MVC
├── models/          # Modèles de données
├── views/           # Templates
├── lib/             # Bibliothèques
├── routes/          # Définition des routes
├── uploads/         # Fichiers uploadés
├── logs/            # Logs application
└── cache/           # Cache temporaire
```

## Support

- **Documentation**: Consultez les fichiers README
- **Test**: Utilisez `simple-test.php` pour diagnostiquer
- **Logs**: Vérifiez les logs d'erreur PHP/Apache
- **Contact**: contact@invexqr.com

---

**L'application est maintenant prête à l'emploi !** 🎉