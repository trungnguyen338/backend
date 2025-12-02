<?php
namespace App\Models;

use App\Core\Database;
use PDO;

class ShipModel {
    private $conn;

    public function __construct() {
        $db = new Database();
        $this->conn = $db->connect();
    }

    public function getActiveProviders() {
        $sql = "SELECT id, name, price, delivery_time FROM shipping_providers WHERE status = 'active'";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    
    public function getPriceById($id) {
        $sql = "SELECT price FROM shipping_providers WHERE id = :id AND status = 'active'";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([':id' => $id]);
        return $stmt->fetchColumn();
    }
}