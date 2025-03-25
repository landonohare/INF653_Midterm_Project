<?php
// index.php

header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');

$method = $_SERVER['REQUEST_METHOD'];

// Handle preflight OPTIONS request for CORS
if ($method === 'OPTIONS') {
    header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
    header('Access-Control-Allow-Headers: Origin, Accept, Content-Type, X-Requested-With');
    exit();
}

// Parse the URL into segments
$request = trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');
$routeParts = explode('/', $request);

// If the user is at the root (e.g., http://localhost/), show a welcome message
if (!isset($routeParts[0]) || $routeParts[0] === '') {
    echo "Welcome to my Quotes API!";
    exit();
}

// If the first segment isn't 'api', it's not a valid API request
if ($routeParts[0] !== 'api') {
    echo json_encode(["message" => "Invalid API Request"]);
    exit();
}

// If there's no second segment to indicate the resource, show an error
if (count($routeParts) < 2) {
    echo json_encode(["message" => "Invalid Resource"]);
    exit();
}

$resource = $routeParts[1];

switch ($resource) {
    case 'quotes':
        require_once 'controllers/QuoteController.php';
        $controller = new QuoteController();
        $controller->processRequest($method, $_GET);
        break;
    case 'authors':
        require_once 'controllers/AuthorController.php';
        $controller = new AuthorController();
        $controller->processRequest($method, $_GET);
        break;
    case 'categories':
        require_once 'controllers/CategoryController.php';
        $controller = new CategoryController();
        $controller->processRequest($method, $_GET);
        break;
    default:
        echo json_encode(["message" => "Invalid Resource"]);
        break;
}
