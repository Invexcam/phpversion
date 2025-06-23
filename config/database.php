<?php

class Database {
    private static $instance = null;
    private $connection;
    
    private function __construct() {
        $host = getenv('PGHOST') ?: $_ENV['PGHOST'] ?? 'localhost';
        $port = getenv('PGPORT') ?: $_ENV['PGPORT'] ?? '5432';
        $dbname = getenv('PGDATABASE') ?: $_ENV['PGDATABASE'] ?? 'qr_generator';
        $username = getenv('PGUSER') ?: $_ENV['PGUSER'] ?? 'postgres';
        $password = getenv('PGPASSWORD') ?: $_ENV['PGPASSWORD'] ?? '';
        
        $dsn = "pgsql:host=$host;port=$port;dbname=$dbname";
        
        try {
            $this->connection = new PDO($dsn, $username, $password, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ]);
        } catch (PDOException $e) {
            throw new Exception("Database connection failed: " . $e->getMessage());
        }
    }
    
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    public function getConnection() {
        return $this->connection;
    }
    
    public function prepare($query) {
        return $this->connection->prepare($query);
    }
    
    public function query($query) {
        return $this->connection->query($query);
    }
    
    public function lastInsertId() {
        return $this->connection->lastInsertId();
    }
}