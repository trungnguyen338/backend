<?php
require_once("./app/Service/PaymentService.php");
require_once("./vendor/autoload.php"); // firebase/php-jwt
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

class PaymentController
{
    private $paymentService;
    private $secretKey = "your_secret_key_here"; // phải giống AuthService

    public function __construct()
    {
        $this->paymentService = new PaymentService();
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

    public function getAllPayments()
    {
        // Nếu cần phân quyền user/admin, có thể check token ở đây
        echo json_encode($this->paymentService->getAllPayments());
    }

    public function getPaymentById($id)
    {
        echo json_encode($this->paymentService->getPaymentById($id));
    }

    public function getPaymentByOrderId($order_id)
    {
        $user = $this->getUserFromToken();
        if (!$user) {
            echo json_encode(["error" => "Chưa đăng nhập"]);
            return;
        }

        echo json_encode($this->paymentService->getPaymentByOrderId($order_id, $user->id));
    }

    public function addPayment($order_id, $method = 'cod', $status = 'pending', $paid_at = null)
    {
        $user = $this->getUserFromToken();
        if (!$user) {
            echo json_encode(["error" => "Chưa đăng nhập"]);
            return;
        }

        echo json_encode($this->paymentService->addPayment($order_id, $method, $status, $paid_at, $user->id));
    }

    public function updatePaymentStatus($id, $status, $paid_at = null)
    {
        $user = $this->getUserFromToken();
        if (!$user) {
            echo json_encode(["error" => "Chưa đăng nhập"]);
            return;
        }

        echo json_encode($this->paymentService->updatePaymentStatus($id, $status, $paid_at, $user->id));
    }

    public function deletePayment($id)
    {
        $user = $this->getUserFromToken();
        if (!$user) {
            echo json_encode(["error" => "Chưa đăng nhập"]);
            return;
        }

        echo json_encode($this->paymentService->deletePayment($id, $user->id));
    }
}
