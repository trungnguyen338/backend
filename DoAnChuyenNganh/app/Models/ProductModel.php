<?php
require_once("./core/database.php");

class Product
{
    private $con;

    public function __construct()
    {
        $db = new Database();
        $this->con = $db->connect();
    }
    //lấy thông tin variant theo id.
    public function getVariantById($variant_id)
    {
        $sql = "SELECT * FROM product_variants WHERE id = ?";
        $stmt = $this->con->prepare($sql);
        $stmt->execute([$variant_id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }


    // Lấy tất cả sản phẩm (kèm variants)
    public function getAll()
    {
        $sql = "SELECT * FROM products ORDER BY id DESC";
        $stmt = $this->con->prepare($sql);
        $stmt->execute();
        $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($products as &$product) {
            $product['variants'] = $this->getVariants($product['id']);
        }
        return $products;
    }

    // Lấy sản phẩm theo id (kèm variants)
    public function getById($id)
    {
        $sql = "SELECT * FROM products WHERE id = ?";
        $stmt = $this->con->prepare($sql);
        $stmt->execute([$id]);
        $product = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($product) {
            $product['variants'] = $this->getVariants($product['id']);
        }
        return $product;
    }

    // Lấy sản phẩm theo subcategory_id (kèm variants)
    public function getBySubcategory($subcategory_id)
    {
        $sql = "SELECT * FROM products WHERE subcategory_id = ?";
        $stmt = $this->con->prepare($sql);
        $stmt->execute([$subcategory_id]);
        $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($products as &$product) {
            $product['variants'] = $this->getVariants($product['id']);
        }
        return $products;
    }

    // Tạo sản phẩm mới (chưa tạo variant)
    public function insert($name, $description = null, $subcategory_id = null, $status = 'available', $image = null)
    {
        $sql = "INSERT INTO products (name, description, subcategory_id, status, image) 
                VALUES (?, ?, ?, ?, ?)";
        $stmt = $this->con->prepare($sql);
        $ok = $stmt->execute([$name, $description, $subcategory_id, $status, $image]);
        return $ok ? $this->con->lastInsertId() : false;
    }

    // Cập nhật sản phẩm chung
    public function update($id, $name, $description = null, $subcategory_id = null, $status = 'available', $image = null)
    {
        $sql = "UPDATE products 
                SET name = ?, description = ?, subcategory_id = ?, status = ?, image = ?
                WHERE id = ?";
        $stmt = $this->con->prepare($sql);
        return $stmt->execute([$name, $description, $subcategory_id, $status, $image, $id]);
    }

    // Xóa sản phẩm (các variant sẽ tự động xóa nhờ FK ON DELETE CASCADE)
    public function delete($id)
    {
        $sql = "DELETE FROM products WHERE id = ?";
        $stmt = $this->con->prepare($sql);
        return $stmt->execute([$id]);
    }

    // Lấy tất cả variant của 1 sản phẩm (kèm tên color và size)
    public function getVariants($product_id)
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
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
