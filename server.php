<?php
// PHP Built-in Server Router
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Start session
session_start();

$uri = urldecode(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));

// Serve static files
if ($uri !== '/' && file_exists(__DIR__ . '/public' . $uri)) {
    return false;
}

// Route handling
require_once 'start-server.php';