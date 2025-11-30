<?php
require_once("./core/database.php");
class CartModel
{
    private $con;

    public function __construct()
    {

        $db = new Database();
        $this->con = $db->connect();
    }

    // Lấy cart theo user_id
    public function getCartByUser($user_id)
    {
        $sql = "SELECT * FROM carts WHERE user_id = ? LIMIT 1";
        $stmt = $this->con->prepare($sql);
        $stmt->execute([$user_id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Tạo cart mới
    public function createCart($user_id)
    {
        $sql = "INSERT INTO carts (user_id) VALUES (?)";
        $stmt = $this->con->prepare($sql);
        $stmt->execute([$user_id]);

        return $this->con->lastInsertId();
    }

    // Lấy cart bằng id
    public function getCartById($cart_id)
    {
        $sql = "SELECT * FROM carts WHERE id = ?";
        $stmt = $this->con->prepare($sql);
        $stmt->execute([$cart_id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
