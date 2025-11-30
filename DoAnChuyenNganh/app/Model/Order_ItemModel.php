<?php
require_once("./core/database.php");
class OrderItemModel
{
    private $con;

    public function __construct()
    {
        $db = new Database();
        $this->con = $db->connect();
    }

    // Lấy item theo order_id (kèm thông tin product + variant)
    public function getByOrderId($order_id)
    {
        $sql = "SELECT oi.*, 
                       pv.price AS variant_price, 
                       pv.color_id, pv.size_id, 
                       p.name AS product_name, 
                       p.image AS product_image
                FROM order_items oi
                JOIN product_variants pv ON oi.product_variant_id = pv.id
                JOIN products p ON pv.product_id = p.id
                WHERE oi.order_id = ?";
        $stmt = $this->con->prepare($sql);
        $stmt->execute([$order_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Thêm item vào order
    public function insert($order_id, $product_variant_id, $quantity, $price)
    {
        $sql = "INSERT INTO order_items (order_id, product_variant_id, quantity, price)
                VALUES (?, ?, ?, ?)";
        $stmt = $this->con->prepare($sql);
        return $stmt->execute([$order_id, $product_variant_id, $quantity, $price]);
    }

    // Xóa tất cả item theo order
    public function deleteByOrder($order_id)
    {
        $sql = "DELETE FROM order_items WHERE order_id = ?";
        $stmt = $this->con->prepare($sql);
        return $stmt->execute([$order_id]);
    }

    // Xóa 1 item theo id
    public function deleteItem($id)
    {
        $sql = "DELETE FROM order_items WHERE id = ?";
        $stmt = $this->con->prepare($sql);
        return $stmt->execute([$id]);
    }
}
