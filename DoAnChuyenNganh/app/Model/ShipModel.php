<?php
require_once("./core/database.php");
class ShipProviderModel
{
    private $con;

    public function __construct()
    {
        $db = new Database();
        $this->con = $db->connect();
    }

    // Lấy toàn bộ nhà vận chuyển
    public function getAll()
    {
        $sql = "SELECT * FROM shipping_providers ORDER BY id DESC";
        $stmt = $this->con->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Lấy theo id
    public function getById($id)
    {
        $sql = "SELECT * FROM shipping_providers WHERE id = ?";
        $stmt = $this->con->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Thêm nhà vận chuyển
    public function insert($name, $phone = null, $price = 0)
    {
        $sql = "INSERT INTO shipping_providers (name, phone, price)
                VALUES (?, ?, ?)";
        $stmt = $this->con->prepare($sql);
        return $stmt->execute([$name, $phone, $price]);
    }

    // Cập nhật
    public function update($id, $name, $phone = null, $price = 0)
    {
        $sql = "UPDATE shipping_providers SET name = ?, phone = ?, price = ? WHERE id = ?";
        $stmt = $this->con->prepare($sql);
        return $stmt->execute([$name, $phone, $price, $id]);
    }

    // Xóa
    public function delete($id)
    {
        $sql = "DELETE FROM shipping_providers WHERE id = ?";
        $stmt = $this->con->prepare($sql);
        return $stmt->execute([$id]);
    }
    // Lấy giá theo id nhà vận chuyển
    public function getPrice($id)
    {
        $sql = "SELECT price FROM shipping_providers WHERE id = ? LIMIT 1";
        $stmt = $this->con->prepare($sql);
        $stmt->execute([$id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ? $row['price'] : null;
    }
}
