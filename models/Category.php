<?php
// models/Category.php

class Category {
    private $conn;
    private $table = "categories";

    public $id;
    public $category;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Retrieve categories; can be filtered by id if provided
    public function read($filters = []) {
        $query = "SELECT id, category FROM " . $this->table;
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

    // Create a new category
    public function create() {
        $query = "INSERT INTO " . $this->table . " (category) VALUES (:category)";
        $stmt = $this->conn->prepare($query);
        if (!$this->category) {
            return false;
        }
        $stmt->bindParam(':category', $this->category);
        if ($stmt->execute()) {
            $this->id = $this->conn->lastInsertId();
            return true;
        }
        return false;
    }

    // Update an existing category
    public function update() {
        $query = "UPDATE " . $this->table . " SET category = :category WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        if (!$this->id || !$this->category) {
            return false;
        }
        $stmt->bindParam(':id', $this->id);
        $stmt->bindParam(':category', $this->category);
        return $stmt->execute();
    }

    // Delete a category
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
