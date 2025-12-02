<?php
require_once("./app/Service/ShipProviderService.php");
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

class ShipProviderController
{
    private $shipService;
    private $secretKey = "your_secret_key_here";

    public function __construct()
    {
        $this->shipService = new ShipProviderService();
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

    // API đọc dữ liệu, không cần JWT
    public function index()
    {
        echo json_encode($this->shipService->getAll());
    }
    public function getById($id)
    {
        echo json_encode($this->shipService->getById($id));
    }
    public function getprice($id)
    {
        echo json_encode($this->shipService->getprice($id));
    }

    // API quản trị cần JWT admin
    public function insert()
    {
        $user = $this->getUserFromToken();
        if (!$user || $user->role !== 'admin') {
            echo json_encode(["error" => "Unauthorized"]);
            return;
        }
        $body = json_decode(file_get_contents("php://input"), true);
        echo json_encode($this->shipService->insert($body));
    }

    public function update()
    {
        $user = $this->getUserFromToken();
        if (!$user || $user->role !== 'admin') {
            echo json_encode(["error" => "Unauthorized"]);
            return;
        }
        $body = json_decode(file_get_contents("php://input"), true);
        echo json_encode($this->shipService->update($body));
    }

    public function delete($id)
    {
        $user = $this->getUserFromToken();
        if (!$user || $user->role !== 'admin') {
            echo json_encode(["error" => "Unauthorized"]);
            return;
        }
        echo json_encode($this->shipService->delete($id));
    }
}
