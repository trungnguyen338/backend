<?php
require_once("./app/Model/SubcategoryModel.php");
header("Access-Control-Allow-Origin: http://localhost:5173");
header("Access-Control-Allow-Credentials: true");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");

class SubcategoryService
{
    private $subcategoryModel;

    public function __construct()
    {
        $this->subcategoryModel = new Subcategory();
    }

    /**
     * Lấy tất cả subcategory
     */
    public function getAll()
    {
        return $this->subcategoryModel->getAll();
    }

    /**
     * Lấy subcategory theo ID
     */
    public function getById($id)
    {
        $subcategory = $this->subcategoryModel->getById($id);

        if (!$subcategory) {
            return [
                "status" => "error",
                "message" => "Subcategory not found"
            ];
        }

        return [
            "status" => "success",
            "data" => $subcategory
        ];
    }

    /**
     * Tạo subcategory mới
     */
    public function create($name, $category_id)
    {


        $ok = $this->subcategoryModel->create($name, $category_id);

        return [
            "status" => $ok ? "success" : "error",
            "message" => $ok ? "Subcategory created successfully" : "Failed to create subcategory"
        ];
    }

    /**
     * Cập nhật subcategory
     */
    public function update($id, $name, $category_id)
    {


        $ok = $this->subcategoryModel->update($id, $name, $category_id);

        return [
            "status" => $ok ? "success" : "error",
            "message" => $ok ? "Subcategory updated successfully" : "Failed to update subcategory"
        ];
    }

    /**
     * Xóa subcategory
     */
    public function delete($id)
    {


        $ok = $this->subcategoryModel->delete($id);

        return [
            "status" => $ok ? "success" : "error",
            "message" => $ok ? "Subcategory deleted successfully" : "Failed to delete subcategory"
        ];
    }

    /**
     * Lấy tất cả subcategory theo category_id
     */
    public function getByCategory($category_id)
    {
        $subcategories = array_filter($this->subcategoryModel->getAll(), function ($subcat) use ($category_id) {
            return $subcat['category_id'] == $category_id;
        });

        return [
            "status" => "success",
            "data" => array_values($subcategories)
        ];
    }
}
