<?php
// models/Author.php

class Author {
    private $conn;
    private $table = "authors";

    public $id;
    public $author;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Retrieve authors; can be filtered by id if provided
    public function read($filters = []) {
        $query = "SELECT id, author FROM " . $this->table;
        $conditions = [];
        $params = [];

        if (isset($filters['id'])) {
            $conditions[] = "id = :id";
            $params[':id'] = $filters['id'];
        }
        if (count($conditions)) {
            $query .= " WHERE " . implode(" AND ", $conditions);
        }
        
        $stmt = $this->conn->prepare($query);
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }
        $stmt->execute();
        return $stmt;
    }

    // Create a new author
    public function create() {
        $query = "INSERT INTO " . $this->table . " (author) VALUES (:author)";
        $stmt = $this->conn->prepare($query);
        if (!$this->author) {
            return false;
        }
        $stmt->bindParam(':author', $this->author);
        if ($stmt->execute()) {
            $this->id = $this->conn->lastInsertId();
            return true;
        }
        return false;
    }

    // Update an existing author
    public function update() {
        $query = "UPDATE " . $this->table . " SET author = :author WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        if (!$this->id || !$this->author) {
            return false;
        }
        $stmt->bindParam(':id', $this->id);
        $stmt->bindParam(':author', $this->author);
        return $stmt->execute();
    }

    // Delete an author
    public function delete() {
        $query = "DELETE FROM " . $this->table . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        if (!$this->id) {
            return false;
        }
        $stmt->bindParam(':id', $this->id);
        return $stmt->execute();
    }
}
