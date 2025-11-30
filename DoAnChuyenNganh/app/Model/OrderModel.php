<?php
require_once("./core/database.php");
class OrderModel
{
    private $con;

    public function __construct()
    {
        $db = new Database();
        $this->con = $db->connect();
    }

    // Lấy tất cả orders
    public function getAll()
    {
        $sql = "SELECT * FROM orders ORDER BY id DESC";
        $stmt = $this->con->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Lấy order theo id
    public function getById($id)
    {
        $sql = "SELECT * FROM orders WHERE id = ?";
        $stmt = $this->con->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Lấy orders theo user_id
    public function getByUser($user_id)
    {
        $sql = "SELECT * FROM orders WHERE user_id = ? ORDER BY id DESC";
        $stmt = $this->con->prepare($sql);
        $stmt->execute([$user_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Tạo order mới.
     * $shipping_id có thể là null.
     * $delivery_status mặc định 'pending' nếu truyền null.
     */
    public function insert($user_id, $total = null, $status = 'pending', $shipping_id = null, $delivery_status = 'pending', $address = null)
    {
        $sql = "INSERT INTO orders (user_id, total, status, shipping_id, delivery_status,address)
                VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $this->con->prepare($sql);
        $ok = $stmt->execute([$user_id, $total, $status, $shipping_id, $delivery_status, $address]);
        if ($ok) {
            return $this->con->lastInsertId();
        }
        return false;
    }

    // Cập nhật trạng thái (status)
    public function updateStatus($id, $status)
    {
        $sql = "UPDATE orders SET status = ? WHERE id = ?";
        $stmt = $this->con->prepare($sql);
        return $stmt->execute([$status, $id]);
    }

    // Cập nhật shipping_id
    public function updateShipping($id, $shipping_id)
    {
        $sql = "UPDATE orders SET shipping_id = ? WHERE id = ?";
        $stmt = $this->con->prepare($sql);
        return $stmt->execute([$shipping_id, $id]);
    }

    // Cập nhật delivery_status (ví dụ: pending, shipping, delivered, returned)
    public function updateDeliveryStatus($id, $delivery_status)
    {
        $sql = "UPDATE orders SET delivery_status = ? WHERE id = ?";
        $stmt = $this->con->prepare($sql);
        return $stmt->execute([$delivery_status, $id]);
    }

    // Cập nhật total (nếu cần)
    public function updateTotal($id, $total)
    {
        $sql = "UPDATE orders SET total = ? WHERE id = ?";
        $stmt = $this->con->prepare($sql);
        return $stmt->execute([$total, $id]);
    }

    // Xóa order (các order_items sẽ bị xóa theo FK ON DELETE CASCADE nếu DB thiết lập)
    public function delete($id)
    {
        $sql = "DELETE FROM orders WHERE id = ?";
        $stmt = $this->con->prepare($sql);
        return $stmt->execute([$id]);
    }
}
