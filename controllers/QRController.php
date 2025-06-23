<?php

class QRController {
    private $qrModel;
    private $scanModel;
    
    public function __construct() {
        $this->qrModel = new QRCode();
        $this->scanModel = new QRScan();
    }
    
    public function getUserQRCodes() {
        require_auth();
        $user = auth_user();
        $qrCodes = $this->qrModel->getUserQRCodes($user['id']);
        json_response($qrCodes);
    }
    
    public function createQRCode() {
        require_auth();
        
        if (!verify_csrf()) {
            json_response(['message' => 'Invalid CSRF token'], 400);
        }
        
        $user = auth_user();
        $data = json_decode(file_get_contents('php://input'), true);
        
        if (!$data) {
            json_response(['message' => 'Données invalides'], 400);
        }
        
        $required = ['name', 'content_type', 'content'];
        foreach ($required as $field) {
            if (!isset($data[$field]) || empty($data[$field])) {
                json_response(['message' => "Le champ $field est requis"], 400);
            }
        }
        
        // Generate original URL based on content type
        $originalUrl = $this->generateOriginalUrl($data['content_type'], $data['content']);
        
        try {
            $qrCode = $this->qrModel->create($user['id'], [
                'name' => $data['name'],
                'original_url' => $originalUrl,
                'content_type' => $data['content_type'],
                'content' => $data['content'],
                'style' => $data['style'] ?? []
            ]);
            
            json_response($qrCode, 201);
        } catch (Exception $e) {
            json_response(['message' => 'Erreur lors de la création du QR code'], 500);
        }
    }
    
    public function updateQRCode($id) {
        require_auth();
        
        if (!verify_csrf()) {
            json_response(['message' => 'Invalid CSRF token'], 400);
        }
        
        $user = auth_user();
        $qrCode = $this->qrModel->findById($id);
        
        if (!$qrCode || $qrCode['user_id'] !== $user['id']) {
            json_response(['message' => 'QR code non trouvé'], 404);
        }
        
        $data = json_decode(file_get_contents('php://input'), true);
        
        if (!$data) {
            json_response(['message' => 'Données invalides'], 400);
        }
        
        // Update original URL if content changed
        if (isset($data['content_type']) && isset($data['content'])) {
            $data['original_url'] = $this->generateOriginalUrl($data['content_type'], $data['content']);
        }
        
        try {
            $updatedQRCode = $this->qrModel->update($id, $data);
            json_response($updatedQRCode);
        } catch (Exception $e) {
            json_response(['message' => 'Erreur lors de la mise à jour'], 500);
        }
    }
    
    public function deleteQRCode($id) {
        require_auth();
        
        if (!verify_csrf()) {
            json_response(['message' => 'Invalid CSRF token'], 400);
        }
        
        $user = auth_user();
        $qrCode = $this->qrModel->findById($id);
        
        if (!$qrCode || $qrCode['user_id'] !== $user['id']) {
            json_response(['message' => 'QR code non trouvé'], 404);
        }
        
        try {
            $this->qrModel->delete($id);
            json_response(['message' => 'QR code supprimé avec succès']);
        } catch (Exception $e) {
            json_response(['message' => 'Erreur lors de la suppression'], 500);
        }
    }
    
    public function getQRCode($id) {
        $qrCode = $this->qrModel->findById($id);
        
        if (!$qrCode) {
            json_response(['message' => 'QR code non trouvé'], 404);
        }
        
        json_response($qrCode);
    }
    
    public function redirectShortCode($shortCode) {
        $qrCode = $this->qrModel->findByShortCode($shortCode);
        
        if (!$qrCode) {
            http_response_code(404);
            echo "QR code non trouvé";
            return;
        }
        
        // Record scan
        $this->recordScan($qrCode['id']);
        
        // Redirect based on content type
        switch ($qrCode['content_type']) {
            case 'url':
                redirect($qrCode['content']['url']);
                break;
            case 'text':
                echo "<pre>" . htmlspecialchars($qrCode['content']['text']) . "</pre>";
                break;
            case 'email':
                $content = $qrCode['content'];
                $mailto = "mailto:" . $content['email'];
                if (!empty($content['subject'])) {
                    $mailto .= "?subject=" . urlencode($content['subject']);
                }
                if (!empty($content['body'])) {
                    $mailto .= (!empty($content['subject']) ? "&" : "?") . "body=" . urlencode($content['body']);
                }
                redirect($mailto);
                break;
            case 'phone':
                redirect("tel:" . $qrCode['content']['phone']);
                break;
            case 'sms':
                $content = $qrCode['content'];
                $sms = "sms:" . $content['phone'];
                if (!empty($content['message'])) {
                    $sms .= "?body=" . urlencode($content['message']);
                }
                redirect($sms);
                break;
            case 'wifi':
                // Display WiFi info
                $content = $qrCode['content'];
                echo "<h3>Configuration WiFi</h3>";
                echo "<p><strong>Réseau:</strong> " . htmlspecialchars($content['ssid']) . "</p>";
                echo "<p><strong>Mot de passe:</strong> " . htmlspecialchars($content['password']) . "</p>";
                echo "<p><strong>Sécurité:</strong> " . htmlspecialchars($content['security']) . "</p>";
                break;
            case 'vcard':
                // Display contact info
                $content = $qrCode['content'];
                echo "<h3>Contact</h3>";
                echo "<p><strong>Nom:</strong> " . htmlspecialchars($content['firstName'] . ' ' . $content['lastName']) . "</p>";
                if (!empty($content['email'])) {
                    echo "<p><strong>Email:</strong> " . htmlspecialchars($content['email']) . "</p>";
                }
                if (!empty($content['phone'])) {
                    echo "<p><strong>Téléphone:</strong> " . htmlspecialchars($content['phone']) . "</p>";
                }
                break;
            default:
                redirect($qrCode['original_url']);
        }
    }
    
    public function getQRCodeAnalytics($id) {
        require_auth();
        
        $user = auth_user();
        $qrCode = $this->qrModel->findById($id);
        
        if (!$qrCode || $qrCode['user_id'] !== $user['id']) {
            json_response(['message' => 'QR code non trouvé'], 404);
        }
        
        $scans = $this->scanModel->getQRCodeScans($id);
        $scanCount = $this->scanModel->getQRCodeScanCount($id);
        
        json_response([
            'qr_code' => $qrCode,
            'scans' => $scans,
            'scan_count' => $scanCount
        ]);
    }
    
    public function generateQRImage($id) {
        $qrCode = $this->qrModel->findById($id);
        
        if (!$qrCode) {
            http_response_code(404);
            echo "QR code non trouvé";
            return;
        }
        
        $url = "https://" . $_SERVER['HTTP_HOST'] . "/s/" . $qrCode['short_code'];
        $qrImageData = $this->generateQRCodeImage($url, $qrCode['style']);
        
        header('Content-Type: image/png');
        echo $qrImageData;
    }
    
    private function generateOriginalUrl($contentType, $content) {
        switch ($contentType) {
            case 'url':
                return $content['url'];
            case 'text':
                return "data:text/plain," . urlencode($content['text']);
            case 'email':
                $mailto = "mailto:" . $content['email'];
                if (!empty($content['subject'])) {
                    $mailto .= "?subject=" . urlencode($content['subject']);
                }
                return $mailto;
            case 'phone':
                return "tel:" . $content['phone'];
            case 'sms':
                $sms = "sms:" . $content['phone'];
                if (!empty($content['message'])) {
                    $sms .= "?body=" . urlencode($content['message']);
                }
                return $sms;
            case 'wifi':
                return "WIFI:T:" . $content['security'] . ";S:" . $content['ssid'] . ";P:" . $content['password'] . ";;";
            case 'vcard':
                $vcard = "BEGIN:VCARD\nVERSION:3.0\n";
                $vcard .= "FN:" . $content['firstName'] . " " . $content['lastName'] . "\n";
                if (!empty($content['email'])) {
                    $vcard .= "EMAIL:" . $content['email'] . "\n";
                }
                if (!empty($content['phone'])) {
                    $vcard .= "TEL:" . $content['phone'] . "\n";
                }
                $vcard .= "END:VCARD";
                return "data:text/vcard," . urlencode($vcard);
            default:
                return "";
        }
    }
    
    private function recordScan($qrCodeId) {
        $ipAddress = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
        $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? 'unknown';
        $deviceType = $this->getDeviceType($userAgent);
        
        // Simple geo-location (you might want to use a proper service)
        $location = $this->getLocationFromIP($ipAddress);
        
        $this->scanModel->create([
            'qr_code_id' => $qrCodeId,
            'ip_address' => $ipAddress,
            'user_agent' => $userAgent,
            'device_type' => $deviceType,
            'country' => $location['country'],
            'city' => $location['city']
        ]);
    }
    
    private function getDeviceType($userAgent) {
        if (preg_match('/mobile/i', $userAgent)) {
            return 'mobile';
        } elseif (preg_match('/tablet/i', $userAgent)) {
            return 'tablet';
        } else {
            return 'desktop';
        }
    }
    
    private function getLocationFromIP($ip) {
        // Placeholder - integrate with a real geo-IP service
        return ['country' => 'Unknown', 'city' => 'Unknown'];
    }
    
    private function generateQRCodeImage($data, $style = []) {
        $options = [
            'size' => $style['size'] ?? 300,
            'margin' => $style['margin'] ?? 10,
            'darkColor' => $style['darkColor'] ?? '#000000',
            'lightColor' => $style['lightColor'] ?? '#ffffff'
        ];
        
        return QRGenerator::generateQRCode($data, $options);
    }
}