<?php
// controllers/QuoteController.php

require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../models/Quote.php';
require_once __DIR__ . '/../models/Author.php';
require_once __DIR__ . '/../models/Category.php';

class QuoteController {
    private $db;
    private $quoteModel;

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
        $this->quoteModel = new Quote($this->db);
    }

    public function processRequest($method, $params) {
        switch ($method) {
            case 'GET':
                $this->handleGet($params);
                break;
            case 'POST':
                $this->handlePost();
                break;
            case 'PUT':
                $this->handlePut();
                break;
            case 'DELETE':
                $this->handleDelete();
                break;
            default:
                echo json_encode(['message' => 'Method Not Allowed']);
                break;
        }
    }

    private function handleGet($params) {
        // Check for "random" parameter if provided
        if (isset($params['random']) && $params['random'] == 'true'){
            $params['random'] = true;
        }
        $stmt = $this->quoteModel->read($params);
        $num = $stmt->rowCount();
        if ($num > 0) {
            // If an id parameter is provided, return a single object.
            if (isset($params['id'])) {
                $row = $stmt->fetch(PDO::FETCH_ASSOC);
                extract($row);
                $quote_item = [
                    "id" => $id,
                    "quote" => $quote,
                    "author" => $author,
                    "category" => $category
                ];
                echo json_encode($quote_item);
            } else {
                // Otherwise, return an array of objects.
                $quotes_arr = [];
                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    extract($row);
                    $quote_item = [
                        "id" => $id,
                        "quote" => $quote,
                        "author" => $author,
                        "category" => $category
                    ];
                    $quotes_arr[] = $quote_item;
                }
                echo json_encode($quotes_arr);
            }
        } else {
            echo json_encode(["message" => "No Quotes Found"]);
        }
    }
    
    private function handlePost() {
        $data = json_decode(file_get_contents("php://input"));
        if (!isset($data->quote) || !isset($data->author_id) || !isset($data->category_id)) {
            echo json_encode(["message" => "Missing Required Parameters"]);
            return;
        }
        $this->quoteModel->quote = $data->quote;
        $this->quoteModel->author_id = $data->author_id;
        $this->quoteModel->category_id = $data->category_id;
        
        try {
            if ($this->quoteModel->create()) {
                echo json_encode([
                    "id" => $this->quoteModel->id,
                    "quote" => $data->quote,
                    "author_id" => $data->author_id,
                    "category_id" => $data->category_id
                ]);
            } else {
                echo json_encode(["message" => "Quote Not Created"]);
            }
        } catch (PDOException $e) {
            $errorMsg = $e->getMessage();
            // Check if it's a foreign key violation and return a custom message
            if (stripos($errorMsg, 'foreign key constraint') !== false) {
                if (stripos($errorMsg, 'author_id') !== false) {
                    echo json_encode(["message" => "author_id Not Found"]);
                } elseif (stripos($errorMsg, 'category_id') !== false) {
                    echo json_encode(["message" => "category_id Not Found"]);
                } else {
                    echo json_encode(["message" => $errorMsg]);
                }
            } else {
                echo json_encode(["message" => $errorMsg]);
            }
        }
    }
    
    private function handlePut() {
        $data = json_decode(file_get_contents("php://input"));
        if (!isset($data->id) || !isset($data->quote) || !isset($data->author_id) || !isset($data->category_id)) {
            echo json_encode(["message" => "Missing Required Parameters"]);
            return;
        }
        $this->quoteModel->id = $data->id;
        $this->quoteModel->quote = $data->quote;
        $this->quoteModel->author_id = $data->author_id;
        $this->quoteModel->category_id = $data->category_id;
        
        try {
            if ($this->quoteModel->update()) {
                echo json_encode([
                    "id" => $data->id,
                    "quote" => $data->quote,
                    "author_id" => $data->author_id,
                    "category_id" => $data->category_id
                ]);
            } else {
                echo json_encode(["message" => "No Quotes Found"]);
            }
        } catch (PDOException $e) {
            $errorMsg = $e->getMessage();
            if (stripos($errorMsg, 'foreign key constraint') !== false) {
                if (stripos($errorMsg, 'author_id') !== false) {
                    echo json_encode(["message" => "author_id Not Found"]);
                } elseif (stripos($errorMsg, 'category_id') !== false) {
                    echo json_encode(["message" => "category_id Not Found"]);
                } else {
                    echo json_encode(["message" => $errorMsg]);
                }
            } else {
                echo json_encode(["message" => $errorMsg]);
            }
        }
    }
    

    private function handleDelete() {
        if (!isset($_GET['id'])) {
            echo json_encode(["message" => "Missing Required Parameters"]);
            return;
        }
        $this->quoteModel->id = $_GET['id'];
        try {
            if ($this->quoteModel->delete()) {
                echo json_encode(["id" => $_GET['id']]);
            } else {
                echo json_encode(["message" => "No Quotes Found"]);
            }
        } catch (PDOException $e) {
            echo json_encode(["message" => $e->getMessage()]);
        }
    }
    