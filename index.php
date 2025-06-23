<?php
// Configuration d'erreur pour production
error_reporting(0);
ini_set('display_errors', 0);

try {
    // Vérifier que les fichiers existent
    if (!file_exists('config/bootstrap.php')) {
        throw new Exception('Fichier bootstrap manquant');
    }
    
    if (!file_exists('routes/web.php')) {
        throw new Exception('Fichier routes manquant');
    }
    
    require_once 'config/bootstrap.php';
    require_once 'routes/web.php';

    // Start session
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    // Get request URI and method
    $requestUri = $_SERVER['REQUEST_URI'] ?? '/';
    $requestMethod = $_SERVER['REQUEST_METHOD'] ?? 'GET';

    // Parse the URI
    $path = parse_url($requestUri, PHP_URL_PATH);
    
    // Nettoyer le path
    $path = trim($path, '/');
    if (empty($path)) {
        $path = '/';
    } else {
        $path = '/' . $path;
    }

    // Route the request
    route($path, $requestMethod);
    
} catch (Exception $e) {
    // En cas d'erreur, afficher une page simple
    http_response_code(500);
    ?>
    <!DOCTYPE html>
    <html lang="fr">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>InvexQR - Configuration en cours</title>
        <style>
            body { font-family: Arial, sans-serif; text-align: center; padding: 50px; background: #f8f9fa; }
            .container { max-width: 600px; margin: 0 auto; background: white; padding: 40px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
            .logo { font-size: 2rem; font-weight: bold; color: #6366f1; margin-bottom: 20px; }
            .error { color: #dc3545; margin: 20px 0; }
            .info { color: #6c757d; margin: 20px 0; }
        </style>
    </head>
    <body>
        <div class="container">
            <div class="logo">InvexQR</div>
            <h2>Configuration du serveur en cours</h2>
            <div class="error">Erreur: <?php echo htmlspecialchars($e->getMessage()); ?></div>
            <div class="info">
                <p>Le serveur est en cours de configuration. Veuillez :</p>
                <ol style="text-align: left;">
                    <li>Vérifier que tous les fichiers sont correctement téléchargés</li>
                    <li>Configurer les variables d'environnement de la base de données</li>
                    <li>Exécuter le script de configuration : setup-production.php</li>
                    <li>Vérifier les permissions des fichiers</li>
                </ol>
            </div>
            <p><strong>Support:</strong> contact@invexqr.com</p>
        </div>
    </body>
    </html>
    <?php
    exit;
}
