<?php
// Script de démarrage pour l'application PHP QR Generator
echo "🚀 Démarrage de l'application QR Generator PHP...\n\n";

// Vérification de l'environnement
echo "📋 Vérification de l'environnement:\n";
echo "- Version PHP: " . phpversion() . "\n";

// Extensions requises
$extensions = ['pdo', 'pdo_pgsql', 'curl', 'gd', 'json', 'mbstring'];
$missing = [];

foreach ($extensions as $ext) {
    if (extension_loaded($ext)) {
        echo "✅ Extension $ext: OK\n";
    } else {
        echo "❌ Extension $ext: MANQUANTE\n";
        $missing[] = $ext;
    }
}

if (!empty($missing)) {
    echo "\n⚠️ Extensions manquantes: " . implode(', ', $missing) . "\n";
    echo "Installez ces extensions avant de continuer.\n\n";
}

// Vérification des fichiers
echo "\n📁 Vérification des fichiers:\n";
$files = [
    'config/bootstrap.php',
    'config/database.php', 
    'routes/web.php',
    'index.php',
    '.htaccess'
];

foreach ($files as $file) {
    if (file_exists($file)) {
        echo "✅ $file: OK\n";
    } else {
        echo "❌ $file: MANQUANT\n";
    }
}

// Instructions de démarrage
echo "\n🔧 Instructions de démarrage:\n\n";
echo "1. Configuration de la base de données:\n";
echo "   - Créez une base PostgreSQL\n";
echo "   - Configurez les variables d'environnement:\n";
echo "     export PGHOST=localhost\n";
echo "     export PGPORT=5432\n";
echo "     export PGDATABASE=qr_generator\n";
echo "     export PGUSER=votre_utilisateur\n";
echo "     export PGPASSWORD=votre_mot_de_passe\n\n";

echo "2. Installation automatique:\n";
echo "   php install.php\n\n";

echo "3. Démarrage du serveur:\n";
echo "   php -S localhost:8080 -t . server.php\n\n";

echo "4. Accès à l'application:\n";
echo "   http://localhost:8080\n\n";

echo "📚 Fonctionnalités disponibles:\n";
echo "- Interface utilisateur complète\n";
echo "- Génération QR codes (URL, Email, SMS, WiFi, vCard)\n";
echo "- Analytics et suivi des scans\n";
echo "- Interface d'administration\n";
echo "- Authentification sécurisée\n\n";

echo "🔑 Comptes de test (après installation):\n";
echo "- Admin: admin@qr-generator.com / admin123\n";
echo "- User: user@test.com / user123\n\n";

echo "Pour démarrer maintenant, exécutez:\n";
echo "php -S localhost:8080 -t . server.php\n";
?>