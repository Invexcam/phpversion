<?php

// Error reporting - Désactivé en production
error_reporting(0);
ini_set('display_errors', 0);
ini_set('log_errors', 1);

// Timezone
date_default_timezone_set('UTC');

// Include composer autoloader if exists
if (file_exists(__DIR__ . '/../vendor/autoload.php')) {
    require_once __DIR__ . '/../vendor/autoload.php';
}

// Load environment variables from system
foreach ($_SERVER as $key => $value) {
    if (strpos($key, 'PG') === 0 || strpos($key, 'VITE_') === 0) {
        $_ENV[$key] = $value;
    }
}

// Auto-load classes
spl_autoload_register(function ($class) {
    $directories = [
        __DIR__ . '/../models/',
        __DIR__ . '/../controllers/',
        __DIR__ . '/../config/',
        __DIR__ . '/../lib/',
    ];
    
    foreach ($directories as $dir) {
        $file = $dir . $class . '.php';
        if (file_exists($file)) {
            require_once $file;
            return;
        }
    }
});

// Include database
require_once __DIR__ . '/database.php';

// Initialize database connection
$db = Database::getInstance();
$pdo = $db->getConnection();

// Helper functions
function redirect($url) {
    header("Location: $url");
    exit;
}

function json_response($data, $status = 200) {
    http_response_code($status);
    header('Content-Type: application/json');
    echo json_encode($data);
    exit;
}

function view($template, $data = []) {
    extract($data);
    ob_start();
    require __DIR__ . "/../views/$template.php";
    return ob_get_clean();
}

function render($template, $data = []) {
    echo view($template, $data);
}

function csrf_token() {
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function verify_csrf() {
    $token = $_POST['csrf_token'] ?? $_GET['csrf_token'] ?? '';
    return hash_equals($_SESSION['csrf_token'] ?? '', $token);
}

function auth_user() {
    return $_SESSION['user'] ?? null;
}

function require_auth() {
    if (!auth_user()) {
        if (is_ajax_request()) {
            json_response(['message' => 'Unauthorized'], 401);
        } else {
            redirect('/login');
        }
    }
}

function require_admin() {
    $user = auth_user();
    if (!$user || $user['role'] !== 'admin') {
        if (is_ajax_request()) {
            json_response(['message' => 'Admin access required'], 403);
        } else {
            redirect('/login');
        }
    }
}

function is_ajax_request() {
    return !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && 
           strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';
}