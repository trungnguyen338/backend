<?php
require_once("./core/database.php");

class CartItemModel
{
    private $con;

    public function __construct()
    {
        $db = new Database();
        $this->con = $db->connect();
    }

    // Lấy danh sách item trong cart (có thông tin variant, product, màu, size)
    public function getItemsByCart($cart_id)
    {
        $sql = "
        SELECT ci.id, ci.cart_id, ci.product_variant_id, ci.quantity,
               pv.price, pv.stock,
               p.name AS product_name,
               c.name AS color_name,
               s.name AS size_name
        FROM cart_items ci
        JOIN product_variants pv ON ci.product_variant_id = pv.id
        JOIN products p ON pv.product_id = p.id
        JOIN colors c ON pv.color_id = c.id
        JOIN sizes s ON pv.size_id = s.id
        WHERE ci.cart_id = ?
    ";
        $stmt = $this->con->prepare($sql);
        $stmt->execute([$cart_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    // Tìm item theo variant
    public function findItem($cart_id, $product_variant_id)
    {
        $sql = "SELECT * FROM cart_items WHERE cart_id = ? AND product_variant_id = ? LIMIT 1";
        $stmt = $this->con->prepare($sql);
        $stmt->execute([$cart_id, $product_variant_id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Thêm variant vào cart
    public function addItem($cart_id, $product_variant_id, $quantity = 1)
    {
        $sql = "INSERT INTO cart_items (cart_id, product_variant_id, quantity) VALUES (?, ?, ?)";
        $stmt = $this->con->prepare($sql);
        return $stmt->execute([$cart_id, $product_variant_id, $quantity]);
    }

    // Cập nhật số lượng
    public function updateQuantity($item_id, $quantity)
    {
        $sql = "UPDATE cart_items SET quantity = ? WHERE id = ?";
        $stmt = $this->con->prepare($sql);
        return $stmt->execute([$quantity, $item_id]);
    }

    // Xóa 1 item
    public function removeItem($item_id)
    {
        $sql = "DELETE FROM cart_items WHERE id = ?";
        $stmt = $this->con->prepare($sql);
        return $stmt->execute([$item_id]);
    }

    // Xóa toàn bộ sản phẩm trong 1 cart
    public function clearCart($cart_id)
    {
        $sql = "DELETE FROM cart_items WHERE cart_id = ?";
        $stmt = $this->con->prepare($sql);
        return $stmt->execute([$cart_id]);
    }
    // lấy 1 cart item theo id:
    public function getItemById($item_id)
    {
        $sql = "SELECT * FROM cart_items WHERE id = ?";
        $stmt = $this->con->prepare($sql);
        $stmt->execute([$item_id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
