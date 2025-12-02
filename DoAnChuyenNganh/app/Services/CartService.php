<?php
namespace App\Services;

use App\Models\CartModel;
use Exception;

class CartService {
    private $cartModel;

    public function __construct(CartModel $cartModel) {
        $this->cartModel = $cartModel;
    }

    // Lấy giỏ hàng của User
    public function getCart($userId) {
        $cart = $this->cartModel->findCartByUserId($userId);
        if (!$cart) {
            return []; // Giỏ hàng trống
        }
        return $this->cartModel->getCartDetails($cart['id']);
    }

    // Thêm vào giỏ hàng
    public function addToCart($userId, $variantId, $quantity) {
        // 1. Kiểm tra User có giỏ chưa, chưa thì tạo
        $cart = $this->cartModel->findCartByUserId($userId);
        if (!$cart) {
            $cartId = $this->cartModel->createCart($userId);
        } else {
            $cartId = $cart['id'];
        }

        // 2. Kiểm tra sản phẩm này đã có trong giỏ chưa
        $existingItem = $this->cartModel->findCartItem($cartId, $variantId);

        if ($existingItem) {
            // Nếu có rồi -> Cộng dồn số lượng
            $newQuantity = $existingItem['quantity'] + $quantity;
            return $this->cartModel->updateCartItemQuantity($existingItem['id'], $newQuantity);
        } else {
            // Nếu chưa có -> Thêm mới
            return $this->cartModel->addCartItem($cartId, $variantId, $quantity);
        }
    }

    // Xóa khỏi giỏ
    public function removeItem($itemId) {
        return $this->cartModel->removeCartItem($itemId);
    }
    
    // Update số lượng (Từ trang giỏ hàng)
    public function updateQuantity($itemId, $quantity) {
        if ($quantity <= 0) {
            return $this->removeItem($itemId);
        }
        return $this->cartModel->updateCartItemQuantity($itemId, $quantity);
    }
    public function syncGuestCart($userId, $guestItems) {
        if (empty($guestItems) || !is_array($guestItems)) {
            return; // Không có gì để đồng bộ
        }

        foreach ($guestItems as $item) {
            if (isset($item['variant_id']) && isset($item['quantity'])) {
                // Tái sử dụng hàm addToCart đã viết để nó tự xử lý cộng dồn
                $this->addToCart($userId, $item['variant_id'], $item['quantity']);
            }
        }
        
        // Trả về giỏ hàng mới nhất sau khi đồng bộ
        return $this->getCart($userId);
    }
}