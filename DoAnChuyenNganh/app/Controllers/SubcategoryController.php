<?php
require_once("./app/Service/SubCategoryService.php");
require_once("./vendor/autoload.php");

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

class SubcategoryController
{
    private $subcategoryService;
    private $secretKey = "your_secret_key_here";

    public function __construct()
    {
        $this->subcategoryService = new SubcategoryService();
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

    // API đọc dữ liệu
    public function index()
    {
        echo json_encode($this->subcategoryService->getAll());
    }
    public function getById($id)
    {
        echo json_encode($this->subcategoryService->getById($id));
    }
    public function getByCategory($category_id)
    {
        echo json_encode($this->subcategoryService->getByCategory($category_id));
    }

    // API quản trị cần JWT admin
    public function create()
    {
        $user = $this->getUserFromToken();
        if (!$user || $user->role !== 'admin') {
            echo json_encode(["error" => "Unauthorized"]);
            return;
        }

        $body = json_decode(file_get_contents("php://input"), true);
        $name = $body['name'] ?? null;
        $category_id = $body['category_id'] ?? null;

        echo json_encode($this->subcategoryService->create($name, $category_id));
    }

    public function update($id)
    {
        $user = $this->getUserFromToken();
        if (!$user || $user->role !== 'admin') {
            echo json_encode(["error" => "Unauthorized"]);
            return;
        }

        $body = json_decode(file_get_contents("php://input"), true);
        $name = $body['name'] ?? null;
        $category_id = $body['category_id'] ?? null;

        echo json_encode($this->subcategoryService->update($id, $name, $category_id));
    }

    public function delete($id)
    {
        $user = $this->getUserFromToken();
        if (!$user || $user->role !== 'admin') {
            echo json_encode(["error" => "Unauthorized"]);
            return;
        }

        echo json_encode($this->subcategoryService->delete($id));
    }
}
