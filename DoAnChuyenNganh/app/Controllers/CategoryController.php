<?php
require_once("./app/Service/CategoryService.php");
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

class CategoryController
{
    private $categoryService;
    private $secretKey = "your_secret_key_here"; // giống AuthService

    public function __construct()
    {
        $this->categoryService = new CategoryService();
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

    private function isAdmin()
    {
        $user = $this->getUserFromToken();
        return $user && ($user->role ?? '') === 'admin';
    }

    public function index()
    {
        echo json_encode($this->categoryService->getAll());
    }

    public function getById($id)
    {
        echo json_encode($this->categoryService->getById($id));
    }

    public function create($name, $description)
    {
        if (!$this->isAdmin()) {
            echo json_encode(["error" => "Không có quyền"]);
            return;
        }
        echo json_encode($this->categoryService->create($name, $description));
    }

    public function update($id, $name, $description)
    {
        if (!$this->isAdmin()) {
            echo json_encode(["error" => "Không có quyền"]);
            return;
        }
        echo json_encode($this->categoryService->update($id, $name, $description));
    }

    public function delete($id)
    {
        if (!$this->isAdmin()) {
            echo json_encode(["error" => "Không có quyền"]);
            return;
        }
        echo json_encode($this->categoryService->delete($id));
    }
}
