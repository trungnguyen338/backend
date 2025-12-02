<?php
require_once("./app/Service/ProductService.php");
require_once("./vendor/autoload.php"); // firebase/php-jwt
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

class ProductController
{
    private $productService;
    private $secretKey = "your_secret_key_here";

    public function __construct()
    {
        $this->productService = new ProductService();
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

    // Lấy tất cả sản phẩm (không cần JWT)
    public function index()
    {
        echo json_encode($this->productService->getAll());
    }

    // Lấy sản phẩm theo id (không cần JWT)
    public function getid($id)
    {
        echo json_encode($this->productService->getById($id));
    }

    // Lấy sản phẩm theo subcategory (không cần JWT)
    public function getbysubcategory($id)
    {
        echo json_encode($this->productService->getAllBySubcategory($id));
    }

    // Tạo sản phẩm mới (cần JWT admin)
    public function create()
    {
        $user = $this->getUserFromToken();
        if (!$user || $user->role !== 'admin') {
            echo json_encode(["error" => "Unauthorized"]);
            return;
        }

        $body = json_decode(file_get_contents("php://input"), true);
        echo json_encode($this->productService->create($body));
    }

    // Cập nhật sản phẩm (cần JWT admin)
    public function update()
    {
        $user = $this->getUserFromToken();
        if (!$user || $user->role !== 'admin') {
            echo json_encode(["error" => "Unauthorized"]);
            return;
        }

        $body = json_decode(file_get_contents("php://input"), true);
        echo json_encode($this->productService->update($body));
    }

    // Xóa sản phẩm (cần JWT admin)
    public function delete($id)
    {
        $user = $this->getUserFromToken();
        if (!$user || $user->role !== 'admin') {
            echo json_encode(["error" => "Unauthorized"]);
            return;
        }

        echo json_encode($this->productService->delete($id));
    }
}
