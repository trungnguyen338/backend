<?php
namespace App\Services;

use App\Models\UserModel;
use Firebase\JWT\JWT;
use Exception;

class AuthService
{
    private $userModel;
    // Lưu ý: Key này nên để trong file config hoặc biến môi trường, tạm thời để đây để test
    private $secretKey = "KEY_BI_MAT_CUA_SHOP_THOI_TRANG_123";

    // --- SỬA ĐOẠN NÀY (Dependency Injection) ---
    // Phải nhận tham số $userModel từ bên ngoài truyền vào
    public function __construct(UserModel $userModel)
    {
        $this->userModel = $userModel;
    }
    // -------------------------------------------

    public function register($username, $password, $repassword, $email, $phone)
    {
        // 1. Validate dữ liệu đầu vào
        if ($password !== $repassword) {
            throw new Exception("Mật khẩu nhập lại không khớp.");
        }

        // Dùng hàm chuẩn PHP để check email
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new Exception("Email không hợp lệ.");
        }

        // Check số điện thoại (Regex đơn giản cho VN)
        if (!preg_match('/^(03|05|07|08|09)+([0-9]{8})$/', $phone)) {
            throw new Exception("Số điện thoại không hợp lệ.");
        }

        // 2. Check logic Database
        if ($this->userModel->findByUsername($username)) {
            throw new Exception("Tên người dùng đã tồn tại.");
        }

        // 3. Hash password
        $hash = password_hash($password, PASSWORD_DEFAULT);

        // 4. Tạo user
        $isCreated = $this->userModel->create($username, $hash, $email, $phone);

        if (!$isCreated) {
            throw new Exception("Lỗi hệ thống, không thể tạo tài khoản.");
        }

        return true;
    }

    public function login($username, $password)
    {
        $user = $this->userModel->findByUsername($username);

        // Check User tồn tại
        if (!$user) {
            throw new Exception("Tài khoản hoặc mật khẩu không chính xác.");
        }

        // Check Password
        if (!password_verify($password, $user['password'])) {
            throw new Exception("Tài khoản hoặc mật khẩu không chính xác.");
        }

        // Tạo Payload cho JWT
        $payload = [
            "iss" => "http://localhost:8000",
            "iat" => time(),
            "exp" => time() + (60 * 60 * 24), // 1 ngày
            "data" => [
                "id" => $user["id"],
                "username" => $user["username"],
                "role" => $user["role"] ?? "user"
            ]
        ];

        // Encode JWT
        $jwt = JWT::encode($payload, $this->secretKey, 'HS256');

        return [
            "token" => $jwt,
            "user_info" => $payload['data']
        ];
    }
    public function logout()
    {
        return true;
    }
   
}