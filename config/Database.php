<?php
// config/Database.php

class Database {
    private $host = "localhost";
    private $db_name = "quotesdb";
    private $username = "your_username";
    private $password = "your_password";
    public $conn;

    public function getConnection() {
        $this->conn = null;
        try {
            // Using PDO for MySQL connection
            $this->conn = new PDO("mysql:host=" . $this->host . ";dbname=" . $this->db_name, 
                                  $this->username, $this->password);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch(PDOException $exception) {
            echo json_encode(["message" => "Connection error: " . $exception->getMessage()]);
            exit;
        }
        return $this->conn;
    }
}
