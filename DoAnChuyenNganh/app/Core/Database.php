<?php
namespace App\Core;

// Bắt buộc phải có 2 dòng này để không bị lỗi "Class not found"
use PDO;
use PDOException;

class Database {

    private $host = "127.0.0.1";
    private $dbname = "shopthoitrang"; // Đảm bảo tên DB này đúng trong phpMyAdmin
    private $username = "root";
    private $password = ""; // <--- ĐỂ TRỐNG Y NHƯ FILE CONFIG CỦA BẠN

    public $conn;

    public function connect() {
        $this->conn = null;

        try {
            // Cấu hình kết nối
            $this->conn = new PDO(
                "mysql:host=" . $this->host . ";dbname=" . $this->dbname . ";charset=utf8mb4",
                $this->username,
                $this->password
            );

            // Cấu hình chế độ báo lỗi (Rất quan trọng để debug)
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

        } catch(PDOException $e) {
            // Nếu lỗi, trả về JSON để Postman hiện ra đẹp
            header("Content-Type: application/json");
            http_response_code(500);
            echo json_encode([
                "status" => "error",
                "message" => "Lỗi kết nối Database: " . $e->getMessage()
            ]);
            exit();
        }

        return $this->conn;
    }
}