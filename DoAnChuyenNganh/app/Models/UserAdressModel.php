<?php
require_once("./core/database.php");
class UserAddressModel
{
    private $con;

    public function __construct()
    {
        $db = new Database();
        $this->con = $db->connect();
    }

    // Lấy tất cả địa chỉ của user
    public function getByUser($user_id)
    {
        $sql = "SELECT * FROM user_addresses WHERE user_id = ?";
        $stmt = $this->con->prepare($sql);
        $stmt->execute([$user_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function getById($address_id)
    {
        $stmt = $this->con->prepare("SELECT * FROM user_addresses WHERE id = ?");
        $stmt->execute([$address_id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Lấy địa chỉ mặc định
    public function getDefault($user_id)
    {
        $sql = "SELECT * FROM user_addresses WHERE user_id = ? AND is_default = 1 LIMIT 1";
        $stmt = $this->con->prepare($sql);
        $stmt->execute([$user_id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Thêm địa chỉ
    public function insert($user_id, $address, $phone, $is_default = 0)
    {
        if ($is_default) {
            $this->unsetDefault($user_id);
        }
        $sql = "INSERT INTO user_addresses (user_id, address, phone, is_default) VALUES (?, ?, ?, ?)";
        $stmt = $this->con->prepare($sql);
        return $stmt->execute([$user_id, $address, $phone, $is_default]);
    }
    public function getLastInsertId()
    {
        return $this->con->lastInsertId();
    }

    // Cập nhật địa chỉ
    public function update($id, $address, $phone, $is_default = 0)
    {
        if ($is_default) {
            $sql_user = "SELECT user_id FROM user_addresses WHERE id = ?";
            $stmt_user = $this->con->prepare($sql_user);
            $stmt_user->execute([$id]);
            $user_id = $stmt_user->fetchColumn();
            $this->unsetDefault($user_id);
        }
        $sql = "UPDATE user_addresses SET address = ?, phone = ?, is_default = ? WHERE id = ?";
        $stmt = $this->con->prepare($sql);
        return $stmt->execute([$address, $phone, $is_default, $id]);
    }

    // Xóa địa chỉ
    public function delete($id)
    {
        $sql = "DELETE FROM user_addresses WHERE id = ?";
        $stmt = $this->con->prepare($sql);
        return $stmt->execute([$id]);
    }

    // Bỏ chọn mặc định các địa chỉ khác
    private function unsetDefault($user_id)
    {
        $sql = "UPDATE user_addresses SET is_default = 0 WHERE user_id = ?";
        $stmt = $this->con->prepare($sql);
        $stmt->execute([$user_id]);
    }
}
