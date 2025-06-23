<?php
// Script pour créer un utilisateur administrateur par défaut
require_once 'config/bootstrap.php';

try {
    $userModel = new User();
    
    // Vérifier si un admin existe déjà
    $existingAdmin = $userModel->findByEmail('admin@qr-generator.com');
    
    if (!$existingAdmin) {
        $adminUser = $userModel->create([
            'email' => 'admin@qr-generator.com',
            'password' => 'admin123',
            'first_name' => 'Admin',
            'last_name' => 'System',
            'role' => 'admin'
        ]);
        
        echo "✅ Utilisateur administrateur créé:\n";
        echo "   Email: admin@qr-generator.com\n";
        echo "   Mot de passe: admin123\n";
        echo "   Accès: /admin\n\n";
    } else {
        echo "ℹ️ Utilisateur administrateur existe déjà:\n";
        echo "   Email: admin@qr-generator.com\n";
        echo "   Accès: /admin\n\n";
    }
    
    // Créer un utilisateur test normal
    $testUser = $userModel->findByEmail('user@test.com');
    if (!$testUser) {
        $userModel->create([
            'email' => 'user@test.com',
            'password' => 'user123',
            'first_name' => 'Utilisateur',
            'last_name' => 'Test',
            'role' => 'user'
        ]);
        
        echo "✅ Utilisateur test créé:\n";
        echo "   Email: user@test.com\n";
        echo "   Mot de passe: user123\n";
        echo "   Accès: /dashboard\n\n";
    }
    
} catch (Exception $e) {
    echo "❌ Erreur: " . $e->getMessage() . "\n";
}