<?php
require_once("./core/database.php");
class PaymentModel
{
    private $con;

    public function __construct()
    {
        $db = new Database();
        $this->con = $db->connect();
    }

    // Lấy tất cả payment
    public function getAll()
    {
        $sql = "SELECT DISTINCT method FROM payments ORDER BY method ASC";
        $stmt = $this->con->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_COLUMN); 
    }


    // Lấy theo id
    public function getById($id)
    {
        $sql = "SELECT * FROM payments WHERE id = ?";
        $stmt = $this->con->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Lấy payment theo order_id
    public function getByOrderId($order_id)
    {
        $sql = "SELECT * FROM payments WHERE order_id = ?";
        $stmt = $this->con->prepare($sql);
        $stmt->execute([$order_id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Thêm payment
    public function insert($order_id, $method = 'cod', $status = 'pending', $paid_at = null)
    {
        $sql = "INSERT INTO payments (order_id, method, status, paid_at)
                VALUES (?, ?, ?, ?)";
        $stmt = $this->con->prepare($sql);
        return $stmt->execute([$order_id, $method, $status, $paid_at]);
    }

    // Cập nhật trạng thái thanh toán
    public function updateStatus($id, $status, $paid_at = null)
    {
        $sql = "UPDATE payments SET status = ?, paid_at = ? WHERE id = ?";
        $stmt = $this->con->prepare($sql);
        return $stmt->execute([$status, $paid_at, $id]);
    }

    // Xóa payment
    public function delete($id)
    {
        $sql = "DELETE FROM payments WHERE id = ?";
        $stmt = $this->con->prepare($sql);
        return $stmt->execute([$id]);
    }
}
