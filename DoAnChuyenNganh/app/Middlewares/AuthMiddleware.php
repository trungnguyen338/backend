<?php
namespace App\Middlewares;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Exception;

class AuthMiddleware {
    private $secretKey = "KEY_BI_MAT_CUA_SHOP_THOI_TRANG_123"; 

    public function isAuth() {
        $headers = apache_request_headers();
        $authHeader = null;

        if (isset($headers['Authorization'])) {
            $authHeader = $headers['Authorization'];
        } elseif (isset($_SERVER['HTTP_AUTHORIZATION'])) {
            $authHeader = $_SERVER['HTTP_AUTHORIZATION'];
        }

        if (!$authHeader || !preg_match('/Bearer\s(\S+)/', $authHeader, $matches)) {
            http_response_code(401);
            echo json_encode(["message" => "Vui lòng đăng nhập (Thiếu Token)"]);
            exit(); 
        }

        $jwt = $matches[1]; 

        try {
            $decoded = JWT::decode($jwt, new Key($this->secretKey, 'HS256'));

            return $decoded->data; 

        } catch (Exception $e) {
            http_response_code(401);
            echo json_encode([
                "message" => "Token không hợp lệ hoặc đã hết hạn",
                "error" => $e->getMessage()
            ]);
            exit();
        }
    }
}