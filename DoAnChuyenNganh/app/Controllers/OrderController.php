<?php
namespace App\Controllers;

use App\Services\OrderService;
use Exception;

class OrderController {
    private $orderService;
    private $userId;

    public function __construct(OrderService $orderService) {
        $this->orderService = $orderService;
    }

    public function setUser($user) {
        $this->userId = $user->id;
    }

    public function checkoutInfo() {
        try {
            $data = $this->orderService->getCheckoutInfo($this->userId);
            echo json_encode(["status" => "success", "data" => $data]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(["status" => "error", "message" => $e->getMessage()]);
        }
    }

    // POST /order/place
    public function place() {
        $data = json_decode(file_get_contents("php://input"), true);
        
        // Validate đơn giản
        if (empty($data['address_id']) || empty($data['shipping_id'])) {
            http_response_code(400);
            echo json_encode(["status" => "error", "message" => "Vui lòng chọn địa chỉ và vận chuyển"]);
            return;
        }

        try {
            $orderId = $this->orderService->createOrder(
                $this->userId, 
                $data['address_id'], 
                $data['shipping_id'],
                $data['payment_method'] ?? 'cod'
            );
            echo json_encode(["status" => "success", "message" => "Đặt hàng thành công", "order_id" => $orderId]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(["status" => "error", "message" => $e->getMessage()]);
        }
    }
}