<?php
namespace App\Models; // 1. Thêm namespace

use App\Core\Database; // 2. Sử dụng class Database qua namespace
use PDO;

class UserModel
{
    private $conn;
    private $table = "users";

    public function __construct()
    {
        // Tự khởi tạo kết nối DB khi new Model
        $db = new Database();
        $this->conn = $db->connect();
    }

    public function create($username, $password, $email, $phone, $role = 'user')
    {
        $sql = "INSERT INTO $this->table (username, password, email, phone, role) 
                VALUES (:username, :password, :email, :phone, :role)";

        $stmt = $this->conn->prepare($sql);

        return $stmt->execute([
            ":username" => $username,
            ":password" => $password,   
            ":email"    => $email,
            ":phone"    => $phone,
            ":role"     => $role
        ]);
    }

    public function getAll()
    {
        $sql = "SELECT * FROM $this->table ORDER BY id DESC";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getById($id)
    {
        $sql = "SELECT * FROM $this->table WHERE id = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([":id" => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Dùng cho Login & Register
    public function findByUsername($username)
    {
        $sql = "SELECT * FROM $this->table WHERE username = :username LIMIT 1";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([":username" => $username]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function update($id, $username, $email, $phone, $role)
    {
        // 3. Sửa lỗi thiếu dấu phẩy sau :phone
        $sql = "UPDATE $this->table 
                SET username = :username, 
                    email = :email, 
                    phone = :phone, 
                    role = :role
                WHERE id = :id";

        $stmt = $this->conn->prepare($sql);

        return $stmt->execute([
            ":username" => $username,
            ":email"    => $email,
            ":phone"    => $phone,
            ":role"     => $role,
            ":id"       => $id
        ]);
    }

    public function updatePassword($id, $newPassword)
    {
        $sql = "UPDATE $this->table SET password = :password WHERE id = :id";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([
            ":password" => $newPassword,
            ":id"       => $id
        ]);
    }

    public function delete($id)
    {
        $sql = "DELETE FROM $this->table WHERE id = :id";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([":id" => $id]);
    }
}