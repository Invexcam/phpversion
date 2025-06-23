<?php
// Script de configuration automatique pour la production
// À exécuter une seule fois après déploiement

echo "<h1>Configuration automatique InvexQR</h1>";
echo "<style>body{font-family:Arial,sans-serif;margin:20px;} .ok{color:green;} .error{color:red;} .info{color:blue;}</style>";

// Désactiver l'affichage des erreurs en production
ini_set('display_errors', 0);
error_reporting(0);

$errors = [];
$success = [];

// 1. Création des tables de base de données
echo "<h2>1. Configuration de la base de données</h2>";
try {
    $host = getenv('PGHOST') ?: $_SERVER['PGHOST'] ?? 'localhost';
    $port = getenv('PGPORT') ?: $_SERVER['PGPORT'] ?? '5432';
    $dbname = getenv('PGDATABASE') ?: $_SERVER['PGDATABASE'] ?? 'qr_generator';
    $username = getenv('PGUSER') ?: $_SERVER['PGUSER'] ?? 'postgres';
    $password = getenv('PGPASSWORD') ?: $_SERVER['PGPASSWORD'] ?? '';
    
    $dsn = "pgsql:host=$host;port=$port;dbname=$dbname";
    $pdo = new PDO($dsn, $username, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
    
    // Création des tables si elles n'existent pas
    $sql_tables = "
    -- Table des sessions
    CREATE TABLE IF NOT EXISTS sessions (
        sid VARCHAR(255) PRIMARY KEY,
        sess JSONB NOT NULL,
        expire TIMESTAMP NOT NULL
    );
    CREATE INDEX IF NOT EXISTS IDX_session_expire ON sessions(expire);

    -- Table des utilisateurs
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

    -- Table des QR codes
    CREATE TABLE IF NOT EXISTS qr_codes (
        id SERIAL PRIMARY KEY,
        user_id VARCHAR(255) NOT NULL REFERENCES users(id) ON DELETE CASCADE,
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

    -- Table des scans
    CREATE TABLE IF NOT EXISTS qr_scans (
        id SERIAL PRIMARY KEY,
        qr_code_id INTEGER NOT NULL REFERENCES qr_codes(id) ON DELETE CASCADE,
        ip_address INET,
        user_agent TEXT,
        referer TEXT,
        device_type VARCHAR(50),
        country VARCHAR(2),
        city VARCHAR(100),
        scanned_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    );

    -- Indexes pour performance
    CREATE INDEX IF NOT EXISTS idx_qr_codes_user_id ON qr_codes(user_id);
    CREATE INDEX IF NOT EXISTS idx_qr_codes_short_code ON qr_codes(short_code);
    CREATE INDEX IF NOT EXISTS idx_qr_scans_qr_code_id ON qr_scans(qr_code_id);
    CREATE INDEX IF NOT EXISTS idx_qr_scans_scanned_at ON qr_scans(scanned_at);
    ";
    
    $pdo->exec($sql_tables);
    $success[] = "Tables de base de données créées avec succès";
    
} catch (Exception $e) {
    $errors[] = "Erreur base de données: " . $e->getMessage();
}

// 2. Vérification et création des dossiers nécessaires
echo "<h2>2. Création des dossiers</h2>";
$directories = [
    'uploads',
    'uploads/logos',
    'uploads/qr-codes',
    'logs',
    'cache'
];

foreach ($directories as $dir) {
    if (!is_dir($dir)) {
        if (mkdir($dir, 0755, true)) {
            $success[] = "Dossier $dir créé";
        } else {
            $errors[] = "Impossible de créer le dossier $dir";
        }
    } else {
        $success[] = "Dossier $dir existe déjà";
    }
}

// 3. Configuration des permissions
echo "<h2>3. Configuration des permissions</h2>";
$files_to_chmod = [
    'uploads' => 0755,
    'logs' => 0755,
    'cache' => 0755,
    'index.php' => 0644,
    '.htaccess' => 0644
];

foreach ($files_to_chmod as $file => $permission) {
    if (file_exists($file)) {
        if (chmod($file, $permission)) {
            $success[] = "Permissions $file mises à jour";
        } else {
            $errors[] = "Impossible de modifier les permissions de $file";
        }
    }
}

// 4. Création du fichier de configuration de production
echo "<h2>4. Configuration de production</h2>";
$config_content = "<?php
// Configuration spécifique à la production
return [
    'environment' => 'production',
    'debug' => false,
    'log_level' => 'error',
    'cache_enabled' => true,
    'session_lifetime' => 7 * 24 * 60 * 60, // 7 jours
    'max_qr_codes_free' => 3,
    'max_upload_size' => 5 * 1024 * 1024, // 5MB
    'allowed_file_types' => ['png', 'jpg', 'jpeg', 'svg'],
    'paypal' => [
        'environment' => 'live', // 'sandbox' pour les tests
        'plan_id' => 'P-4F775898EU1340713NBLITJI'
    ]
];
";

if (file_put_contents('config/production.php', $config_content)) {
    $success[] = "Fichier de configuration de production créé";
} else {
    $errors[] = "Impossible de créer le fichier de configuration";
}

// 5. Test de la configuration finale
echo "<h2>5. Tests finaux</h2>";
try {
    // Test de l'autoloader
    require_once 'config/bootstrap.php';
    $success[] = "Bootstrap chargé avec succès";
    
    // Test de connexion DB
    if (isset($pdo)) {
        $stmt = $pdo->query("SELECT COUNT(*) FROM users");
        $user_count = $stmt->fetchColumn();
        $success[] = "Base de données opérationnelle ($user_count utilisateurs)";
    }
    
} catch (Exception $e) {
    $errors[] = "Erreur lors des tests: " . $e->getMessage();
}

// Affichage du résumé
echo "<h2>Résumé de la configuration</h2>";
if (!empty($success)) {
    echo "<h3 class='ok'>Succès:</h3><ul>";
    foreach ($success as $msg) {
        echo "<li class='ok'>✓ $msg</li>";
    }
    echo "</ul>";
}

if (!empty($errors)) {
    echo "<h3 class='error'>Erreurs:</h3><ul>";
    foreach ($errors as $msg) {
        echo "<li class='error'>✗ $msg</li>";
    }
    echo "</ul>";
}

if (empty($errors)) {
    echo "<div class='ok'>";
    echo "<h3>Configuration terminée avec succès!</h3>";
    echo "<p>Votre application InvexQR est maintenant configurée pour la production.</p>";
    echo "<p><strong>Important:</strong> Supprimez ce fichier setup-production.php pour des raisons de sécurité.</p>";
    echo "<p><a href='/'>Accéder à l'application</a></p>";
    echo "</div>";
} else {
    echo "<div class='error'>";
    echo "<h3>Configuration incomplète</h3>";
    echo "<p>Corrigez les erreurs ci-dessus avant de continuer.</p>";
    echo "</div>";
}
?>