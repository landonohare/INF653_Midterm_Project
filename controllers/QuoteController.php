<?php
// controllers/QuoteController.php

require_once __DIR__ . '../config/Database.php';
require_once __DIR__ . '../models/Quote.php';
require_once __DIR__ . '../models/Author.php';
require_once __DIR__ . '../models/Category.php';

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
        if(isset($params['random']) && $params['random'] == 'true'){
            $params['random'] = true;
        }
        $stmt = $this->quoteModel->read($params);
        $num = $stmt->rowCount();
        if($num > 0) {
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
        } else {
            echo json_encode(["message" => "No Quotes Found"]);
        }
    }

    private function handlePost() {
        // For POST, read JSON input
        $data = json_decode(file_get_contents("php://input"));
        // Check for required fields
        if(!isset($data->quote) || !isset($data->author_id) || !isset($data->category_id)) {
            echo json_encode(["message" => "Missing Required Parameters"]);
            return;
        }
        $this->quoteModel->quote = $data->quote;
        $this->quoteModel->author_id = $data->author_id;
        $this->quoteModel->category_id = $data->category_id;

        // (Additional checks to ensure author_id and category_id exist can be added here)

        if($this->quoteModel->create()){
            // Return created quote (you might run a read query to join the author and category names)
            echo json_encode([
                "id" => $this->quoteModel->id,
                "quote" => $data->quote,
                "author_id" => $data->author_id,
                "category_id" => $data->category_id
            ]);
        } else {
            echo json_encode(["message" => "Quote Not Created"]);
        }
    }

    private function handlePut() {
        // For PUT, get JSON input
        $data = json_decode(file_get_contents("php://input"));
        if(!isset($data->id) || !isset($data->quote) || !isset($data->author_id) || !isset($data->category_id)) {
            echo json_encode(["message" => "Missing Required Parameters"]);
            return;
        }
        $this->quoteModel->id = $data->id;
        $this->quoteModel->quote = $data->quote;
        $this->quoteModel->author_id = $data->author_id;
        $this->quoteModel->category_id = $data->category_id;

        // (Again, add checks for author_id and category_id existence if needed)

        if($this->quoteModel->update()){
            echo json_encode([
                "id" => $data->id,
                "quote" => $data->quote,
                "author_id" => $data->author_id,
                "category_id" => $data->category_id
            ]);
        } else {
            echo json_encode(["message" => "No Quotes Found"]);
        }
    }

    private function handleDelete() {
        // For DELETE, you may get parameters via GET or JSON input depending on your client.
        // Here we assume an id is passed via the query string.
        if(!isset($_GET['id'])) {
            echo json_encode(["message" => "Missing Required Parameters"]);
            return;
        }
        $this->quoteModel->id = $_GET['id'];
        if($this->quoteModel->delete()){
            echo json_encode(["id" => $_GET['id']]);
        } else {
            echo json_encode(["message" => "No Quotes Found"]);
        }
    }
}
