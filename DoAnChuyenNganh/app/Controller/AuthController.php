<?php
require_once "./app/Service/AuthService.php";
header("Access-Control-Allow-Origin: http://localhost:5173");
header("Access-Control-Allow-Credentials: true");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");
header("Content-Type: application/json; charset=UTF-8");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

class AuthController
{
    private $authService;

    public function __construct()
    {
        $this->authService = new AuthService();
    }

    public function register()
    {
        $body = json_decode(file_get_contents("php://input"), true);
        if (!$body) {
            echo json_encode(["error" => "Invalid JSON body"]);
            return;
        }

        $res = $this->authService->register(
            $body["username"],
            $body["password"],
            $body["repassword"],
            $body["email"] ?? null,
            $body["phone"] ?? null
        );

        echo json_encode($res);
    }

    // --- Sửa login để trả JWT ---
    public function login()
    {
        $body = json_decode(file_get_contents("php://input"), true);

        $res = $this->authService->login(
            $body["username"],
            $body["password"]
        );

        // Nếu login thành công, AuthService trả user info + token
        if (isset($res['status']) && $res['status'] === 'success') {
            echo json_encode([
                "status" => "success",
                "user" => $res['user'],    // thông tin user
                "token" => $res['token']   // JWT token
            ]);
        } else {
            echo json_encode($res);
        }
    }

    // --- Logout bây giờ frontend chỉ xóa token ---
    public function logout()
    {
        // Không cần destroy session nữa
        echo json_encode(["success" => true]);
    }
}
