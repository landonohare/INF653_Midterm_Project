<?php
// controllers/CategoryController.php

require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../models/Category.php';

class CategoryController {
    private $db;
    private $categoryModel;

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
        $this->categoryModel = new Category($this->db);
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
        $stmt = $this->categoryModel->read($params);
        $num = $stmt->rowCount();
        if ($num > 0) {
            // If an id parameter is provided, return a single object
            if (isset($params['id'])) {
                $row = $stmt->fetch(PDO::FETCH_ASSOC);
                echo json_encode($row);
            } else {
                $categories_arr = [];
                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    $categories_arr[] = $row;
                }
                echo json_encode($categories_arr);
            }
        } else {
            echo json_encode(["message" => "category_id Not Found"]);
        }
    }

    private function handlePost() {
        $data = json_decode(file_get_contents("php://input"));
        if (!isset($data->category)) {
            echo json_encode(["message" => "Missing Required Parameters"]);
            return;
        }
        $this->categoryModel->category = $data->category;
        try {
            if ($this->categoryModel->create()) {
                echo json_encode([
                    "id" => $this->categoryModel->id,
                    "category" => $data->category
                ]);
            } else {
                echo json_encode(["message" => "Category Not Created"]);
            }
        } catch (PDOException $e) {
            echo json_encode(["message" => $e->getMessage()]);
        }
    }

    private function handlePut() {
        $data = json_decode(file_get_contents("php://input"));
        if (!isset($data->id) || !isset($data->category)) {
            echo json_encode(["message" => "Missing Required Parameters"]);
            return;
        }
        $this->categoryModel->id = $data->id;
        $this->categoryModel->category = $data->category;
        try {
            if ($this->categoryModel->update()) {
                echo json_encode([
                    "id" => $data->id,
                    "category" => $data->category
                ]);
            } else {
                echo json_encode(["message" => "category_id Not Found"]);
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
        $this->categoryModel->id = $_GET['id'];
        try {
            if ($this->categoryModel->delete()) {
                echo json_encode(["id" => $_GET['id']]);
            } else {
                echo json_encode(["message" => "category_id Not Found"]);
            }
        } catch (PDOException $e) {
            echo json_encode(["message" => $e->getMessage()]);
        }
    }
}
