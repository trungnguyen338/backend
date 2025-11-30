<?php

header("Access-Control-Allow-Headers: Content-Type");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Origin: http://localhost:5173");
header("Access-Control-Allow-Credentials: true");
require_once "app/Controller/ProductController.php";
require_once "app/Controller/CategoryController.php";
require_once "app/Controller/CartController.php";
require_once "app/Controller/OrderController.php";
require_once "app/Controller/PaymentController.php";
require_once "app/Controller/ShipController.php";
require_once "app/Controller/AuthController.php";
require_once "app/Controller/SubcategoryController.php";
require_once "app/Controller/UserController.php";
require_once "./core/Session.php";


if (isset($_GET['url'])) {
    $parts = explode('/', trim($_GET['url'], '/'));
    $controllerName = $parts[0] ?? 'product';
    $action = $parts[1] ?? 'index';
    $id = $parts[2] ?? null;
} else {
    $controllerName = $_GET['controller'] ?? 'product';
    $action = $_GET['action'] ?? 'index';
    $id = $_GET['id'] ?? null;
}

$controllers = [
    "product" => ProductController::class,
    "category" => CategoryController::class,
    "cart" => CartController::class,
    "order" => OrderController::class,
    "payment" => PaymentController::class,
    "ship" => ShipProviderController::class,
    "auth" => AuthController::class,
    "subcategory" => SubcategoryController::class,
    "user" => UserController::class
];

if (!isset($controllers[$controllerName])) {
    die(json_encode(["error" => "Controller not found"]));
}

$controller = new $controllers[$controllerName]();

// Check if method exists
if (!method_exists($controller, $action)) {
    die(json_encode(["error" => "Method '$action' not found in " . get_class($controller)]));
}

if ($id !== null) {
    $controller->$action($id);
} else {
    $controller->$action();
}
