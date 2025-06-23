<?php
/**
 * Installeur InvexQR - Version simplifiée
 * Script d'installation automatique pour hébergement PHP 8
 */

// Configuration d'affichage
error_reporting(E_ALL);
ini_set('display_errors', 1);
set_time_limit(300);

$step = $_GET['step'] ?? 'check';
$errors = [];
$success = [];

?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Installation InvexQR</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 0; padding: 20px; background: #f5f7fa; }
        .container { max-width: 800px; margin: 0 auto; background: white; border-radius: 8px; box-shadow: 0 4px 20px rgba(0,0,0,0.1); }
        .header { background: #6366f1; color: white; padding: 30px; border-radius: 8px 8px 0 0; text-align: center; }
        .content { padding: 30px; }
        .step { background: #f8f9fa; padding: 20px; margin: 15px 0; border-radius: 6px; border-left: 4px solid #6366f1; }
        .success { border-color: #28a745; background: #d4edda; color: #155724; }
        .error { border-color: #dc3545; background: #f8d7da; color: #721c24; }
        .warning { border-color: #ffc107; background: #fff3cd; color: #856404; }
        .form-group { margin: 15px 0; }
        .form-group label { display: block; margin-bottom: 5px; font-weight: bold; }
        .form-group input { width: 100%; padding: 10px; border: 2px solid #e2e8f0; border-radius: 4px; }
        .btn { background: #6366f1; color: white; padding: 12px 24px; border: none; border-radius: 4px; cursor: pointer; text-decoration: none; display: inline-block; margin: 5px; }
        .btn:hover { background: #4f46e5; }
        .btn-success { background: #28a745; }
        .nav { margin: 20px 0; text-align: center; }
        .nav a { margin: 0 10px; padding: 8px 16px; background: #e2e8f0; color: #475569; text-decoration: none; border-radius: 4px; }
        .nav a.active { background: #6366f1; color: white; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Installation InvexQR</h1>
            <p>Configuration automatique de votre générateur de QR codes</p>
        </div>
        
        <div class="nav">
            <a href="?step=check" class="<?= $step === 'check' ? 'active' : '' ?>">1. Vérification</a>
            <a href="?step=database" class="<?= $step === 'database' ? 'active' : '' ?>">2. Base de données</a>
            <a href="?step=finish" class="<?= $step === 'finish' ? 'active' : '' ?>">3. Finalisation</a>
        </div>
        
        <div class="content">
            <?php
            
            if ($step === 'check') {
                echo "<h2>Étape 1 : Vérification de l'environnement</h2>";
                
                // Vérification PHP
                $phpVersion = phpversion();
                if (version_compare($phpVersion, '8.0.0', '>=')) {
                    echo "<div class='step success'>✅ PHP $phpVersion (compatible)</div>";
                } else {
                    echo "<div class='step error'>❌ PHP $phpVersion (8.0+ requis)</div>";
                    $errors[] = "Version PHP incompatible";
                }
                
                // Extensions
                $extensions = ['pdo', 'pdo_pgsql', 'curl', 'gd', 'json', 'mbstring'];
                $missingExt = [];
                foreach ($extensions as $ext) {
                    if (extension_loaded($ext)) {
                        echo "<div class='step success'>✅ Extension $ext disponible</div>";
                    } else {
                        echo "<div class='step error'>❌ Extension $ext manquante</div>";
                        $missingExt[] = $ext;
                    }
                }
                
                // Création des dossiers
                $dirs = ['uploads', 'logs', 'cache'];
                foreach ($dirs as $dir) {
                    if (!is_dir($dir)) {
                        if (@mkdir($dir, 0755, true)) {
                            echo "<div class='step success'>✅ Dossier $dir créé</div>";
                        } else {
                            echo "<div class='step error'>❌ Impossible de créer $dir</div>";
                        }
                    } else {
                        echo "<div class='step success'>✅ Dossier $dir existe</div>";
                    }
                }
                
                // Fichiers requis
                $files = ['config/bootstrap.php', 'config/database.php', 'routes/web.php'];
                foreach ($files as $file) {
                    if (file_exists($file)) {
                        echo "<div class='step success'>✅ Fichier $file présent</div>";
                    } else {
                        echo "<div class='step error'>❌ Fichier $file manquant</div>";
                        $errors[] = "Fichier manquant: $file";
                    }
                }
                
                if (empty($errors) && empty($missingExt)) {
                    echo "<div class='step success'>";
                    echo "<h3>Environnement prêt</h3>";
                    echo "<p>Toutes les vérifications sont passées avec succès.</p>";
                    echo "<a href='?step=database' class='btn'>Étape suivante : Configuration DB</a>";
                    echo "</div>";
                } else {
                    echo "<div class='step error'>";
                    echo "<h3>Problèmes détectés</h3>";
                    echo "<p>Corrigez ces problèmes avant de continuer :</p>";
                    echo "<ul>";
                    foreach ($errors as $error) {
                        echo "<li>$error</li>";
                    }
                    foreach ($missingExt as $ext) {
                        echo "<li>Extension manquante: $ext</li>";
                    }
                    echo "</ul>";
                    echo "</div>";
                }
                
            } elseif ($step === 'database') {
                echo "<h2>Étape 2 : Configuration de la base de données</h2>";
                
                if ($_SERVER['REQUEST_METHOD'] === 'POST') {
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
                        
                        echo "<div class='step success'>✅ Connexion PostgreSQL réussie</div>";
                        
                        // Création des tables
                        $sqlCommands = [
                            "CREATE TABLE IF NOT EXISTS sessions (
                                sid VARCHAR(255) PRIMARY KEY,
                                sess JSONB NOT NULL,
                                expire TIMESTAMP NOT NULL
                            )",
                            "CREATE INDEX IF NOT EXISTS IDX_session_expire ON sessions(expire)",
                            "CREATE TABLE IF NOT EXISTS users (
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
                            )",
                            "CREATE TABLE IF NOT EXISTS qr_codes (
                                id SERIAL PRIMARY KEY,
                                user_id VARCHAR(255) NOT NULL,
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
                            )",
                            "CREATE TABLE IF NOT EXISTS qr_scans (
                                id SERIAL PRIMARY KEY,
                                qr_code_id INTEGER NOT NULL,
                                ip_address INET,
                                user_agent TEXT,
                                device_type VARCHAR(50),
                                country VARCHAR(2),
                                city VARCHAR(100),
                                scanned_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
                            )",
                            "CREATE INDEX IF NOT EXISTS idx_qr_codes_user_id ON qr_codes(user_id)",
                            "CREATE INDEX IF NOT EXISTS idx_qr_codes_short_code ON qr_codes(short_code)",
                            "CREATE INDEX IF NOT EXISTS idx_qr_scans_qr_code_id ON qr_scans(qr_code_id)"
                        ];
                        
                        foreach ($sqlCommands as $sql) {
                            $pdo->exec($sql);
                        }
                        
                        echo "<div class='step success'>✅ Tables créées avec succès</div>";
                        
                        // Sauvegarde config
                        $configContent = "<?php\n// Configuration base de données\nreturn [\n";
                        foreach ($dbConfig as $key => $value) {
                            $configContent .= "    '$key' => '" . addslashes($value) . "',\n";
                        }
                        $configContent .= "];\n";
                        
                        if (file_put_contents('config/db-config.php', $configContent)) {
                            echo "<div class='step success'>✅ Configuration sauvegardée</div>";
                        }
                        
                        echo "<div class='step success'>";
                        echo "<h3>Base de données configurée</h3>";
                        echo "<p>La structure de base de données a été créée avec succès.</p>";
                        echo "<a href='?step=finish' class='btn'>Finaliser l'installation</a>";
                        echo "</div>";
                        
                    } catch (Exception $e) {
                        echo "<div class='step error'>❌ Erreur : " . htmlspecialchars($e->getMessage()) . "</div>";
                        $showForm = true;
                    }
                } else {
                    $showForm = true;
                }
                
                if (isset($showForm)) {
                    echo "<form method='POST'>";
                    echo "<div class='form-group'>";
                    echo "<label>Hôte :</label>";
                    echo "<input type='text' name='db_host' value='localhost' required>";
                    echo "</div>";
                    
                    echo "<div class='form-group'>";
                    echo "<label>Port :</label>";
                    echo "<input type='number' name='db_port' value='5432' required>";
                    echo "</div>";
                    
                    echo "<div class='form-group'>";
                    echo "<label>Nom de la base :</label>";
                    echo "<input type='text' name='db_name' placeholder='invexqr_db' required>";
                    echo "</div>";
                    
                    echo "<div class='form-group'>";
                    echo "<label>Utilisateur :</label>";
                    echo "<input type='text' name='db_user' required>";
                    echo "</div>";
                    
                    echo "<div class='form-group'>";
                    echo "<label>Mot de passe :</label>";
                    echo "<input type='password' name='db_pass' required>";
                    echo "</div>";
                    
                    echo "<button type='submit' class='btn'>Tester et configurer</button>";
                    echo "</form>";
                }
                
            } elseif ($step === 'finish') {
                echo "<h2>Étape 3 : Installation terminée</h2>";
                
                echo "<div class='step success'>";
                echo "<h3>🎉 Félicitations !</h3>";
                echo "<p>Votre application InvexQR est maintenant installée et opérationnelle.</p>";
                echo "</div>";
                
                echo "<div class='step'>";
                echo "<h3>Fonctionnalités disponibles :</h3>";
                echo "<ul>";
                echo "<li><strong>Plan gratuit :</strong> 3 QR codes maximum par utilisateur</li>";
                echo "<li><strong>Plan premium :</strong> QR codes illimités + logos personnalisés (5€/mois)</li>";
                echo "<li><strong>Authentification :</strong> Firebase et Replit intégrés</li>";
                echo "<li><strong>Analytics :</strong> Suivi des scans en temps réel</li>";
                echo "<li><strong>API :</strong> Intégration possible avec vos systèmes</li>";
                echo "</ul>";
                echo "</div>";
                
                echo "<div class='step warning'>";
                echo "<h3>Actions de sécurité importantes :</h3>";
                echo "<ol>";
                echo "<li>Supprimez ce fichier d'installation : <code>installer-simple.php</code></li>";
                echo "<li>Vérifiez les permissions des fichiers (644 pour PHP, 755 pour dossiers)</li>";
                echo "<li>Configurez SSL/HTTPS sur votre domaine</li>";
                echo "<li>Testez toutes les fonctionnalités</li>";
                echo "</ol>";
                echo "</div>";
                
                echo "<div style='text-align: center; margin: 30px 0;'>";
                echo "<a href='/' class='btn btn-success'>Accéder à l'application</a>";
                echo "<button onclick='deleteInstaller()' class='btn' style='background: #dc3545;'>Supprimer l'installeur</button>";
                echo "</div>";
            }
            ?>
        </div>
    </div>
    
    <script>
        function deleteInstaller() {
            if (confirm('Voulez-vous supprimer le fichier d\'installation ?')) {
                fetch('?action=delete', {method: 'POST'})
                .then(() => {
                    alert('Installeur supprimé. Redirection...');
                    window.location.href = '/';
                });
            }
        }
    </script>
</body>
</html>

<?php
if (isset($_GET['action']) && $_GET['action'] === 'delete') {
    @unlink(__FILE__);
    exit('OK');
}
?>