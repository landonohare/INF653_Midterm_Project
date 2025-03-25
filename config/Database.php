<?php
// config/Database.php

class Database {
    private $host = getenv('DB_HOST') ?: 'localhost';
    private $db_name = getenv('DB_NAME') ?: 'quotesdb';
    private $username = getenv('DB_USERNAME') ?: 'your_username';
    private $password = getenv('DB_PASSWORD') ?: 'your_password';
    public $conn;

    public function getConnection() {
        $this->conn = null;
        try {
            $this->conn = new PDO(
                "mysql:host=" . $this->host . ";dbname=" . $this->db_name,
                $this->username,
                $this->password
            );
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch(PDOException $exception) {
            echo json_encode(["message" => "Connection error: " . $exception->getMessage()]);
            exit;
        }
        return $this->conn;
    }
}

