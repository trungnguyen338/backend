<?php
namespace App\Models;

use App\Core\Database;
use PDO;

class CartModel {
    private $conn;

    public function __construct() {
        $db = new Database();
        $this->conn = $db->connect();
    }

    public function findCartByUserId($userId) {
        $sql = "SELECT * FROM carts WHERE user_id = :user_id LIMIT 1";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([':user_id' => $userId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function createCart($userId) {
        $sql = "INSERT INTO carts (user_id) VALUES (:user_id)";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([':user_id' => $userId]);
        return $this->conn->lastInsertId();
    }

    public function findCartItem($cartId, $variantId) {
        $sql = "SELECT * FROM cart_items WHERE cart_id = :cart_id AND product_variant_id = :variant_id LIMIT 1";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([':cart_id' => $cartId, ':variant_id' => $variantId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function addCartItem($cartId, $variantId, $quantity) {
        $sql = "INSERT INTO cart_items (cart_id, product_variant_id, quantity) VALUES (:cart_id, :variant_id, :quantity)";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([':cart_id' => $cartId, ':variant_id' => $variantId, ':quantity' => $quantity]);
    }

    public function updateCartItemQuantity($itemId, $newQuantity) {
        $sql = "UPDATE cart_items SET quantity = :quantity WHERE id = :id";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([':quantity' => $newQuantity, ':id' => $itemId]);
    }

    public function removeCartItemByCartId($cartId) {
        $sql = "DELETE FROM cart_items WHERE cart_id = :cart_id";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([':cart_id' => $cartId]);
    }

    public function getCartDetails($cartId) {
        // --- SỬA LẠI CÂU SQL NÀY ---
        $sql = "SELECT 
                    ci.id as cart_item_id, 
                    ci.product_variant_id, 
                    ci.quantity, 
                    p.name as product_name, 
                    p.image as product_image,
                    v.price, 
                    c.name as color, 
                    s.name as size
                FROM cart_items ci
                JOIN product_variants v ON ci.product_variant_id = v.id
                JOIN products p ON v.product_id = p.id
                LEFT JOIN colors c ON v.color_id = c.id
                LEFT JOIN sizes s ON v.size_id = s.id
                WHERE ci.cart_id = :cart_id";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([':cart_id' => $cartId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}