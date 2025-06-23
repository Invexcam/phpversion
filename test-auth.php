<?php
require_once 'config/bootstrap.php';

// Test authentication system
echo "<h1>Test d'Authentification PHP</h1>";

// Test database connection
try {
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM users");
    $result = $stmt->fetch();
    echo "<p>✅ Base de données connectée - {$result['count']} utilisateurs</p>";
} catch (Exception $e) {
    echo "<p>❌ Erreur base de données: " . $e->getMessage() . "</p>";
}

// Test admin user
$adminEmail = 'admin@qr-generator.com';
$stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
$stmt->execute([$adminEmail]);
$admin = $stmt->fetch();

if ($admin) {
    echo "<p>✅ Utilisateur admin trouvé: {$admin['email']} (Role: {$admin['role']})</p>";
} else {
    echo "<p>❌ Utilisateur admin non trouvé</p>";
}

// Test regular user
$userEmail = 'user@test.com';
$stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
$stmt->execute([$userEmail]);
$user = $stmt->fetch();

if ($user) {
    echo "<p>✅ Utilisateur test trouvé: {$user['email']} (Role: {$user['role']})</p>";
} else {
    echo "<p>❌ Utilisateur test non trouvé</p>";
}

// Test password verification
if ($admin && password_verify('admin123', $admin['password_hash'])) {
    echo "<p>✅ Mot de passe admin vérifié</p>";
} else {
    echo "<p>❌ Erreur vérification mot de passe admin</p>";
}

echo "<hr>";
echo "<p><a href='/login'>Page de connexion</a></p>";
echo "<p><a href='/admin'>Interface admin</a></p>";
echo "<p><a href='/dashboard'>Tableau de bord</a></p>";
?>