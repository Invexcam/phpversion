<?php

class QRCode {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    public function create($userId, $data) {
        $sql = "INSERT INTO qr_codes (user_id, name, original_url, short_code, content_type, content, style, created_at) 
                VALUES (:user_id, :name, :original_url, :short_code, :content_type, :content, :style, NOW()) 
                RETURNING *";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'user_id' => $userId,
            'name' => $data['name'],
            'original_url' => $data['original_url'],
            'short_code' => $this->generateShortCode(),
            'content_type' => $data['content_type'],
            'content' => json_encode($data['content']),
            'style' => json_encode($data['style'] ?? [])
        ]);
        
        return $stmt->fetch();
    }
    
    public function findById($id) {
        $sql = "SELECT * FROM qr_codes WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => $id]);
        $result = $stmt->fetch();
        
        if ($result) {
            $result['content'] = json_decode($result['content'], true);
            $result['style'] = json_decode($result['style'], true);
        }
        
        return $result;
    }
    
    public function findByShortCode($shortCode) {
        $sql = "SELECT * FROM qr_codes WHERE short_code = :short_code";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['short_code' => $shortCode]);
        $result = $stmt->fetch();
        
        if ($result) {
            $result['content'] = json_decode($result['content'], true);
            $result['style'] = json_decode($result['style'], true);
        }
        
        return $result;
    }
    
    public function getUserQRCodes($userId) {
        $sql = "SELECT * FROM qr_codes WHERE user_id = :user_id ORDER BY created_at DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['user_id' => $userId]);
        $results = $stmt->fetchAll();
        
        foreach ($results as &$result) {
            $result['content'] = json_decode($result['content'], true);
            $result['style'] = json_decode($result['style'], true);
        }
        
        return $results;
    }
    
    public function update($id, $data) {
        $fields = [];
        $params = ['id' => $id];
        
        foreach ($data as $key => $value) {
            if ($key !== 'id') {
                if ($key === 'content' || $key === 'style') {
                    $fields[] = "$key = :$key";
                    $params[$key] = json_encode($value);
                } else {
                    $fields[] = "$key = :$key";
                    $params[$key] = $value;
                }
            }
        }
        
        if (empty($fields)) return false;
        
        $sql = "UPDATE qr_codes SET " . implode(', ', $fields) . " WHERE id = :id RETURNING *";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        $result = $stmt->fetch();
        
        if ($result) {
            $result['content'] = json_decode($result['content'], true);
            $result['style'] = json_decode($result['style'], true);
        }
        
        return $result;
    }
    
    public function delete($id) {
        $sql = "DELETE FROM qr_codes WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute(['id' => $id]);
    }
    
    public function getAllQRCodes($limit = 50, $offset = 0) {
        $sql = "SELECT qr.*, u.email as user_email, u.first_name, u.last_name
                FROM qr_codes qr 
                LEFT JOIN users u ON qr.user_id = u.id 
                ORDER BY qr.created_at DESC 
                LIMIT :limit OFFSET :offset";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        $results = $stmt->fetchAll();
        
        foreach ($results as &$result) {
            $result['content'] = json_decode($result['content'], true);
            $result['style'] = json_decode($result['style'], true);
        }
        
        return $results;
    }
    
    public function getQRCodeCount() {
        $sql = "SELECT COUNT(*) as count FROM qr_codes";
        $stmt = $this->db->query($sql);
        return $stmt->fetch()['count'];
    }
    
    private function generateShortCode() {
        do {
            $shortCode = substr(str_shuffle('abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789'), 0, 8);
            $sql = "SELECT COUNT(*) as count FROM qr_codes WHERE short_code = :short_code";
            $stmt = $this->db->prepare($sql);
            $stmt->execute(['short_code' => $shortCode]);
            $exists = $stmt->fetch()['count'] > 0;
        } while ($exists);
        
        return $shortCode;
    }
}