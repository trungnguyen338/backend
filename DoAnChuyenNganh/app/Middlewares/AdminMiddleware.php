<?php
require_once "./AuthMiddleware.php";

class AdminMiddleware
{
    public static function handle()
    {
        $user = AuthMiddleware::handle(); // Kiểm tra token trước
        if (($user->role ?? "") !== "admin") {
            http_response_code(403);
            echo json_encode(["success" => false, "message" => "Không có quyền truy cập"]);
            exit();
        }
        return $user; // trả về payload admin
    }
}
