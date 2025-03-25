<?php
// controllers/AuthorController.php

require_once '../config/Database.php';
require_once '../models/Author.php';

class AuthorController {
    private $db;
    private $authorModel;

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
        $this->authorModel = new Author($this->db);
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
        $stmt = $this->authorModel->read($params);
        $num = $stmt->rowCount();
        if ($num > 0) {
            $authors_arr = [];
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                extract($row);
                $author_item = [
                    "id" => $id,
                    "author" => $author
                ];
                $authors_arr[] = $author_item;
            }
            echo json_encode($authors_arr);
        } else {
            echo json_encode(["message" => "author_id Not Found"]);
        }
    }

    private function handlePost() {
        $data = json_decode(file_get_contents("php://input"));
        if (!isset($data->author)) {
            echo json_encode(["message" => "Missing Required Parameters"]);
            return;
        }
        $this->authorModel->author = $data->author;
        if ($this->authorModel->create()) {
            echo json_encode([
                "id" => $this->authorModel->id,
                "author" => $data->author
            ]);
        } else {
            echo json_encode(["message" => "Author Not Created"]);
        }
    }

    private function handlePut() {
        $data = json_decode(file_get_contents("php://input"));
        if (!isset($data->id) || !isset($data->author)) {
            echo json_encode(["message" => "Missing Required Parameters"]);
            return;
        }
        $this->authorModel->id = $data->id;
        $this->authorModel->author = $data->author;
        if ($this->authorModel->update()) {
            echo json_encode([
                "id" => $data->id,
                "author" => $data->author
            ]);
        } else {
            echo json_encode(["message" => "author_id Not Found"]);
        }
    }

    private function handleDelete() {
        if (!isset($_GET['id'])) {
            echo json_encode(["message" => "Missing Required Parameters"]);
            return;
        }
        $this->authorModel->id = $_GET['id'];
        if ($this->authorModel->delete()) {
            echo json_encode(["id" => $_GET['id']]);
        } else {
            echo json_encode(["message" => "author_id Not Found"]);
        }
    }
}
