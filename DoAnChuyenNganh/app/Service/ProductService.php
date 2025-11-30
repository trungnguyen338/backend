<?php
require_once("./app/Model/ProductModel.php");
require_once("./app/Model/ProductVariantModel.php");
require("./app/Middleware/AdminMiddleware.php");
header("Access-Control-Allow-Origin: http://localhost:5173");
header("Access-Control-Allow-Credentials: true");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");

class ProductService
{
    private $productModel;
    private $variantModel;

    public function __construct()
    {
        $this->productModel = new Product();
        $this->variantModel = new ProductVariant();
    }

    // Lấy tất cả sản phẩm (kèm variants)
    public function getAll()
    {
        return $this->productModel->getAll();
    }

    // Lấy sản phẩm theo id
    public function getById($id)
    {
        $product = $this->productModel->getById($id);
        if (!$product) return ["error" => "Product not found"];
        return $product;
    }

    // Lấy sản phẩm theo subcategory
    public function getAllBySubcategory($sub_id)
    {
        $products = $this->productModel->getBySubcategory($sub_id);
        return $products ?: ["message" => "No products found"];
    }

    // Tạo sản phẩm (và variants)
    public function create($data)
    {

        // 1. Tạo product
        $productId = $this->productModel->insert(
            $data['name'],
            $data['description'] ?? null,
            $data['category_id'] ?? null,
            $data['status'] ?? 'available',
            $data['image'] ?? null
        );

        if (!$productId) return ["error" => "Create product failed"];

        // 2. Tạo variants
        if (!empty($data['variants']) && is_array($data['variants'])) {
            foreach ($data['variants'] as $v) {
                $this->variantModel->insert(
                    $productId,
                    $v['color_id'],
                    $v['size_id'],
                    $v['price'],
                    $v['stock']
                );
            }
        }

        return ["message" => "Product created"];
    }

    // Cập nhật sản phẩm (và variants)
    public function update($data)
    {

        if (!isset($data['id'])) return ["error" => "Missing product id"];

        // 1. Cập nhật product chung
        $ok = $this->productModel->update(
            $data['id'],
            $data['name'] ?? null,
            $data['description'] ?? null,
            $data['category_id'] ?? null,
            $data['status'] ?? 'available',
            $data['image'] ?? null
        );

        if (!$ok) return ["error" => "Update product failed"];

        // 2. Cập nhật variants nếu có
        if (!empty($data['variants']) && is_array($data['variants'])) {
            foreach ($data['variants'] as $v) {
                if (isset($v['id'])) {
                    // update variant nếu có id
                    $this->variantModel->update(
                        $v['id'],
                        $v['color_id'],
                        $v['size_id'],
                        $v['price'],
                        $v['stock']
                    );
                } else {
                    // tạo mới nếu chưa có id
                    $this->variantModel->insert(
                        $data['id'],
                        $v['color_id'],
                        $v['size_id'],
                        $v['price'],
                        $v['stock']
                    );
                }
            }
        }

        return ["message" => "Product updated"];
    }

    // Xóa sản phẩm (variant sẽ tự xóa nhờ FK)
    public function delete($id)
    {

        $ok = $this->productModel->delete($id);
        return $ok ? ["message" => "Product deleted"] : ["error" => "Delete failed"];
    }
}
