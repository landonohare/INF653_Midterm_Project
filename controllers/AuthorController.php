<?php
// controllers/AuthorController.php

require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../models/Author.php';

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
            // If an id parameter is provided, return a single object
            if (isset($params['id'])) {
                $row = $stmt->fetch(PDO::FETCH_ASSOC);
                echo json_encode($row);
            } else {
                $authors_arr = [];
                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    $authors_arr[] = $row;
                }
                echo json_encode($authors_arr);
            }
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
        try {
            if ($this->authorModel->create()) {
                echo json_encode([
                    "id" => $this->authorModel->id,
                    "author" => $data->author
                ]);
            } else {
                echo json_encode(["message" => "Author Not Created"]);
            }
        } catch (PDOException $e) {
            echo json_encode(["message" => $e->getMessage()]);
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
        try {
            if ($this->authorModel->update()) {
                echo json_encode([
                    "id" => $data->id,
                    "author" => $data->author
                ]);
            } else {
                echo json_encode(["message" => "author_id Not Found"]);
            }
        } catch (PDOException $e) {
            echo json_encode(["message" => $e->getMessage()]);
        }
    }

    private function handleDelete() {
        if (!isset($_GET['id'])) {
            echo json_encode(["message" => "Missing Required Parameters"]);
            return;
        }
        $this->authorModel->id = $_GET['id'];
        try {
            if ($this->authorModel->delete()) {
                echo json_encode(["id" => $_GET['id']]);
            } else {
                echo json_encode(["message" => "author_id Not Found"]);
            }
        } catch (PDOException $e) {
            echo json_encode(["message" => $e->getMessage()]);
        }
    }
}
