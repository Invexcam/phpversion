#!/bin/bash

echo "🚀 QR Generator PHP - Démarrage Rapide"
echo "======================================"

# Vérification PHP
if ! command -v php &> /dev/null; then
    echo "❌ PHP n'est pas installé"
    exit 1
fi

echo "✅ PHP $(php -v | head -n1 | cut -d' ' -f2) détecté"

# Vérification des extensions
echo "📋 Vérification des extensions PHP..."
php -m | grep -E "(pdo|curl|gd|json|mbstring)" > /dev/null
if [ $? -eq 0 ]; then
    echo "✅ Extensions de base disponibles"
else
    echo "⚠️ Certaines extensions peuvent être manquantes"
fi

# Création des dossiers nécessaires
echo "📁 Création des dossiers..."
mkdir -p uploads logs cache
chmod 755 uploads logs cache

# Démarrage du serveur
echo "🌐 Démarrage du serveur sur http://localhost:8080"
echo "Appuyez sur Ctrl+C pour arrêter"
echo ""

php -S localhost:8080 -t . server.php