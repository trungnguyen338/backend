<?php
namespace App\Controllers;

use App\Models\UserModel;
use App\Services\AuthService;
use Exception;

class AuthController
{
    private $authService;

    public function __construct()
    {
        $userModel = new UserModel();
        $this->authService = new AuthService($userModel);
        
        $this->setCorsHeaders();
    }

    private function setCorsHeaders() {
        header("Access-Control-Allow-Origin: http://localhost:5173");
        header("Access-Control-Allow-Credentials: true");
        header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
        header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");
    }

    public function login()
    {
        // Xử lý preflight request của trình duyệt
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            http_response_code(200);
            exit();
        }

        $data = json_decode(file_get_contents("php://input"), true);
        $username = $data['username'] ?? '';
        $password = $data['password'] ?? '';

        try {
            $result = $this->authService->login($username, $password);

            echo json_encode([
                "success" => true,
                "message" => "Đăng nhập thành công",
                "data" => $result
            ]);

        } catch (Exception $e) {
           
            http_response_code(400); 
            echo json_encode([
                "success" => false,
                "message" => $e->getMessage()
            ]);
        }
    }


    public function register()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            http_response_code(200);
            exit();
        }

        $data = json_decode(file_get_contents("php://input"), true);

        try {
            $this->authService->register(
                $data['username'],
                $data['password'],
                $data['repassword'],
                $data['email'],
                $data['phone']
            );

            http_response_code(201); // 201 Created
            echo json_encode([
                "success" => true,
                "message" => "Đăng ký thành công"
            ]);

        } catch (Exception $e) {
            http_response_code(400);
            echo json_encode([
                "success" => false,
                "message" => $e->getMessage()
            ]);
        }
    }
    public function logout()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405); 
            echo json_encode(["status" => "error", "message" => "Phương thức không được hỗ trợ. Vui lòng dùng POST."]);
            return;
        }

        try {
            $this->authService->logout();

            if (isset($_COOKIE['token'])) {
                setcookie('token', '', time() - 3600, '/'); 
            }
            if (isset($_COOKIE['PHPSESSID'])) {
                setcookie('PHPSESSID', '', time() - 3600, '/');
            }

            http_response_code(200);
            echo json_encode([
                "status" => "success",
                "message" => "Đăng xuất thành công."
            ]);

        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                "status" => "error",
                "message" => "Lỗi đăng xuất: " . $e->getMessage()
            ]);
        }
    }
}