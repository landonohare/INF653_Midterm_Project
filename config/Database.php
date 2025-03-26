<?php
// config/Database.php

class Database {
    private $host;
    private $db_name;
    private $username;
    private $password;
    public $conn;

    public function __construct() {
        // Use environment variables set in Render (or locally for dev)
        $this->host = getenv('DB_HOST') ?: 'localhost';
        $this->db_name = getenv('DB_NAME') ?: 'quotesdb';
        $this->username = getenv('DB_USERNAME') ?: 'postgres';
        $this->password = getenv('DB_PASSWORD') ?: 'password';
    }

    public function getConnection() {
        $this->conn = null;
        try {
            // Use pgsql:host=... instead of mysql:host=...
            // Also include port=5432 explicitly if needed
            $dsn = "pgsql:host={$this->host};port=5432;dbname={$this->db_name};sslmode=require";
            $this->conn = new PDO($dsn, $this->username, $this->password);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch(PDOException $exception) {
            echo json_encode([
                "message" => "Connection error: " . $exception->getMessage()
            ]);
            exit;
        }
        return $this->conn;
    }
}

