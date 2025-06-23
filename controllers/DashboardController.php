<?php

class DashboardController {
    private $userModel;
    private $qrModel;
    private $scanModel;
    
    public function __construct() {
        $this->userModel = new User();
        $this->qrModel = new QRCode();
        $this->scanModel = new QRScan();
    }
    
    public function index() {
        require_auth();
        $user = auth_user();
        
        $analytics = [
            'total_qr_codes' => 0,
            'total_scans' => 0,
            'scans_today' => 0,
            'active_qr_codes' => 0
        ];
        $topQRCodes = [];
        $recentActivity = [];
        $scanTrends = [];
        
        render('dashboard/index', [
            'user' => $user,
            'analytics' => $analytics,
            'topQRCodes' => $topQRCodes,
            'recentActivity' => $recentActivity,
            'scanTrends' => $scanTrends
        ]);
    }
    
    public function qrCodes() {
        require_auth();
        $user = auth_user();
        
        $qrCodes = $this->qrModel->getUserQRCodes($user['id']);
        render('dashboard/qr-codes', ['qrCodes' => $qrCodes]);
    }
    
    public function analytics() {
        require_auth();
        $user = auth_user();
        
        $analytics = $this->scanModel->getUserAnalytics($user['id']);
        $deviceBreakdown = $this->scanModel->getDeviceBreakdown($user['id']);
        $locationBreakdown = $this->scanModel->getLocationBreakdown($user['id']);
        $scanTrends = $this->scanModel->getScanTrends($user['id'], 30);
        
        render('dashboard/analytics', [
            'analytics' => $analytics,
            'deviceBreakdown' => $deviceBreakdown,
            'locationBreakdown' => $locationBreakdown,
            'scanTrends' => $scanTrends
        ]);
    }
    
    public function getAnalyticsData() {
        require_auth();
        $user = auth_user();
        
        $qrCodeId = $_GET['qr_code_id'] ?? null;
        
        if ($qrCodeId) {
            $qrCode = $this->qrModel->findById($qrCodeId);
            if (!$qrCode || $qrCode['user_id'] !== $user['id']) {
                json_response(['message' => 'QR code non trouvé'], 404);
            }
            
            $scans = $this->scanModel->getQRCodeScans($qrCodeId);
            $scanCount = $this->scanModel->getQRCodeScanCount($qrCodeId);
            
            json_response([
                'qr_code' => $qrCode,
                'scans' => $scans,
                'scan_count' => $scanCount
            ]);
        } else {
            $analytics = $this->scanModel->getUserAnalytics($user['id']);
            $deviceBreakdown = $this->scanModel->getDeviceBreakdown($user['id']);
            $locationBreakdown = $this->scanModel->getLocationBreakdown($user['id']);
            $scanTrends = $this->scanModel->getScanTrends($user['id'], 30);
            
            json_response([
                'analytics' => $analytics,
                'deviceBreakdown' => $deviceBreakdown,
                'locationBreakdown' => $locationBreakdown,
                'scanTrends' => $scanTrends
            ]);
        }
    }
}