<?php
require_once("./app/Service/OrderService.php");
require_once("./vendor/autoload.php"); // firebase/php-jwt
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

header("Access-Control-Allow-Origin: http://localhost:5173");
header("Access-Control-Allow-Credentials: true");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

class OrderController
{
    private $orderService;
    private $secretKey = "your_secret_key_here"; // giống AuthService

    public function __construct()
    {
        $this->orderService = new OrderService();
    }

    private function getUserFromToken()
    {
        $headers = getallheaders();
        if (!isset($headers['Authorization'])) return null;

        if (!preg_match('/Bearer\s(\S+)/', $headers['Authorization'], $matches)) return null;

        $token = $matches[1];

        try {
            $decoded = JWT::decode($token, new Key($this->secretKey, 'HS256'));
            return $decoded;
        } catch (Exception $e) {
            return null;
        }
    }

    /*** ADMIN METHODS ***/
    public function index()
    {
        echo json_encode($this->orderService->getAllOrders());
    }

    public function getOrder($order_id)
    {
        echo json_encode($this->orderService->getOrder($order_id));
    }

    public function getOrdersByUser($user_id)
    {
        echo json_encode($this->orderService->getOrdersByUser($user_id));
    }

    public function create()
    {
        $user = $this->getUserFromToken();
        if (!$user) {
            echo json_encode(["error" => "Chưa đăng nhập"]);
            return;
        }

        $body = json_decode(file_get_contents("php://input"), true);

        echo json_encode($this->orderService->createOrder(
            $user->id,
            $body["total"] ?? 0,
            $body["status"] ?? "pending",
            $body["shipping_id"] ?? null,
            $body["delivery_status"] ?? "pending"
        ));
    }

    public function addItem()
    {
        $body = json_decode(file_get_contents("php://input"), true);
        echo json_encode($this->orderService->addItem(
            $body["order_id"],
            $body["product_variant_id"],
            $body["quantity"],
            $body["price"]
        ));
    }

    public function deleteItem($item_id)
    {
        echo json_encode($this->orderService->deleteItem($item_id));
    }

    public function clearItems($order_id)
    {
        echo json_encode($this->orderService->clearOrderItems($order_id));
    }

    public function updateStatus()
    {
        $body = json_decode(file_get_contents("php://input"), true);
        echo json_encode($this->orderService->updateStatus(
            $body["order_id"],
            $body["status"]
        ));
    }

    public function updateDelivery()
    {
        $body = json_decode(file_get_contents("php://input"), true);
        echo json_encode($this->orderService->updateDeliveryStatus(
            $body["order_id"],
            $body["delivery_status"]
        ));
    }

    public function updateShipping()
    {
        $body = json_decode(file_get_contents("php://input"), true);
        echo json_encode($this->orderService->updateShipping(
            $body["order_id"],
            $body["shipping_id"]
        ));
    }

    public function updateTotal()
    {
        $body = json_decode(file_get_contents("php://input"), true);
        echo json_encode($this->orderService->updateTotal(
            $body["order_id"],
            $body["total"]
        ));
    }

    // user thêm địa chỉ nhận hàng mới
    public function address()
    {
        $user = $this->getUserFromToken();
        if (!$user) {
            echo json_encode(["error" => "Chưa đăng nhập"]);
            return;
        }

        $body = json_decode(file_get_contents("php://input"), true);

        echo json_encode($this->orderService->addUserAddress(
            $user->id,
            $body['address'] ?? '',
            $body['phone'] ?? '',
            $body['is_default'] ?? 0
        ));
    }

    // Xem giỏ hàng checkout trước khi confirm
    public function checkout()
    {
        $user = $this->getUserFromToken();
        if (!$user) {
            echo json_encode(["status" => "error", "message" => "Chưa đăng nhập"]);
            return;
        }

        $items = $this->orderService->getCartCheckout($user->id);
        echo json_encode($items);
    }

    // Xác nhận đơn hàng
    public function confirm()
    {
        $user = $this->getUserFromToken();
        if (!$user) {
            echo json_encode(["status" => "error", "message" => "Chưa đăng nhập"]);
            return;
        }

        $body = json_decode(file_get_contents("php://input"), true);

        echo json_encode($this->orderService->confirmOrder(
            $user->id,
            $body["shipping_id"],
            $body["payment_method"] ?? 'cod',
            $body["address_id"]
        ));
    }
}
