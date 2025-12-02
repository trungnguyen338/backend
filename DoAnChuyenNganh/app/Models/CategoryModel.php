<?php
require_once("./core/database.php");
class category
{
    private $con;
    public function __construct()
    {
        $db = new Database();
        $this->con = $db->connect();
    }

    public function getAll()
    {
        $stmt = $this->con->prepare("SELECT * FROM  categories ORDER BY sort_order ASC");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function getById($id)
    {
        $stmt = $this->con->prepare("SELECT * from categories where id=?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    public function create($name, $description)
    {
        $stmt = $this->con->prepare("INSERT INTO categories(name,description) values(?,?)");
        return $stmt->execute([$name, $description]);
    }
    public function update($id, $name, $description)
    {
        $stmt = $this->con->prepare("UPDATE categories set name =?,description=? where id=?");
        return $stmt->execute([$name, $description, $id]);
    }
    public function delete($id)
    {
        $stmt = $this->con->prepare("DELETE FROM categories where id=?");
        return $stmt->execute([$id]);
    }

    // Hàm cập nhật thứ tự danh mục

    public function updateOrder($orders)
    {
        $stmt = $this->con->prepare("UPDATE categories SET sort_order=? WHERE id=?");
        foreach ($orders as $item) {
            $stmt->execute([$item['sort_order'], $item['id']]);
        }
        return true;
    }
}
