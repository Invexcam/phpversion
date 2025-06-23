<?php

class AuthController {
    private $userModel;
    
    public function __construct() {
        $this->userModel = new User();
    }
    
    public function showLogin() {
        if (auth_user()) {
            redirect('/dashboard');
        }
        render('auth/login');
    }
    
    public function showRegister() {
        if (auth_user()) {
            redirect('/dashboard');
        }
        render('auth/register');
    }
    
    public function login() {
        if (!verify_csrf()) {
            json_response(['message' => 'Invalid CSRF token'], 400);
        }
        
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';
        
        if (empty($email) || empty($password)) {
            json_response(['message' => 'Email et mot de passe requis'], 400);
        }
        
        $user = $this->userModel->findByEmail($email);
        
        if (!$user || !$this->userModel->verifyPassword($password, $user['password_hash'])) {
            json_response(['message' => 'Identifiants invalides'], 401);
        }
        
        $_SESSION['user'] = [
            'id' => $user['id'],
            'email' => $user['email'],
            'first_name' => $user['first_name'],
            'last_name' => $user['last_name'],
            'role' => $user['role'],
            'subscription_status' => $user['subscription_status']
        ];
        
        json_response(['message' => 'Connexion réussie', 'user' => $_SESSION['user']]);
    }
    
    public function register() {
        if (!verify_csrf()) {
            json_response(['message' => 'Invalid CSRF token'], 400);
        }
        
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';
        $firstName = $_POST['first_name'] ?? '';
        $lastName = $_POST['last_name'] ?? '';
        
        if (empty($email) || empty($password)) {
            json_response(['message' => 'Email et mot de passe requis'], 400);
        }
        
        if (strlen($password) < 6) {
            json_response(['message' => 'Le mot de passe doit contenir au moins 6 caractères'], 400);
        }
        
        // Check if user exists
        if ($this->userModel->findByEmail($email)) {
            json_response(['message' => 'Un utilisateur avec cet email existe déjà'], 400);
        }
        
        try {
            $user = $this->userModel->create([
                'email' => $email,
                'password' => $password,
                'first_name' => $firstName,
                'last_name' => $lastName,
                'role' => 'user'
            ]);
            
            $_SESSION['user'] = [
                'id' => $user['id'],
                'email' => $user['email'],
                'first_name' => $user['first_name'],
                'last_name' => $user['last_name'],
                'role' => $user['role'],
                'subscription_status' => $user['subscription_status']
            ];
            
            json_response(['message' => 'Inscription réussie', 'user' => $_SESSION['user']], 201);
        } catch (Exception $e) {
            json_response(['message' => 'Erreur lors de l\'inscription'], 500);
        }
    }
    
    public function logout() {
        session_destroy();
        if (is_ajax_request()) {
            json_response(['message' => 'Déconnexion réussie']);
        } else {
            redirect('/');
        }
    }
    
    public function getCurrentUser() {
        $user = auth_user();
        if (!$user) {
            json_response(['message' => 'Non autorisé'], 401);
        }
        
        json_response(['user' => $user]);
    }
    
    public function resetPassword() {
        if (!verify_csrf()) {
            json_response(['message' => 'Invalid CSRF token'], 400);
        }
        
        $email = $_POST['email'] ?? '';
        
        if (empty($email)) {
            json_response(['message' => 'Email requis'], 400);
        }
        
        $user = $this->userModel->findByEmail($email);
        
        if (!$user) {
            // Ne pas révéler si l'email existe ou non
            json_response(['message' => 'Si un compte avec cet email existe, un lien de réinitialisation a été envoyé']);
        }
        
        // Générer un token de réinitialisation
        $resetToken = bin2hex(random_bytes(32));
        $expiry = date('Y-m-d H:i:s', strtotime('+1 hour'));
        
        $this->userModel->update($user['id'], [
            'reset_token' => $resetToken,
            'reset_token_expires' => $expiry
        ]);
        
        // Ici vous pourriez envoyer un email avec le token
        // Pour cette démo, on retourne le token (ne pas faire en production)
        
        json_response(['message' => 'Instructions de réinitialisation envoyées', 'token' => $resetToken]);
    }
}