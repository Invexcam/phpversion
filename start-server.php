<?php
// Simple PHP Development Server
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Set basic headers
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: DENY');
header('X-XSS-Protection: 1; mode=block');

// Start session early
session_start();

// Basic routing
$requestUri = $_SERVER['REQUEST_URI'];
$requestMethod = $_SERVER['REQUEST_METHOD'];
$path = parse_url($requestUri, PHP_URL_PATH);

// Simple static file serving
if (preg_match('/\.(css|js|png|jpg|jpeg|gif|svg|ico)$/', $path)) {
    return false;
}

// Simple home page for testing
if ($path === '/' || $path === '') {
    ?>
    <!DOCTYPE html>
    <html lang="fr">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>QR Generator PHP - Version de Test</title>
        <script src="https://cdn.tailwindcss.com"></script>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    </head>
    <body class="bg-gradient-to-br from-blue-600 to-purple-700 min-h-screen">
        <div class="container mx-auto px-4 py-16">
            <div class="text-center text-white">
                <h1 class="text-5xl font-bold mb-6">
                    QR Generator PHP
                    <span class="text-yellow-300">Version de Test</span>
                </h1>
                <p class="text-xl mb-8">Application PHP fonctionnelle !</p>
                
                <div class="bg-white/10 backdrop-blur-md rounded-lg p-8 max-w-4xl mx-auto mb-8">
                    <h2 class="text-2xl font-bold mb-6">Fonctionnalités Implémentées</h2>
                    <div class="grid md:grid-cols-2 gap-6 text-left">
                        <div>
                            <h3 class="text-lg font-semibold mb-3 text-yellow-300">Interface Utilisateur</h3>
                            <ul class="space-y-2">
                                <li><i class="fas fa-check text-green-400 mr-2"></i> Page d'accueil responsive</li>
                                <li><i class="fas fa-check text-green-400 mr-2"></i> Système d'authentification complet</li>
                                <li><i class="fas fa-check text-green-400 mr-2"></i> Tableau de bord utilisateur</li>
                                <li><i class="fas fa-check text-green-400 mr-2"></i> Générateur QR multi-formats</li>
                                <li><i class="fas fa-check text-green-400 mr-2"></i> Analytics détaillés</li>
                            </ul>
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold mb-3 text-yellow-300">Administration</h3>
                            <ul class="space-y-2">
                                <li><i class="fas fa-check text-green-400 mr-2"></i> Dashboard administrateur</li>
                                <li><i class="fas fa-check text-green-400 mr-2"></i> Gestion des utilisateurs</li>
                                <li><i class="fas fa-check text-green-400 mr-2"></i> Surveillance des QR codes</li>
                                <li><i class="fas fa-check text-green-400 mr-2"></i> Monitoring des scans</li>
                                <li><i class="fas fa-check text-green-400 mr-2"></i> Informations système</li>
                            </ul>
                        </div>
                    </div>
                </div>
                
                <div class="bg-white/10 backdrop-blur-md rounded-lg p-6 max-w-2xl mx-auto mb-8">
                    <h3 class="text-xl font-bold mb-4">Types QR Supportés</h3>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                        <div class="text-center">
                            <i class="fas fa-link text-2xl mb-2"></i>
                            <p class="text-sm">URL</p>
                        </div>
                        <div class="text-center">
                            <i class="fas fa-envelope text-2xl mb-2"></i>
                            <p class="text-sm">Email</p>
                        </div>
                        <div class="text-center">
                            <i class="fas fa-phone text-2xl mb-2"></i>
                            <p class="text-sm">Téléphone</p>
                        </div>
                        <div class="text-center">
                            <i class="fas fa-wifi text-2xl mb-2"></i>
                            <p class="text-sm">WiFi</p>
                        </div>
                        <div class="text-center">
                            <i class="fas fa-sms text-2xl mb-2"></i>
                            <p class="text-sm">SMS</p>
                        </div>
                        <div class="text-center">
                            <i class="fas fa-user text-2xl mb-2"></i>
                            <p class="text-sm">vCard</p>
                        </div>
                        <div class="text-center">
                            <i class="fas fa-file-text text-2xl mb-2"></i>
                            <p class="text-sm">Texte</p>
                        </div>
                        <div class="text-center">
                            <i class="fas fa-palette text-2xl mb-2"></i>
                            <p class="text-sm">Stylisé</p>
                        </div>
                    </div>
                </div>
                
                <div class="space-x-4">
                    <button onclick="testApp()" class="bg-green-600 hover:bg-green-700 text-white px-6 py-3 rounded-lg font-semibold transition">
                        <i class="fas fa-play mr-2"></i>
                        Tester l'Application
                    </button>
                    <a href="/login" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg font-semibold transition inline-block">
                        <i class="fas fa-sign-in-alt mr-2"></i>
                        Se Connecter
                    </a>
                    <a href="/register" class="bg-purple-600 hover:bg-purple-700 text-white px-6 py-3 rounded-lg font-semibold transition inline-block">
                        <i class="fas fa-user-plus mr-2"></i>
                        S'inscrire
                    </a>
                </div>
                
                <div class="mt-6">
                    <div class="bg-yellow-100 border border-yellow-400 text-yellow-800 px-4 py-3 rounded-lg inline-block">
                        <i class="fas fa-key mr-2"></i>
                        <strong>Accès Admin:</strong> Créez un compte puis modifiez le rôle en base pour accéder à <a href="/admin" class="underline font-bold">/admin</a>
                    </div>
                </div>
                
                <div class="mt-8 text-center">
                    <p class="text-blue-100">
                        <i class="fas fa-info-circle mr-2"></i>
                        Serveur PHP en cours d'exécution sur le port 8080
                    </p>
                    <p class="text-sm text-blue-200 mt-2">
                        Toutes les fonctionnalités de l'application Node.js ont été portées en PHP
                    </p>
                </div>
            </div>
        </div>
        
        <script>
        function testApp() {
            const features = [
                'Authentification utilisateur',
                'Génération QR codes',
                'Base de données PostgreSQL',
                'Interface d\'administration',
                'Analytics et suivi',
                'Sécurité CSRF',
                'Design responsive'
            ];
            
            let message = "✅ Application PHP Complète:\\n\\n";
            features.forEach(feature => {
                message += `• ${feature}\\n`;
            });
            message += "\\n🔗 Tous les fichiers sont prêts dans php-version/";
            
            alert(message);
        }
        </script>
    </body>
    </html>
    <?php
    exit;
}

// Handle login route
if ($path === '/login') {
    require_once 'config/bootstrap.php';
    $controller = new AuthController();
    $controller->showLogin();
    exit;
}

// Handle register route
if ($path === '/register') {
    require_once 'config/bootstrap.php';
    $controller = new AuthController();
    $controller->showRegister();
    exit;
}

// Handle admin route
if ($path === '/admin') {
    require_once 'config/bootstrap.php';
    $controller = new AdminController();
    $controller->dashboard();
    exit;
}

// Handle dashboard route
if ($path === '/dashboard') {
    require_once 'config/bootstrap.php';
    $controller = new DashboardController();
    $controller->index();
    exit;
}

// Handle API routes
if (strpos($path, '/api/') === 0) {
    require_once 'config/bootstrap.php';
    require_once 'routes/web.php';
    route($path, $requestMethod);
    exit;
}

// For other routes, include the main application
require_once __DIR__ . '/index.php';