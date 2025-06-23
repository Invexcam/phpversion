<?php
// Script de diagnostic pour identifier les problèmes de configuration
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>Diagnostic InvexQR - Serveur de Production</h1>";
echo "<style>body{font-family:Arial,sans-serif;margin:20px;} .ok{color:green;} .error{color:red;} .warning{color:orange;}</style>";

// 1. Vérification de la version PHP
echo "<h2>1. Version PHP</h2>";
echo "Version PHP: " . phpversion() . "<br>";
if (version_compare(phpversion(), '8.0.0', '>=')) {
    echo "<span class='ok'>✓ Version PHP compatible</span><br>";
} else {
    echo "<span class='error'>✗ Version PHP trop ancienne (8.0+ requis)</span><br>";
}

// 2. Vérification des extensions PHP
echo "<h2>2. Extensions PHP</h2>";
$required_extensions = ['pdo', 'pdo_pgsql', 'curl', 'gd', 'json', 'mbstring'];
foreach ($required_extensions as $ext) {
    if (extension_loaded($ext)) {
        echo "<span class='ok'>✓ Extension $ext disponible</span><br>";
    } else {
        echo "<span class='error'>✗ Extension $ext manquante</span><br>";
    }
}

// 3. Vérification des variables d'environnement
echo "<h2>3. Variables d'environnement</h2>";
$env_vars = ['PGHOST', 'PGPORT', 'PGDATABASE', 'PGUSER', 'PGPASSWORD'];
foreach ($env_vars as $var) {
    $value = getenv($var) ?: $_SERVER[$var] ?? null;
    if ($value) {
        echo "<span class='ok'>✓ $var définie</span><br>";
    } else {
        echo "<span class='error'>✗ $var manquante</span><br>";
    }
}

// 4. Test de connexion à la base de données
echo "<h2>4. Connexion base de données</h2>";
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
    
    echo "<span class='ok'>✓ Connexion base de données réussie</span><br>";
    
    // Test d'une requête simple
    $stmt = $pdo->query("SELECT version()");
    $version = $stmt->fetchColumn();
    echo "Version PostgreSQL: " . $version . "<br>";
    
} catch (Exception $e) {
    echo "<span class='error'>✗ Erreur de connexion: " . $e->getMessage() . "</span><br>";
}

// 5. Vérification des permissions de fichiers
echo "<h2>5. Permissions des fichiers</h2>";
$files_to_check = [
    'index.php',
    'config/bootstrap.php',
    'config/database.php',
    '.htaccess'
];

foreach ($files_to_check as $file) {
    if (file_exists($file)) {
        $perms = substr(sprintf('%o', fileperms($file)), -4);
        echo "Fichier $file: permissions $perms ";
        if (is_readable($file)) {
            echo "<span class='ok'>✓ Lisible</span><br>";
        } else {
            echo "<span class='error'>✗ Non lisible</span><br>";
        }
    } else {
        echo "<span class='error'>✗ Fichier $file manquant</span><br>";
    }
}

// 6. Vérification des dossiers
echo "<h2>6. Structure des dossiers</h2>";
$dirs_to_check = ['config', 'controllers', 'models', 'views', 'lib'];
foreach ($dirs_to_check as $dir) {
    if (is_dir($dir)) {
        echo "<span class='ok'>✓ Dossier $dir présent</span><br>";
    } else {
        echo "<span class='error'>✗ Dossier $dir manquant</span><br>";
    }
}

// 7. Test de l'autoloader
echo "<h2>7. Test de l'autoloader</h2>";
try {
    spl_autoload_register(function ($class) {
        $directories = [
            __DIR__ . '/models/',
            __DIR__ . '/controllers/',
            __DIR__ . '/config/',
            __DIR__ . '/lib/',
        ];
        
        foreach ($directories as $dir) {
            $file = $dir . $class . '.php';
            if (file_exists($file)) {
                require_once $file;
                return;
            }
        }
    });
    echo "<span class='ok'>✓ Autoloader configuré</span><br>";
} catch (Exception $e) {
    echo "<span class='error'>✗ Erreur autoloader: " . $e->getMessage() . "</span><br>";
}

// 8. Informations système
echo "<h2>8. Informations système</h2>";
echo "Serveur: " . $_SERVER['SERVER_SOFTWARE'] . "<br>";
echo "Document Root: " . $_SERVER['DOCUMENT_ROOT'] . "<br>";
echo "Script actuel: " . $_SERVER['SCRIPT_FILENAME'] . "<br>";
echo "Répertoire de travail: " . getcwd() . "<br>";

// 9. Test d'écriture des logs
echo "<h2>9. Test d'écriture des logs</h2>";
$log_message = "Test diagnostic - " . date('Y-m-d H:i:s');
if (error_log($log_message)) {
    echo "<span class='ok'>✓ Écriture des logs possible</span><br>";
} else {
    echo "<span class='error'>✗ Impossible d'écrire les logs</span><br>";
}

// 10. Résumé des recommandations
echo "<h2>10. Recommandations</h2>";
echo "<ul>";
echo "<li>Vérifiez que toutes les extensions PHP requises sont installées</li>";
echo "<li>Configurez correctement les variables d'environnement de la base de données</li>";
echo "<li>Assurez-vous que les permissions des fichiers sont correctes (644 pour les fichiers, 755 pour les dossiers)</li>";
echo "<li>Activez mod_rewrite sur Apache si nécessaire</li>";
echo "<li>Consultez les logs Apache pour les erreurs spécifiques</li>";
echo "</ul>";

echo "<p><strong>Après avoir corrigé les problèmes identifiés, supprimez ce fichier de diagnostic pour des raisons de sécurité.</strong></p>";
?>