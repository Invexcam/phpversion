<?php
// Test simple de connectivité serveur
header('Content-Type: text/html; charset=UTF-8');
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test Serveur - InvexQR</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 40px; background: #f5f5f5; }
        .container { background: white; padding: 30px; border-radius: 8px; max-width: 800px; margin: 0 auto; }
        .status-ok { color: #27ae60; font-weight: bold; }
        .status-error { color: #e74c3c; font-weight: bold; }
        .info-box { background: #ecf0f1; padding: 15px; margin: 10px 0; border-radius: 5px; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Test Serveur InvexQR</h1>
        
        <div class="info-box">
            <h3>Statut du serveur</h3>
            <p class="status-ok">✓ Serveur PHP opérationnel</p>
            <p>Version PHP: <?php echo phpversion(); ?></p>
            <p>Serveur: <?php echo $_SERVER['SERVER_SOFTWARE'] ?? 'Inconnu'; ?></p>
            <p>Document Root: <?php echo $_SERVER['DOCUMENT_ROOT'] ?? 'Inconnu'; ?></p>
            <p>Heure serveur: <?php echo date('Y-m-d H:i:s'); ?></p>
        </div>

        <div class="info-box">
            <h3>Extensions PHP</h3>
            <?php
            $extensions = ['pdo', 'pdo_pgsql', 'curl', 'gd', 'json'];
            foreach ($extensions as $ext) {
                if (extension_loaded($ext)) {
                    echo "<p class='status-ok'>✓ Extension $ext disponible</p>";
                } else {
                    echo "<p class='status-error'>✗ Extension $ext manquante</p>";
                }
            }
            ?>
        </div>

        <div class="info-box">
            <h3>Variables d'environnement</h3>
            <?php
            $env_vars = ['PGHOST', 'PGDATABASE', 'PGUSER'];
            foreach ($env_vars as $var) {
                $value = $_SERVER[$var] ?? $_ENV[$var] ?? getenv($var);
                if ($value) {
                    echo "<p class='status-ok'>✓ $var configurée</p>";
                } else {
                    echo "<p class='status-error'>✗ $var manquante</p>";
                }
            }
            ?>
        </div>

        <div class="info-box">
            <h3>Structure des fichiers</h3>
            <?php
            $files = ['index.php', 'config/bootstrap.php', 'config/database.php', '.htaccess'];
            foreach ($files as $file) {
                if (file_exists($file)) {
                    echo "<p class='status-ok'>✓ Fichier $file présent</p>";
                } else {
                    echo "<p class='status-error'>✗ Fichier $file manquant</p>";
                }
            }
            ?>
        </div>

        <div class="info-box">
            <h3>Actions recommandées</h3>
            <ol>
                <li>Vérifiez que tous les fichiers PHP sont présents dans le bon répertoire</li>
                <li>Configurez les variables d'environnement de la base de données</li>
                <li>Testez les permissions des fichiers (644 pour PHP, 755 pour dossiers)</li>
                <li>Vérifiez que mod_rewrite est activé sur Apache</li>
            </ol>
        </div>

        <p><strong>Une fois les problèmes corrigés, supprimez ce fichier de test.</strong></p>
    </div>
</body>
</html>