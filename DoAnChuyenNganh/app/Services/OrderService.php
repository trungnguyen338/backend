<?php
namespace App\Services;

use App\Core\Database;
use App\Models\CartModel;
use App\Models\ShipModel;
use PDO;
use Exception;

class OrderService {
    private $db;
    private $cartModel;
    private $shipModel;

    // Inject CartModel để xử lý giỏ hàng
    // Tự khởi tạo ShipModel và DB bên trong
    public function __construct(CartModel $cartModel) {
        $this->db = new Database();
        $this->cartModel = $cartModel;
        $this->shipModel = new ShipModel();
    }

    /**
     * 1. Lấy thông tin hiển thị cho trang Checkout
     * Gồm: Hàng trong giỏ, Tổng tiền hàng, Địa chỉ user, Các đơn vị ship
     */
    public function getCheckoutInfo($userId) {
        $conn = $this->db->connect();

        // A. Lấy giỏ hàng
        $cart = $this->cartModel->findCartByUserId($userId);
        if (!$cart) {
            return ["cart_items" => [], "subtotal" => 0, "addresses" => [], "shipping" => []];
        }

        $cartItems = $this->cartModel->getCartDetails($cart['id']);
        
        // B. Tính tạm tính (Subtotal)
        $subtotal = 0;
        foreach ($cartItems as $item) {
            $subtotal += $item['price'] * $item['quantity'];
        }

        // C. Lấy danh sách địa chỉ của User (Raw SQL cho nhanh)
        $sqlAddr = "SELECT * FROM user_addresses WHERE user_id = :uid";
        $stmtAddr = $conn->prepare($sqlAddr);
        $stmtAddr->execute([':uid' => $userId]);
        $addresses = $stmtAddr->fetchAll(PDO::FETCH_ASSOC);

        // D. Lấy danh sách Shipping đang active (Dùng ShipModel)
        $shipping = $this->shipModel->getActiveProviders();

        return [
            "cart_items" => $cartItems,
            "subtotal"   => $subtotal,
            "addresses"  => $addresses,
            "shipping_providers" => $shipping
        ];
    }

    /**
     * 2. Xử lý Đặt hàng (Transaction)
     * Đây là hàm quan trọng nhất: Tạo đơn -> Thêm chi tiết -> Xóa giỏ
     */
    public function createOrder($userId, $addressId, $shippingId, $paymentMethod = 'cod') {
        $conn = $this->db->connect();

        // --- BƯỚC 1: CHUẨN BỊ DỮ LIỆU ---

        // Lấy lại giỏ hàng (Backend phải tự tính, không tin client)
        $cart = $this->cartModel->findCartByUserId($userId);
        if (!$cart) throw new Exception("Giỏ hàng không tồn tại");

        $items = $this->cartModel->getCartDetails($cart['id']);
        if (empty($items)) throw new Exception("Giỏ hàng trống, không thể thanh toán");

        // Tính tổng tiền hàng
        $totalAmount = 0;
        foreach ($items as $item) {
            $totalAmount += $item['price'] * $item['quantity'];
        }

        // Lấy giá Ship từ DB (Chống hack giá ship)
        $shipPrice = $this->shipModel->getPriceById($shippingId);
        if ($shipPrice === false) {
            throw new Exception("Đơn vị vận chuyển không hợp lệ");
        }

        // Tổng tiền cuối cùng
        $finalTotal = $totalAmount + $shipPrice;

        // --- BƯỚC 2: THỰC HIỆN GIAO DỊCH (TRANSACTION) ---
        try {
            $conn->beginTransaction(); // Bắt đầu khóa dữ liệu

            // A. Tạo đơn hàng (Insert Orders)
            $sqlOrder = "INSERT INTO orders (user_id, status, total, shipping_id, delivery_status, address, created_at) 
                         VALUES (:uid, 'pending', :total, :sid, 'pending', :addr_id, NOW())";
            $stmtOrder = $conn->prepare($sqlOrder);
            $stmtOrder->execute([
                ':uid' => $userId,
                ':total' => $finalTotal,
                ':sid' => $shippingId,
                ':addr_id' => $addressId
            ]);
            $orderId = $conn->lastInsertId();

            // B. Tạo chi tiết đơn hàng (Insert Order Items)
            $sqlItem = "INSERT INTO order_items (order_id, product_variant_id, quantity, price) 
                        VALUES (:oid, :vid, :qty, :price)";
            $stmtItem = $conn->prepare($sqlItem);

            foreach ($items as $item) {
                // Kiểm tra kỹ variant_id
                if (empty($item['product_variant_id'])) {
                    throw new Exception("Lỗi dữ liệu sản phẩm (Thiếu Variant ID)");
                }

                $stmtItem->execute([
                    ':oid' => $orderId,
                    ':vid' => $item['product_variant_id'],
                    ':qty' => $item['quantity'],
                    ':price' => $item['price'] // Giá tại thời điểm mua
                ]);
            }

            // C. Tạo thanh toán (Insert Payments)
            $sqlPay = "INSERT INTO payments (order_id, method, status, paid_at) VALUES (:oid, :method, 'pending', NULL)";
            $stmtPay = $conn->prepare($sqlPay);
            $stmtPay->execute([':oid' => $orderId, ':method' => $paymentMethod]);

            // D. Xóa sạch giỏ hàng của User này (Quan trọng)
            $this->cartModel->removeCartItemByCartId($cart['id']);

            $conn->commit(); // Chốt đơn thành công
            return $orderId;

        } catch (Exception $e) {
            $conn->rollBack(); // Có lỗi thì hoàn tác tất cả
            throw new Exception("Lỗi đặt hàng: " . $e->getMessage());
        }
    }
}