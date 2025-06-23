<?php

$routes = [];

function route($path, $method) {
    global $routes;
    
    // Define routes
    $routes = [
        // Public routes
        'GET:/' => ['PublicController', 'home'],
        'GET:/login' => ['AuthController', 'showLogin'],
        'GET:/register' => ['AuthController', 'showRegister'],
        'POST:/api/auth/login' => ['AuthController', 'login'],
        'POST:/api/auth/register' => ['AuthController', 'register'],
        'POST:/api/auth/logout' => ['AuthController', 'logout'],
        'POST:/api/auth/reset-password' => ['AuthController', 'resetPassword'],
        'GET:/api/auth/user' => ['AuthController', 'getCurrentUser'],
        
        // QR Code public routes
        'GET:/s/{shortCode}' => ['QRController', 'redirectShortCode'],
        'GET:/api/public/stats' => ['PublicController', 'getPublicStats'],
        
        // Dashboard routes (protected)
        'GET:/dashboard' => ['DashboardController', 'index'],
        'GET:/dashboard/qr-codes' => ['DashboardController', 'qrCodes'],
        'GET:/dashboard/analytics' => ['DashboardController', 'analytics'],
        'GET:/api/dashboard/analytics' => ['DashboardController', 'getAnalyticsData'],
        
        // QR Code API routes (protected)
        'GET:/api/qr-codes' => ['QRController', 'getUserQRCodes'],
        'POST:/api/qr-codes' => ['QRController', 'createQRCode'],
        'GET:/api/qr-codes/{id}' => ['QRController', 'getQRCode'],
        'PUT:/api/qr-codes/{id}' => ['QRController', 'updateQRCode'],
        'DELETE:/api/qr-codes/{id}' => ['QRController', 'deleteQRCode'],
        'GET:/api/qr-codes/{id}/analytics' => ['QRController', 'getQRCodeAnalytics'],
        'GET:/api/qr-codes/{id}/image' => ['QRController', 'generateQRImage'],
        
        // Admin routes (admin only)
        'GET:/admin' => ['AdminController', 'dashboard'],
        'GET:/admin/users' => ['AdminController', 'users'],
        'GET:/admin/qr-codes' => ['AdminController', 'qrCodes'],
        'GET:/admin/scans' => ['AdminController', 'scans'],
        'GET:/admin/users/create' => ['AdminController', 'createUser'],
        'POST:/admin/users/create' => ['AdminController', 'createUser'],
        'GET:/admin/users/{id}/edit' => ['AdminController', 'editUser'],
        'POST:/admin/users/{id}/edit' => ['AdminController', 'editUser'],
        'DELETE:/admin/users/{id}' => ['AdminController', 'deleteUser'],
        'DELETE:/admin/qr-codes/{id}' => ['AdminController', 'deleteQRCode'],
        'GET:/admin/stats' => ['AdminController', 'getStats'],
        'GET:/admin/system' => ['AdminController', 'systemInfo'],
        'GET:/admin/settings' => ['AdminController', 'settings'],
        'POST:/admin/settings' => ['AdminController', 'settings'],
    ];
    
    // Parse path parameters
    $matchedRoute = null;
    $params = [];
    
    foreach ($routes as $routePattern => $handler) {
        list($routeMethod, $routePath) = explode(':', $routePattern, 2);
        
        if ($routeMethod !== $method) {
            continue;
        }
        
        // Convert route pattern to regex
        $pattern = preg_replace('/\{([^}]+)\}/', '([^/]+)', $routePath);
        $pattern = '#^' . $pattern . '$#';
        
        if (preg_match($pattern, $path, $matches)) {
            $matchedRoute = $handler;
            
            // Extract parameter names
            preg_match_all('/\{([^}]+)\}/', $routePath, $paramNames);
            
            // Map parameter values
            for ($i = 1; $i < count($matches); $i++) {
                $paramName = $paramNames[1][$i - 1];
                $params[$paramName] = $matches[$i];
            }
            break;
        }
    }
    
    if (!$matchedRoute) {
        http_response_code(404);
        echo "Page non trouvée";
        return;
    }
    
    list($controllerName, $actionName) = $matchedRoute;
    
    try {
        $controller = new $controllerName();
        
        // Call controller method with parameters
        if (!empty($params)) {
            call_user_func_array([$controller, $actionName], array_values($params));
        } else {
            $controller->$actionName();
        }
    } catch (Exception $e) {
        error_log("Router error: " . $e->getMessage());
        http_response_code(500);
        
        if (is_ajax_request()) {
            json_response(['message' => 'Erreur interne du serveur'], 500);
        } else {
            echo "Erreur interne du serveur";
        }
    }
}