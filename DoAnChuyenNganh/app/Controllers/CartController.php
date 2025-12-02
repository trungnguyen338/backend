<?php
namespace App\Controllers;

use App\Services\CartService;
use Exception;

class CartController {
    private $cartService;
    private $userId; // User ID của người đang đăng nhập

    public function __construct(CartService $cartService) {
        $this->cartService = $cartService;
    }

    // Setter để index.php truyền User ID vào sau khi check Token
    public function setUser($user) {
        $this->userId = $user->id; // $user lấy từ JWT
    }

    // GET: /cart
    public function index() {
        try {
            $items = $this->cartService->getCart($this->userId);
            echo json_encode([
                "status" => "success",
                "data" => $items
            ]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(["status" => "error", "message" => $e->getMessage()]);
        }
    }

    // POST: /cart/add
    public function add() {
        $data = json_decode(file_get_contents("php://input"), true);
        
        if (!isset($data['variant_id']) || !isset($data['quantity'])) {
            http_response_code(400);
            echo json_encode(["status" => "error", "message" => "Thiếu thông tin sản phẩm"]);
            return;
        }

        try {
            $this->cartService->addToCart($this->userId, $data['variant_id'], $data['quantity']);
            echo json_encode(["status" => "success", "message" => "Đã thêm vào giỏ hàng"]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(["status" => "error", "message" => $e->getMessage()]);
        }
    }

    // DELETE: /cart/remove?id=123
    public function remove() {
        $itemId = $_GET['id'] ?? null;
        if (!$itemId) {
            http_response_code(400);
            echo json_encode(["status" => "error", "message" => "Thiếu ID sản phẩm"]);
            return;
        }
        
        $this->cartService->removeItem($itemId);
        echo json_encode(["status" => "success", "message" => "Đã xóa sản phẩm"]);
    }
    public function sync() {
        $data = json_decode(file_get_contents("php://input"), true);
        $guestItems = $data['items'] ?? [];

        try {
            $newCart = $this->cartService->syncGuestCart($this->userId, $guestItems);
            echo json_encode(["status" => "success", "message" => "Đồng bộ thành công", "data" => $newCart]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(["status" => "error", "message" => $e->getMessage()]);
        }
    }
}