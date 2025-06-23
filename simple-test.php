<?php
// Simple test to verify PHP application
session_start();
require_once 'config/bootstrap.php';

echo "<!DOCTYPE html><html><head><title>QR Generator PHP Test</title>";
echo "<script src='https://cdn.tailwindcss.com'></script></head>";
echo "<body class='bg-gray-100 p-8'>";

echo "<div class='max-w-4xl mx-auto'>";
echo "<h1 class='text-3xl font-bold mb-6'>QR Generator PHP - Test Complet</h1>";

// Test database connection
echo "<div class='bg-white p-6 rounded-lg shadow-md mb-6'>";
echo "<h2 class='text-xl font-bold mb-4'>État de la Base de Données</h2>";
try {
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM users");
    $result = $stmt->fetch();
    echo "<p class='text-green-600'>✅ Connexion réussie - {$result['count']} utilisateurs</p>";
    
    // Show users
    $stmt = $pdo->query("SELECT email, role FROM users ORDER BY role DESC");
    $users = $stmt->fetchAll();
    echo "<div class='mt-4'>";
    echo "<h3 class='font-semibold mb-2'>Utilisateurs créés :</h3>";
    foreach ($users as $user) {
        $badge = $user['role'] === 'admin' ? 'bg-red-100 text-red-800' : 'bg-blue-100 text-blue-800';
        echo "<span class='inline-block px-3 py-1 rounded-full text-sm $badge mr-2 mb-2'>";
        echo "{$user['email']} ({$user['role']})";
        echo "</span>";
    }
    echo "</div>";
} catch (Exception $e) {
    echo "<p class='text-red-600'>❌ Erreur: " . $e->getMessage() . "</p>";
}
echo "</div>";

// Authentication test
echo "<div class='bg-white p-6 rounded-lg shadow-md mb-6'>";
echo "<h2 class='text-xl font-bold mb-4'>Test d'Authentification</h2>";

if (isset($_POST['login'])) {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();
    
    if ($user && password_verify($password, $user['password_hash'])) {
        $_SESSION['user'] = $user;
        echo "<p class='text-green-600'>✅ Connexion réussie pour {$user['email']}</p>";
        echo "<p class='text-sm text-gray-600'>Rôle: {$user['role']}</p>";
    } else {
        echo "<p class='text-red-600'>❌ Échec de la connexion</p>";
    }
}

if (isset($_POST['logout'])) {
    session_destroy();
    session_start();
    echo "<p class='text-blue-600'>Déconnexion effectuée</p>";
}

$currentUser = $_SESSION['user'] ?? null;

if ($currentUser) {
    echo "<div class='bg-green-50 p-4 rounded mb-4'>";
    echo "<p class='font-semibold'>Connecté en tant que: {$currentUser['email']}</p>";
    echo "<p class='text-sm'>Rôle: {$currentUser['role']}</p>";
    echo "<form method='post' class='mt-2'>";
    echo "<button type='submit' name='logout' class='bg-red-500 text-white px-4 py-2 rounded'>Se déconnecter</button>";
    echo "</form>";
    echo "</div>";
    
    // Show role-specific content
    if ($currentUser['role'] === 'admin') {
        echo "<div class='bg-red-50 p-4 rounded mb-4'>";
        echo "<h3 class='font-semibold text-red-800'>Accès Administrateur</h3>";
        echo "<p class='text-sm'>Vous avez accès à l'interface d'administration</p>";
        echo "<a href='/admin' class='inline-block mt-2 bg-red-600 text-white px-4 py-2 rounded'>Interface Admin</a>";
        echo "</div>";
    } else {
        echo "<div class='bg-blue-50 p-4 rounded mb-4'>";
        echo "<h3 class='font-semibold text-blue-800'>Accès Utilisateur</h3>";
        echo "<p class='text-sm'>Vous avez accès au tableau de bord</p>";
        echo "<a href='/dashboard' class='inline-block mt-2 bg-blue-600 text-white px-4 py-2 rounded'>Tableau de Bord</a>";
        echo "</div>";
    }
} else {
    echo "<form method='post' class='space-y-4'>";
    echo "<div>";
    echo "<label class='block text-sm font-medium mb-1'>Email:</label>";
    echo "<select name='email' class='w-full border rounded px-3 py-2'>";
    echo "<option value='admin@qr-generator.com'>admin@qr-generator.com (Admin)</option>";
    echo "<option value='user@test.com'>user@test.com (Utilisateur)</option>";
    echo "</select>";
    echo "</div>";
    echo "<div>";
    echo "<label class='block text-sm font-medium mb-1'>Mot de passe:</label>";
    echo "<input type='password' name='password' placeholder='admin123 ou user123' class='w-full border rounded px-3 py-2'>";
    echo "</div>";
    echo "<button type='submit' name='login' class='bg-blue-600 text-white px-6 py-2 rounded'>Se connecter</button>";
    echo "</form>";
}
echo "</div>";

// Navigation links
echo "<div class='bg-white p-6 rounded-lg shadow-md'>";
echo "<h2 class='text-xl font-bold mb-4'>Navigation</h2>";
echo "<div class='grid md:grid-cols-3 gap-4'>";
echo "<a href='/login' class='block bg-blue-500 text-white text-center py-3 rounded'>Page de Connexion</a>";
echo "<a href='/admin' class='block bg-red-500 text-white text-center py-3 rounded'>Interface Admin</a>";
echo "<a href='/dashboard' class='block bg-green-500 text-white text-center py-3 rounded'>Tableau de Bord</a>";
echo "</div>";
echo "</div>";

echo "</div></body></html>";
?>