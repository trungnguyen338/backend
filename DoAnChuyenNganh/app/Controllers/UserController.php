<?php
require_once "./app/Service/UserService.php";
require_once "./vendor/autoload.php";

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

class UserController
{
    private $userService;
    private $secretKey = "your_secret_key_here";

    public function __construct()
    {
        $this->userService = new UserService();
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

    // ADMIN METHODS
    public function index()
    {
        $user = $this->getUserFromToken();
        if (!$user || $user->role !== 'admin') {
            echo json_encode(["error" => "Unauthorized"]);
            return;
        }
        echo json_encode($this->userService->getAll());
    }

    public function id($id)
    {
        $user = $this->getUserFromToken();
        if (!$user || $user->role !== 'admin') {
            echo json_encode(["error" => "Unauthorized"]);
            return;
        }
        echo json_encode($this->userService->getById($id));
    }

    public function update($id)
    {
        $user = $this->getUserFromToken();
        if (!$user || $user->role !== 'admin') {
            echo json_encode(["error" => "Unauthorized"]);
            return;
        }

        $body = json_decode(file_get_contents("php://input"), true);
        $ok = $this->userService->update($id, $body);

        echo json_encode(["success" => $ok]);
    }

    public function delete($id)
    {
        $user = $this->getUserFromToken();
        if (!$user || $user->role !== 'admin') {
            echo json_encode(["error" => "Unauthorized"]);
            return;
        }

        $ok = $this->userService->delete($id);
        echo json_encode(["success" => $ok]);
    }
}
