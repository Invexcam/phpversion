<?php

class AdminController {
    private $userModel;
    private $qrModel;
    private $scanModel;
    
    public function __construct() {
        $this->userModel = new User();
        $this->qrModel = new QRCode();
        $this->scanModel = new QRScan();
    }
    
    public function dashboard() {
        require_admin();
        
        $stats = [
            'total_users' => $this->userModel->getUserCount(),
            'total_qr_codes' => $this->qrModel->getQRCodeCount(),
            'total_scans' => 0, // Placeholder for now
            'recent_users' => $this->userModel->getAllUsers(5),
            'recent_qr_codes' => [],
            'recent_scans' => []
        ];
        
        render('admin/dashboard', ['stats' => $stats]);
    }
    
    public function users() {
        require_admin();
        
        $page = (int)($_GET['page'] ?? 1);
        $limit = 20;
        $offset = ($page - 1) * $limit;
        
        $users = $this->userModel->getAllUsers($limit, $offset);
        $totalUsers = $this->userModel->getUserCount();
        $totalPages = ceil($totalUsers / $limit);
        
        render('admin/users', [
            'users' => $users,
            'currentPage' => $page,
            'totalPages' => $totalPages,
            'totalUsers' => $totalUsers
        ]);
    }
    
    public function qrCodes() {
        require_admin();
        
        $page = (int)($_GET['page'] ?? 1);
        $limit = 20;
        $offset = ($page - 1) * $limit;
        
        $qrCodes = $this->qrModel->getAllQRCodes($limit, $offset);
        $totalQRCodes = $this->qrModel->getQRCodeCount();
        $totalPages = ceil($totalQRCodes / $limit);
        
        render('admin/qr-codes', [
            'qrCodes' => $qrCodes,
            'currentPage' => $page,
            'totalPages' => $totalPages,
            'totalQRCodes' => $totalQRCodes
        ]);
    }
    
    public function scans() {
        require_admin();
        
        $page = (int)($_GET['page'] ?? 1);
        $limit = 20;
        $offset = ($page - 1) * $limit;
        
        $scans = $this->scanModel->getAllScans($limit, $offset);
        $totalScans = $this->scanModel->getTotalScanCount();
        $totalPages = ceil($totalScans / $limit);
        
        render('admin/scans', [
            'scans' => $scans,
            'currentPage' => $page,
            'totalPages' => $totalPages,
            'totalScans' => $totalScans
        ]);
    }
    
    public function createUser() {
        require_admin();
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!verify_csrf()) {
                json_response(['message' => 'Invalid CSRF token'], 400);
            }
            
            $data = [
                'email' => $_POST['email'] ?? '',
                'password' => $_POST['password'] ?? '',
                'first_name' => $_POST['first_name'] ?? '',
                'last_name' => $_POST['last_name'] ?? '',
                'role' => $_POST['role'] ?? 'user'
            ];
            
            if (empty($data['email']) || empty($data['password'])) {
                json_response(['message' => 'Email et mot de passe requis'], 400);
            }
            
            if ($this->userModel->findByEmail($data['email'])) {
                json_response(['message' => 'Un utilisateur avec cet email existe déjà'], 400);
            }
            
            try {
                $user = $this->userModel->create($data);
                json_response(['message' => 'Utilisateur créé avec succès', 'user' => $user], 201);
            } catch (Exception $e) {
                json_response(['message' => 'Erreur lors de la création'], 500);
            }
        } else {
            render('admin/create-user');
        }
    }
    
    public function editUser($id) {
        require_admin();
        
        $user = $this->userModel->findById($id);
        if (!$user) {
            json_response(['message' => 'Utilisateur non trouvé'], 404);
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!verify_csrf()) {
                json_response(['message' => 'Invalid CSRF token'], 400);
            }
            
            $data = [];
            if (!empty($_POST['email'])) $data['email'] = $_POST['email'];
            if (!empty($_POST['first_name'])) $data['first_name'] = $_POST['first_name'];
            if (!empty($_POST['last_name'])) $data['last_name'] = $_POST['last_name'];
            if (!empty($_POST['role'])) $data['role'] = $_POST['role'];
            if (!empty($_POST['password'])) $data['password_hash'] = password_hash($_POST['password'], PASSWORD_DEFAULT);
            
            try {
                $updatedUser = $this->userModel->update($id, $data);
                json_response(['message' => 'Utilisateur mis à jour', 'user' => $updatedUser]);
            } catch (Exception $e) {
                json_response(['message' => 'Erreur lors de la mise à jour'], 500);
            }
        } else {
            render('admin/edit-user', ['user' => $user]);
        }
    }
    
    public function deleteUser($id) {
        require_admin();
        
        if (!verify_csrf()) {
            json_response(['message' => 'Invalid CSRF token'], 400);
        }
        
        $user = $this->userModel->findById($id);
        if (!$user) {
            json_response(['message' => 'Utilisateur non trouvé'], 404);
        }
        
        // Ne pas supprimer le dernier admin
        if ($user['role'] === 'admin') {
            $adminCount = $this->userModel->getUserCount('admin');
            if ($adminCount <= 1) {
                json_response(['message' => 'Impossible de supprimer le dernier administrateur'], 400);
            }
        }
        
        try {
            $this->userModel->delete($id);
            json_response(['message' => 'Utilisateur supprimé avec succès']);
        } catch (Exception $e) {
            json_response(['message' => 'Erreur lors de la suppression'], 500);
        }
    }
    
    public function deleteQRCode($id) {
        require_admin();
        
        if (!verify_csrf()) {
            json_response(['message' => 'Invalid CSRF token'], 400);
        }
        
        $qrCode = $this->qrModel->findById($id);
        if (!$qrCode) {
            json_response(['message' => 'QR code non trouvé'], 404);
        }
        
        try {
            $this->qrModel->delete($id);
            json_response(['message' => 'QR code supprimé avec succès']);
        } catch (Exception $e) {
            json_response(['message' => 'Erreur lors de la suppression'], 500);
        }
    }
    
    public function getStats() {
        require_admin();
        
        $stats = [
            'users' => [
                'total' => $this->userModel->getUserCount(),
                'active' => $this->userModel->getUserCount('active'),
                'premium' => $this->userModel->getUserCount('premium')
            ],
            'qr_codes' => [
                'total' => $this->qrModel->getQRCodeCount(),
                'today' => $this->qrModel->getQRCodeCount('today'),
                'this_week' => $this->qrModel->getQRCodeCount('week')
            ],
            'scans' => [
                'total' => $this->scanModel->getTotalScanCount(),
                'today' => $this->scanModel->getTotalScanCount('today'),
                'this_week' => $this->scanModel->getTotalScanCount('week')
            ]
        ];
        
        json_response($stats);
    }
    
    public function systemInfo() {
        require_admin();
        
        $info = [
            'php_version' => PHP_VERSION,
            'server' => $_SERVER['SERVER_SOFTWARE'] ?? 'Unknown',
            'memory_usage' => memory_get_usage(true),
            'memory_limit' => ini_get('memory_limit'),
            'upload_max_filesize' => ini_get('upload_max_filesize'),
            'post_max_size' => ini_get('post_max_size'),
            'max_execution_time' => ini_get('max_execution_time'),
            'database_connection' => $this->testDatabaseConnection()
        ];
        
        render('admin/system-info', ['info' => $info]);
    }
    
    public function settings() {
        require_admin();
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!verify_csrf()) {
                json_response(['message' => 'Invalid CSRF token'], 400);
            }
            
            // Ici vous pourriez sauvegarder les paramètres dans une table de configuration
            json_response(['message' => 'Paramètres sauvegardés']);
        } else {
            render('admin/settings');
        }
    }
    
    private function testDatabaseConnection() {
        try {
            $db = Database::getInstance();
            $db->query("SELECT 1");
            return true;
        } catch (Exception $e) {
            return false;
        }
    }
}