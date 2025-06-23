<?php

class User {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    public function create($data) {
        $sql = "INSERT INTO users (id, email, first_name, last_name, profile_image_url, password_hash, role, created_at) 
                VALUES (:id, :email, :first_name, :last_name, :profile_image_url, :password_hash, :role, NOW()) 
                RETURNING *";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'id' => $data['id'] ?? uniqid(),
            'email' => $data['email'],
            'first_name' => $data['first_name'] ?? null,
            'last_name' => $data['last_name'] ?? null,
            'profile_image_url' => $data['profile_image_url'] ?? null,
            'password_hash' => isset($data['password']) ? password_hash($data['password'], PASSWORD_DEFAULT) : null,
            'role' => $data['role'] ?? 'user'
        ]);
        
        return $stmt->fetch();
    }
    
    public function findById($id) {
        $sql = "SELECT * FROM users WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => $id]);
        return $stmt->fetch();
    }
    
    public function findByEmail($email) {
        $sql = "SELECT * FROM users WHERE email = :email";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['email' => $email]);
        return $stmt->fetch();
    }
    
    public function update($id, $data) {
        $fields = [];
        $params = ['id' => $id];
        
        foreach ($data as $key => $value) {
            if ($key !== 'id') {
                $fields[] = "$key = :$key";
                $params[$key] = $value;
            }
        }
        
        if (empty($fields)) return false;
        
        $sql = "UPDATE users SET " . implode(', ', $fields) . " WHERE id = :id RETURNING *";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetch();
    }
    
    public function updateSubscription($userId, $subscriptionData) {
        $sql = "UPDATE users SET 
                subscription_id = :subscription_id,
                subscription_status = :subscription_status,
                subscription_plan_id = :subscription_plan_id
                WHERE id = :user_id RETURNING *";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'user_id' => $userId,
            'subscription_id' => $subscriptionData['subscription_id'],
            'subscription_status' => $subscriptionData['subscription_status'],
            'subscription_plan_id' => $subscriptionData['subscription_plan_id']
        ]);
        
        return $stmt->fetch();
    }
    
    public function verifyPassword($password, $hash) {
        return password_verify($password, $hash);
    }
    
    public function getAllUsers($limit = 50, $offset = 0) {
        $sql = "SELECT id, email, first_name, last_name, role, subscription_status, created_at 
                FROM users ORDER BY created_at DESC LIMIT :limit OFFSET :offset";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }
    
    public function getUserCount() {
        $sql = "SELECT COUNT(*) as count FROM users";
        $stmt = $this->db->query($sql);
        return $stmt->fetch()['count'];
    }
    
    public function delete($id) {
        $sql = "DELETE FROM users WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute(['id' => $id]);
    }
}