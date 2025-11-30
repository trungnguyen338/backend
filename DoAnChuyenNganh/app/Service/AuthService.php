<?php
require_once "./app/Model/UserModel.php";
require_once("./app/Middleware/ValidationMiddleware.php");
require_once "./vendor/autoload.php"; // JWT library
use Firebase\JWT\JWT;

header("Access-Control-Allow-Origin: http://localhost:5173");
header("Access-Control-Allow-Credentials: true");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");

class AuthService
{
    private $userModel;
    private $secretKey = "your_secret_key_here"; // nhớ đổi key này

    public function __construct()
    {
        $this->userModel = new UserModel();
    }

    // Đăng ký
    public function register($username, $password, $repassword, $email, $phone)
    {
        if ($this->userModel->findByUsername($username)) {
            return ["success" => false, "message" => "Tên người dùng đã tồn tại!"];
        }
        if ($password !== $repassword) {
            return ["success" => false, "message" => "Mật khẩu không trùng!"];
        }
        if (!isEmail($email)) {
            return ["success" => false, "message" => "Email không hợp lệ!"];
        }
        if (!isValidPhone($phone)) {
            return ["success" => false, "message" => "Số điện thoại không hợp lệ!"];
        }

        $hash = hashPassword($password, PASSWORD_BCRYPT);
        $ok = $this->userModel->create($username, $hash, $email, $phone);

        return ["success" => $ok];
    }

    // Đăng nhập
    public function login($username, $password)
    {
        $user = $this->userModel->findByUsername($username);
        if (!$user) {
            return ["success" => false, "message" => "Không tìm thấy tên đăng nhập!"];
        }

        if (!verifyPassword($password, $user["password"])) {
            return ["success" => false, "message" => "Sai mật khẩu!"];
        }

        // Tạo JWT
        $payload = [
            "id" => $user["id"],
            "username" => $user["username"],
            "role" => $user["role"] ?? "user",
            "iat" => time(),
            "exp" => time() + (60 * 60 * 24) // 1 ngày
        ];

        $jwt = JWT::encode($payload, $this->secretKey, 'HS256');

        return ["success" => true, "token" => $jwt, "user" => $payload];
    }

    // Đăng xuất
    public function logout()
    {
        // Với JWT, logout là frontend xóa token
        return ["success" => true];
    }
}
