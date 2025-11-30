<?php
require_once("./core/database.php");

class ProductVariant
{
    private $con;

    public function __construct()
    {
        $db = new Database();
        $this->con = $db->connect();
    }

    // Lấy tất cả variant theo product_id kèm ảnh
    public function getByProduct($product_id)
    {
        $sql = "SELECT 
                    pv.id AS variant_id,
                    pv.product_id,
                    pv.color_id,
                    pv.size_id,
                    pv.price,
                    pv.stock,
                    c.name AS color_name,
                    s.name AS size_name,
                    pi.image_path AS variant_image
                FROM product_variants pv
                JOIN colors c ON pv.color_id = c.id
                JOIN sizes s ON pv.size_id = s.id
                LEFT JOIN product_images pi ON pv.id = pi.variant_id
                WHERE pv.product_id = ?";
        $stmt = $this->con->prepare($sql);
        $stmt->execute([$product_id]);
        return $stmt->fetchAll(PDO::FETCH_BOTH);
    }

    // Lấy variant theo id kèm ảnh
    public function getById($id)
    {
        $sql = "SELECT 
                    pv.id AS variant_id,
                    pv.product_id,
                    pv.color_id,
                    pv.size_id,
                    pv.price,
                    pv.stock,
                    c.name AS color_name,
                    s.name AS size_name,
                    pi.image_path AS variant_image
                FROM product_variants pv
                JOIN colors c ON pv.color_id = c.id
                JOIN sizes s ON pv.size_id = s.id
                LEFT JOIN product_images pi ON pv.id = pi.variant_id
                WHERE pv.id = ?";
        $stmt = $this->con->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Thêm variant mới + ảnh (nếu có)
    public function insert($product_id, $color_id, $size_id, $price, $stock, $image_path = null)
    {
        $sql = "INSERT INTO product_variants (product_id, color_id, size_id, price, stock) 
                VALUES (?, ?, ?, ?, ?)";
        $stmt = $this->con->prepare($sql);
        if ($stmt->execute([$product_id, $color_id, $size_id, $price, $stock])) {
            $variant_id = $this->con->lastInsertId();
            if ($image_path) {
                $this->insertImage($variant_id, $image_path);
            }
            return $variant_id;
        }
        return false;
    }

    // Cập nhật variant + ảnh
    public function update($id, $color_id, $size_id, $price, $stock, $image_path = null)
    {
        $sql = "UPDATE product_variants 
                SET color_id = ?, size_id = ?, price = ?, stock = ?
                WHERE id = ?";
        $stmt = $this->con->prepare($sql);
        if ($stmt->execute([$color_id, $size_id, $price, $stock, $id])) {
            if ($image_path) {
                $this->updateImage($id, $image_path);
            }
            return true;
        }
        return false;
    }

    // Xóa variant (ảnh sẽ tự động xóa do FK ON DELETE CASCADE)
    public function delete($id)
    {
        $sql = "DELETE FROM product_variants WHERE id = ?";
        $stmt = $this->con->prepare($sql);
        return $stmt->execute([$id]);
    }

    // Thêm ảnh cho variant
    private function insertImage($variant_id, $image_path)
    {
        $sql = "INSERT INTO product_images (variant_id, image_path) VALUES (?, ?)";
        $stmt = $this->con->prepare($sql);
        return $stmt->execute([$variant_id, $image_path]);
    }

    // Cập nhật ảnh variant
    private function updateImage($variant_id, $image_path)
    {
        $sql = "SELECT id FROM product_images WHERE variant_id = ?";
        $stmt = $this->con->prepare($sql);
        $stmt->execute([$variant_id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row) {
            $sql = "UPDATE product_images SET image_path = ? WHERE variant_id = ?";
            $stmt = $this->con->prepare($sql);
            return $stmt->execute([$image_path, $variant_id]);
        } else {
            return $this->insertImage($variant_id, $image_path);
        }
    }
}
