<?php

class QRScan {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    public function create($data) {
        $sql = "INSERT INTO qr_scans (qr_code_id, ip_address, user_agent, device_type, country, city, scanned_at) 
                VALUES (:qr_code_id, :ip_address, :user_agent, :device_type, :country, :city, NOW()) 
                RETURNING *";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'qr_code_id' => $data['qr_code_id'],
            'ip_address' => $data['ip_address'],
            'user_agent' => $data['user_agent'],
            'device_type' => $data['device_type'],
            'country' => $data['country'],
            'city' => $data['city']
        ]);
        
        return $stmt->fetch();
    }
    
    public function getQRCodeScans($qrCodeId) {
        $sql = "SELECT * FROM qr_scans WHERE qr_code_id = :qr_code_id ORDER BY scanned_at DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['qr_code_id' => $qrCodeId]);
        return $stmt->fetchAll();
    }
    
    public function getQRCodeScanCount($qrCodeId) {
        $sql = "SELECT COUNT(*) as count FROM qr_scans WHERE qr_code_id = :qr_code_id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['qr_code_id' => $qrCodeId]);
        return $stmt->fetch()['count'];
    }
    
    public function getUserAnalytics($userId) {
        $sql = "SELECT 
                    COUNT(DISTINCT qr.id) as total_qr_codes,
                    COUNT(s.id) as total_scans,
                    COUNT(CASE WHEN DATE(s.scanned_at) = CURRENT_DATE THEN 1 END) as scans_today,
                    COUNT(DISTINCT CASE WHEN s.id IS NOT NULL THEN qr.id END) as active_qr_codes
                FROM qr_codes qr
                LEFT JOIN qr_scans s ON qr.id = s.qr_code_id
                WHERE qr.user_id = :user_id";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['user_id' => $userId]);
        return $stmt->fetch();
    }
    
    public function getTopPerformingQRCodes($userId, $limit = 5) {
        $sql = "SELECT qr.*, COUNT(s.id) as scan_count
                FROM qr_codes qr
                LEFT JOIN qr_scans s ON qr.id = s.qr_code_id
                WHERE qr.user_id = :user_id
                GROUP BY qr.id
                ORDER BY scan_count DESC
                LIMIT :limit";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':user_id', $userId);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        $results = $stmt->fetchAll();
        
        foreach ($results as &$result) {
            $result['content'] = json_decode($result['content'], true);
            $result['style'] = json_decode($result['style'], true);
        }
        
        return $results;
    }
    
    public function getDeviceBreakdown($userId) {
        $sql = "SELECT s.device_type, COUNT(*) as count
                FROM qr_scans s
                JOIN qr_codes qr ON s.qr_code_id = qr.id
                WHERE qr.user_id = :user_id
                GROUP BY s.device_type
                ORDER BY count DESC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['user_id' => $userId]);
        return $stmt->fetchAll();
    }
    
    public function getLocationBreakdown($userId) {
        $sql = "SELECT s.country, COUNT(*) as count
                FROM qr_scans s
                JOIN qr_codes qr ON s.qr_code_id = qr.id
                WHERE qr.user_id = :user_id AND s.country IS NOT NULL
                GROUP BY s.country
                ORDER BY count DESC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['user_id' => $userId]);
        return $stmt->fetchAll();
    }
    
    public function getScanTrends($userId, $days = 7) {
        $sql = "SELECT DATE(s.scanned_at) as date, COUNT(*) as scans
                FROM qr_scans s
                JOIN qr_codes qr ON s.qr_code_id = qr.id
                WHERE qr.user_id = :user_id 
                AND s.scanned_at >= CURRENT_DATE - INTERVAL '$days days'
                GROUP BY DATE(s.scanned_at)
                ORDER BY date";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['user_id' => $userId]);
        return $stmt->fetchAll();
    }
    
    public function getRecentScanActivity($userId, $limit = 10) {
        $sql = "SELECT s.*, qr.name as qr_name
                FROM qr_scans s
                JOIN qr_codes qr ON s.qr_code_id = qr.id
                WHERE qr.user_id = :user_id
                ORDER BY s.scanned_at DESC
                LIMIT :limit";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':user_id', $userId);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }
    
    public function getPublicStats() {
        $sql = "SELECT 
                    COUNT(DISTINCT qr.id) as total_qr_codes,
                    COUNT(s.id) as total_scans,
                    COUNT(DISTINCT qr.user_id) as total_users,
                    COUNT(CASE WHEN DATE(s.scanned_at) = CURRENT_DATE THEN 1 END) as scans_today
                FROM qr_codes qr
                LEFT JOIN qr_scans s ON qr.id = s.qr_code_id";
        
        $stmt = $this->db->query($sql);
        return $stmt->fetch();
    }
    
    public function getAllScans($limit = 50, $offset = 0) {
        $sql = "SELECT s.*, qr.name as qr_name, qr.user_id, u.email as user_email
                FROM qr_scans s
                LEFT JOIN qr_codes qr ON s.qr_code_id = qr.id
                LEFT JOIN users u ON qr.user_id = u.id
                ORDER BY s.scanned_at DESC
                LIMIT :limit OFFSET :offset";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }
    
    public function getTotalScanCount() {
        $sql = "SELECT COUNT(*) as count FROM qr_scans";
        $stmt = $this->db->query($sql);
        return $stmt->fetch()['count'];
    }
}