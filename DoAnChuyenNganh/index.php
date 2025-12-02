<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require __DIR__ . '/vendor/autoload.php';

use App\Core\Database;
use App\Models\UserModel;
use App\Services\AuthService;
use App\Controllers\AuthController;
use App\Middlewares\AuthMiddleware;

header("Access-Control-Allow-Origin: http://localhost:5173");
header("Access-Control-Allow-Credentials: true");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");
header("Content-Type: application/json; charset=UTF-8"); // Luôn trả về JSON

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit();
}

try {
    $requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    $path = trim($requestUri, '/'); 

   
    $parts = !empty($path) ? explode('/', $path) : [];

    $controllerName = $parts[0] ?? 'product'; 
    $action = $parts[1] ?? 'index';           
    $id = $parts[2] ?? null;

    $protectedControllers = ['cart', 'order', 'payment', 'user', 'profile'];
    
    if (in_array($controllerName, $protectedControllers)) {
        $middleware = new AuthMiddleware();
        $currentUser = $middleware->isAuth(); 
    }

    $db = new Database(); 

    $controller = null;

    switch ($controllerName) {
        case 'auth':
            $userModel = new UserModel(); 
            $authService = new AuthService($userModel); 
            $controller = new AuthController($authService); 
            break;
        case 'cart':
            $cartModel = new \App\Models\CartModel();
            $cartService = new \App\Services\CartService($cartModel);
            $controller = new \App\Controllers\CartController($cartService);
                
            if (isset($currentUser)) {
                    $controller->setUser($currentUser);
            
            }else {
                     http_response_code(401);
                     echo json_encode(["message" => "Unauthorized"]);
                     exit;
                }
            break;
            case 'order':
                $cartModel = new \App\Models\CartModel();
                $orderService = new \App\Services\OrderService($cartModel); 
                $controller = new \App\Controllers\OrderController($orderService);
                
                if (isset($currentUser)) {
                    $controller->setUser($currentUser);
                } else {
                     // Chặn nếu chưa login
                     http_response_code(401);
                     echo json_encode(["message" => "Unauthorized"]);
                     exit;
                }
                break;
    
        

        default:
            $className = "App\\Controllers\\" . ucfirst($controllerName) . "Controller";
            
            if (class_exists($className)) {
                $controller = new $className();
            } else {
                throw new Exception("Controller '$controllerName' not found");
            }
            break;
    }

    if (method_exists($controller, $action)) {
        if ($id !== null) {
            $controller->$action($id);
        } else {
            $controller->$action();
        }
    } else {
        throw new Exception("Method '$action' not found in " . get_class($controller));
    }

} catch (Exception $e) {
    $statusCode = 500;
    if ($e->getMessage() == "Controller '$controllerName' not found") $statusCode = 404;
    
    http_response_code($statusCode);
    echo json_encode([
        "status" => "error",
        "message" => $e->getMessage()
    ]);
}