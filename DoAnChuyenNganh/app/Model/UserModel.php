<?php
require_once("./core/database.php");

class UserModel
{
    private $conn;
    private $table = "users";

    public function __construct()
    {
        $db = new Database();
        $this->conn = $db->connect();
    }

    public function create($username, $password, $email, $phone, $role = 'user')
    {
        $sql = "INSERT INTO $this->table (username, password, email ,phone ,role) 
                VALUES (:username, :password, :email,:phone, :role)";

        $stmt = $this->conn->prepare($sql);

        return $stmt->execute([
            ":username" => $username,
            ":password" => $password,   // nhớ hash trước khi truyền vào
            ":email"    => $email,
            ":phone"    => $phone,
            ":role"     => $role
        ]);
    }


    // LẤY TOÀN BỘ USER

    public function getAll()
    {
        $sql = "SELECT * FROM $this->table ORDER BY id DESC";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    // LẤY USER THEO ID
    public function getById($id)
    {
        $sql = "SELECT * FROM $this->table WHERE id = :id";
        $stmt = $this->conn->prepare($sql);

        $stmt->execute([":id" => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }


    // TÌM USER THEO USERNAME
    // dùng để LOGIN

    public function findByUsername($username)
    {
        $sql = "SELECT * FROM $this->table WHERE username = :username LIMIT 1";
        $stmt = $this->conn->prepare($sql);

        $stmt->execute([":username" => $username]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // ==========================
    // UPDATE USER
    // ==========================
    public function update($id, $username, $email, $phone, $role)
    {
        $sql = "UPDATE $this->table 
                SET username = :username, 
                    email = :email, 
                    phone = :phone
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

    // ==========================
    // UPDATE PASSWORD
    // ==========================
    public function updatePassword($id, $newPassword)
    {
        $sql = "UPDATE $this->table SET password = :password WHERE id = :id";

        $stmt = $this->conn->prepare($sql);

        return $stmt->execute([
            ":password" => $newPassword,
            ":id"       => $id
        ]);
    }

    // ==========================
    // DELETE USER
    // ==========================
    public function delete($id)
    {
        $sql = "DELETE FROM $this->table WHERE id = :id";
        $stmt = $this->conn->prepare($sql);

        return $stmt->execute([":id" => $id]);
    }
}
