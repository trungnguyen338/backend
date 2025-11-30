<?php
require_once "./vendor/autoload.php";

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class AuthMiddleware
{
    private static $secretKey = "your_secret_key_here"; // Phải giống AuthService

    /**
     * Kiểm tra token JWT hợp lệ
     * @return object payload của user
     */
    public static function handle()
    {
        $headers = getallheaders();
        $authHeader = $headers['Authorization'] ?? $headers['authorization'] ?? null;

        if (!$authHeader) {
            http_response_code(401);
            echo json_encode(["success" => false, "message" => "Thiếu token"]);
            exit();
        }

        // Header phải dạng: Bearer <token>
        if (!preg_match('/Bearer\s(\S+)/', $authHeader, $matches)) {
            http_response_code(401);
            echo json_encode(["success" => false, "message" => "Token không hợp lệ"]);
            exit();
        }

        $jwt = $matches[1];

        try {
            $decoded = JWT::decode($jwt, new Key(self::$secretKey, 'HS256'));
        } catch (\Firebase\JWT\ExpiredException $e) {
            http_response_code(401);
            echo json_encode(["success" => false, "message" => "Token đã hết hạn"]);
            exit();
        } catch (Exception $e) {
            http_response_code(401);
            echo json_encode(["success" => false, "message" => "Token không hợp lệ"]);
            exit();
        }

        // Trả về payload user cho controller dùng
        return $decoded;
    }
}
