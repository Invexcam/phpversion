<?php
/**
 * Script d'installation automatique InvexQR
 * À exécuter une seule fois après téléchargement des fichiers
 */

// Configuration d'affichage
error_reporting(E_ALL);
ini_set('display_errors', 1);
set_time_limit(300); // 5 minutes max

?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Installation InvexQR</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { 
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px;
        }
        .container {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            border-radius: 12px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        .header {
            background: #6366f1;
            color: white;
            padding: 30px;
            text-align: center;
        }
        .header h1 { font-size: 2rem; margin-bottom: 10px; }
        .content { padding: 30px; }
        .step {
            margin: 20px 0;
            padding: 20px;
            border-radius: 8px;
            border-left: 4px solid #6366f1;
        }
        .step h3 { color: #6366f1; margin-bottom: 10px; }
        .success { background: #d4edda; border-color: #28a745; }
        .success h3 { color: #28a745; }
        .error { background: #f8d7da; border-color: #dc3545; }
        .error h3 { color: #dc3545; }
        .warning { background: #fff3cd; border-color: #ffc107; }
        .warning h3 { color: #856404; }
        .form-group { margin: 15px 0; }
        .form-group label { display: block; margin-bottom: 5px; font-weight: bold; }
        .form-group input, .form-group select {
            width: 100%;
            padding: 10px;
            border: 2px solid #e2e8f0;
            border-radius: 6px;
            font-size: 14px;
        }
        .btn {
            background: #6366f1;
            color: white;
            padding: 12px 24px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 16px;
            margin: 10px 5px;
        }
        .btn:hover { background: #4f46e5; }
        .btn-success { background: #28a745; }
        .btn-danger { background: #dc3545; }
        .progress {
            background: #e2e8f0;
            border-radius: 4px;
            height: 8px;
            margin: 10px 0;
        }
        .progress-bar {
            background: #6366f1;
            height: 100%;
            border-radius: 4px;
            transition: width 0.3s;
        }
        pre {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 6px;
            overflow-x: auto;
            font-size: 12px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🚀 Installation InvexQR</h1>
            <p>Configuration automatique de votre générateur de QR codes</p>
        </div>
        <div class="content">
            <?php
            
            // Variables de progression
            $steps = [];
            $errors = [];
            $warnings = [];
            $config = [];
            
            // Fonction d'affichage des étapes
            if (!function_exists('displayStep')) {
                function displayStep($title, $content, $type = 'info') {
                    $class = $type === 'success' ? 'success' : ($type === 'error' ? 'error' : ($type === 'warning' ? 'warning' : ''));
                    echo "<div class='step $class'>";
                    echo "<h3>$title</h3>";
                    echo "<div>$content</div>";
                    echo "</div>";
                }
            }
            
            // 1. VÉRIFICATION DE L'ENVIRONNEMENT
            displayStep("📋 Étape 1 : Vérification de l'environnement", "Contrôle de la configuration serveur...");
            
            // Version PHP
            $phpVersion = phpversion();
            if (version_compare($phpVersion, '8.0.0', '>=')) {
                $steps[] = "✅ PHP $phpVersion (compatible)";
            } else {
                $errors[] = "❌ PHP $phpVersion (8.0+ requis)";
            }
            
            // Extensions PHP requises
            $requiredExtensions = ['pdo', 'pdo_pgsql', 'curl', 'gd', 'json', 'mbstring'];
            foreach ($requiredExtensions as $ext) {
                if (extension_loaded($ext)) {
                    $steps[] = "✅ Extension $ext disponible";
                } else {
                    $errors[] = "❌ Extension $ext manquante";
                }
            }
            
            // Permissions d'écriture
            $writableDirs = ['uploads', 'logs', 'cache'];
            foreach ($writableDirs as $dir) {
                if (!is_dir($dir)) {
                    if (@mkdir($dir, 0755, true)) {
                        $steps[] = "✅ Dossier $dir créé";
                    } else {
                        $errors[] = "❌ Impossible de créer le dossier $dir";
                    }
                } else {
                    $steps[] = "✅ Dossier $dir existe";
                }
                
                if (is_writable($dir)) {
                    $steps[] = "✅ Dossier $dir accessible en écriture";
                } else {
                    $warnings[] = "⚠️ Permissions du dossier $dir à vérifier";
                }
            }
            
            // 2. CONFIGURATION DE LA BASE DE DONNÉES
            if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['step']) && $_POST['step'] === 'database') {
                
                displayStep("🗄️ Étape 2 : Configuration de la base de données", "Test de connexion en cours...");
                
                $dbConfig = [
                    'host' => $_POST['db_host'] ?? 'localhost',
                    'port' => $_POST['db_port'] ?? '5432',
                    'database' => $_POST['db_name'] ?? '',
                    'username' => $_POST['db_user'] ?? '',
                    'password' => $_POST['db_pass'] ?? ''
                ];
                
                try {
                    $dsn = "pgsql:host={$dbConfig['host']};port={$dbConfig['port']};dbname={$dbConfig['database']}";
                    $pdo = new PDO($dsn, $dbConfig['username'], $dbConfig['password'], [
                        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    ]);
                    
                    displayStep("✅ Connexion base de données", "Connexion PostgreSQL réussie", 'success');
                    
                    // Création des tables
                    $sqlTables = file_get_contents('schema.sql');
                    if (!$sqlTables) {
                        $sqlTables = "
                        -- Tables InvexQR
                        CREATE TABLE IF NOT EXISTS sessions (
                            sid VARCHAR(255) PRIMARY KEY,
                            sess JSONB NOT NULL,
                            expire TIMESTAMP NOT NULL
                        );
                        
                        CREATE TABLE IF NOT EXISTS users (
                            id VARCHAR(255) PRIMARY KEY,
                            email VARCHAR(255) UNIQUE,
                            first_name VARCHAR(255),
                            last_name VARCHAR(255),
                            profile_image_url VARCHAR(500),
                            subscription_id VARCHAR(255),
                            subscription_status VARCHAR(50) DEFAULT 'inactive',
                            subscription_plan_id VARCHAR(255),
                            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
                        );
                        
                        CREATE TABLE IF NOT EXISTS qr_codes (
                            id SERIAL PRIMARY KEY,
                            user_id VARCHAR(255) NOT NULL REFERENCES users(id),
                            name VARCHAR(255) NOT NULL,
                            original_url TEXT NOT NULL,
                            short_code VARCHAR(20) UNIQUE NOT NULL,
                            type VARCHAR(50) NOT NULL,
                            description TEXT,
                            content JSONB,
                            style JSONB,
                            content_type VARCHAR(50),
                            customization JSONB,
                            is_active BOOLEAN DEFAULT true,
                            is_public BOOLEAN DEFAULT false,
                            likes INTEGER DEFAULT 0,
                            views INTEGER DEFAULT 0,
                            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
                        );
                        
                        CREATE TABLE IF NOT EXISTS qr_scans (
                            id SERIAL PRIMARY KEY,
                            qr_code_id INTEGER NOT NULL REFERENCES qr_codes(id),
                            ip_address INET,
                            user_agent TEXT,
                            device_type VARCHAR(50),
                            country VARCHAR(2),
                            city VARCHAR(100),
                            scanned_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
                        );
                        
                        CREATE INDEX IF NOT EXISTS idx_qr_codes_user_id ON qr_codes(user_id);
                        CREATE INDEX IF NOT EXISTS idx_qr_codes_short_code ON qr_codes(short_code);
                        ";
                    }
                    
                    $pdo->exec($sqlTables);
                    displayStep("✅ Tables créées", "Structure de base de données initialisée", 'success');
                    
                    // Sauvegarde de la configuration
                    $configContent = "<?php\nreturn " . var_export($dbConfig, true) . ";\n";
                    file_put_contents('config/database-config.php', $configContent);
                    
                    displayStep("🎉 Installation terminée", "
                        <p>Votre application InvexQR est maintenant installée et configurée !</p>
                        <p><strong>Actions suivantes :</strong></p>
                        <ul>
                            <li>Supprimez ce fichier install.php pour sécuriser votre installation</li>
                            <li>Accédez à votre application : <a href='/'>Page d'accueil</a></li>
                            <li>Créez votre premier compte utilisateur</li>
                        </ul>
                        <a href='/' class='btn btn-success'>Accéder à l'application</a>
                        <button onclick='deleteInstaller()' class='btn btn-danger'>Supprimer l'installeur</button>
                    ", 'success');
                    
                } catch (Exception $e) {
                    displayStep("❌ Erreur de base de données", "Erreur : " . $e->getMessage(), 'error');
                    $showDbForm = true;
                }
                
            } else {
                $showDbForm = true;
            }
            
            // Affichage du formulaire de configuration DB
            if (isset($showDbForm) && $showDbForm) {
                ?>
                <div class="step">
                    <h3>🗄️ Configuration de la base de données</h3>
                    <p>Entrez les informations de connexion à votre base PostgreSQL :</p>
                    
                    <form method="POST">
                        <input type="hidden" name="step" value="database">
                        
                        <div class="form-group">
                            <label>Hôte de la base de données :</label>
                            <input type="text" name="db_host" value="localhost" required>
                        </div>
                        
                        <div class="form-group">
                            <label>Port :</label>
                            <input type="number" name="db_port" value="5432" required>
                        </div>
                        
                        <div class="form-group">
                            <label>Nom de la base de données :</label>
                            <input type="text" name="db_name" placeholder="invexqr_db" required>
                        </div>
                        
                        <div class="form-group">
                            <label>Utilisateur :</label>
                            <input type="text" name="db_user" required>
                        </div>
                        
                        <div class="form-group">
                            <label>Mot de passe :</label>
                            <input type="password" name="db_pass" required>
                        </div>
                        
                        <button type="submit" class="btn">Configurer la base de données</button>
                    </form>
                </div>
                <?php
            }
            
            // Affichage du résumé des vérifications
            if (!empty($steps) || !empty($errors) || !empty($warnings)) {
                echo "<div class='step'>";
                echo "<h3>📊 Résumé des vérifications</h3>";
                
                if (!empty($steps)) {
                    echo "<h4>Succès :</h4><ul>";
                    foreach ($steps as $step) {
                        echo "<li>$step</li>";
                    }
                    echo "</ul>";
                }
                
                if (!empty($warnings)) {
                    echo "<h4>Avertissements :</h4><ul>";
                    foreach ($warnings as $warning) {
                        echo "<li>$warning</li>";
                    }
                    echo "</ul>";
                }
                
                if (!empty($errors)) {
                    echo "<h4>Erreurs à corriger :</h4><ul>";
                    foreach ($errors as $error) {
                        echo "<li>$error</li>";
                    }
                    echo "</ul>";
                }
                
                echo "</div>";
            }
            ?>
        </div>
    </div>
    
    <script>
        function deleteInstaller() {
            if (confirm('Êtes-vous sûr de vouloir supprimer le fichier d\'installation ?')) {
                fetch('', {
                    method: 'POST',
                    headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                    body: 'action=delete_installer'
                }).then(() => {
                    alert('Fichier d\'installation supprimé. Redirection vers l\'application...');
                    window.location.href = '/';
                });
            }
        }
    </script>
</body>
</html>

<?php
// Gestion de la suppression de l'installeur
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete_installer') {
    @unlink(__FILE__);
    exit('OK');
}
?>