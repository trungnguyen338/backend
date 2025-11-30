<?php
require_once("./app/Service/CartService.php");
require_once("./vendor/autoload.php"); // nếu dùng firebase/php-jwt
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

header("Access-Control-Allow-Origin: http://localhost:5173");
header("Access-Control-Allow-Credentials: true");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

class CartController
{
    private $cartService;
    private $secretKey = "your_secret_key_here"; // đặt key giống AuthService

    public function __construct()
    {
        $this->cartService = new CartService();
    }

    // Hàm giải mã JWT
    private function getUserIdFromToken()
    {
        $headers = getallheaders();
        if (!isset($headers['Authorization'])) return null;

        $matches = [];
        if (!preg_match('/Bearer\s(\S+)/', $headers['Authorization'], $matches)) return null;

        $token = $matches[1];

        try {
            $decoded = JWT::decode($token, new Key($this->secretKey, 'HS256'));
            return $decoded->user_id ?? null;
        } catch (Exception $e) {
            return null;
        }
    }

    // Xem giỏ hàng
    public function index()
    {
        $user_id = $this->getUserIdFromToken();
        if (!$user_id) {
            echo json_encode(["error" => "Chưa đăng nhập"]);
            return;
        }

        echo json_encode($this->cartService->getCart($user_id));
    }

    // Thêm variant vào giỏ
    public function addToCart()
    {
        $user_id = $this->getUserIdFromToken();
        $body = json_decode(file_get_contents("php://input"), true);
        $product_variant_id = $body['product_variant_id'] ?? null;
        $quantity = $body['quantity'] ?? 1;

        if (!$user_id) {
            echo json_encode(["error" => "Chưa đăng nhập"]);
            return;
        }
        if (!$product_variant_id) {
            echo json_encode(["error" => "Thiếu product_variant_id"]);
            return;
        }

        echo json_encode($this->cartService->addToCart($user_id, $product_variant_id, $quantity));
    }

    public function updateItem()
    {
        $body = json_decode(file_get_contents("php://input"), true);
        $item_id = $body['item_id'] ?? null;
        $quantity = $body['quantity'] ?? 1;

        if (!$item_id) {
            echo json_encode(["error" => "Thiếu item_id"]);
            return;
        }

        $success = $this->cartService->updateItem($item_id, $quantity);
        echo json_encode(["success" => $success]);
    }

    public function removeItem()
    {
        $body = json_decode(file_get_contents("php://input"), true);
        $item_id = $body['item_id'] ?? null;

        if (!$item_id) {
            echo json_encode(["error" => "Thiếu item_id"]);
            return;
        }

        $success = $this->cartService->removeItem($item_id);
        echo json_encode(["success" => $success]);
    }

    public function clearCart()
    {
        $user_id = $this->getUserIdFromToken();
        if (!$user_id) {
            echo json_encode(["error" => "Chưa đăng nhập"]);
            return;
        }

        $success = $this->cartService->clearCart($user_id);
        echo json_encode(["success" => $success]);
    }

    public function syncCart()
    {
        $user_id = $this->getUserIdFromToken();
        if (!$user_id) {
            echo json_encode(["error" => "Chưa đăng nhập"]);
            return;
        }

        $body = json_decode(file_get_contents("php://input"), true);
        $items = $body["items"] ?? [];

        echo json_encode($this->cartService->syncCart($user_id, $items));
    }
}
