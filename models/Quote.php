<?php
// models/Quote.php

class Quote {
    private $conn;
    private $table = "quotes";

    public $id;
    public $quote;
    public $author_id;
    public $category_id;
    // Extended fields for joined data
    public $author;
    public $category;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Get all quotes or filtered by id, author_id, category_id, etc.
    public function read($filters = []) {
        $query = "SELECT q.id, q.quote, a.author, c.category 
                  FROM " . $this->table . " q
                  INNER JOIN authors a ON q.author_id = a.id
                  INNER JOIN categories c ON q.category_id = c.id";
        
        $conditions = [];
        $params = [];

        if(isset($filters['id'])) {
            $conditions[] = "q.id = :id";
            $params[':id'] = $filters['id'];
        }
        if(isset($filters['author_id'])) {
            $conditions[] = "q.author_id = :author_id";
            $params[':author_id'] = $filters['author_id'];
        }
        if(isset($filters['category_id'])) {
            $conditions[] = "q.category_id = :category_id";
            $params[':category_id'] = $filters['category_id'];
        }
        // Optional extra: random quote selection
        if(isset($filters['random']) && $filters['random'] == true) {
            $query .= (count($conditions) ? " WHERE " . implode(" AND ", $conditions) : "");
            $query .= " ORDER BY RAND() LIMIT 1";
        } else {
            if(count($conditions)) {
                $query .= " WHERE " . implode(" AND ", $conditions);
            }
        }
        
        $stmt = $this->conn->prepare($query);
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }
        $stmt->execute();
        return $stmt;
    }

    // Create a new quote
    public function create() {
        $query = "INSERT INTO " . $this->table . " (quote, author_id, category_id)
                  VALUES (:quote, :author_id, :category_id)";
        $stmt = $this->conn->prepare($query);
        // Ensure required fields are provided
        if(!$this->quote || !$this->author_id || !$this->category_id){
            return false;
        }
        $stmt->bindParam(':quote', $this->quote);
        $stmt->bindParam(':author_id', $this->author_id);
        $stmt->bindParam(':category_id', $this->category_id);
        if($stmt->execute()){
            $this->id = $this->conn->lastInsertId();
            return true;
        }
        return false;
    }

    // Update an existing quote
    public function update() {
        $query = "UPDATE " . $this->table . " 
                  SET quote = :quote, author_id = :author_id, category_id = :category_id
                  WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        if(!$this->id || !$this->quote || !$this->author_id || !$this->category_id){
            return false;
        }
        $stmt->bindParam(':id', $this->id);
        $stmt->bindParam(':quote', $this->quote);
        $stmt->bindParam(':author_id', $this->author_id);
        $stmt->bindParam(':category_id', $this->category_id);
        return $stmt->execute();
    }

    // Delete a quote
    public function delete() {
        $query = "DELETE FROM " . $this->table . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        if(!$this->id){
            return false;
        }
        $stmt->bindParam(':id', $this->id);
        return $stmt->execute();
    }
}
