<?php
require_once("./app/Model/CategoryModel.php");
header("Access-Control-Allow-Origin: http://localhost:5173");
header("Access-Control-Allow-Credentials: true");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");

class CategoryService
{
    private $categoryModel;

    public function __construct()
    {
        $this->categoryModel = new Category();
    }

    // Lấy toàn bộ danh mục
    public function getAll()
    {
        return $this->categoryModel->getAll();
    }

    // Lấy 1 danh mục theo ID
    public function getById($id)
    {
        $category = $this->categoryModel->getById($id);

        if (!$category) {
            return [
                "status" => "error",
                "message" => "Category not found"
            ];
        }

        return [
            "status" => "success",
            "data" => $category
        ];
    }

    // Tạo danh mục
    public function create($name, $description)
    {

        $ok = $this->categoryModel->create($name, $description);

        return [
            "status" => $ok ? "success" : "error",
            "message" => $ok ? "Category created" : "Create failed"
        ];
    }

    // Cập nhật danh mục
    public function update($id, $name, $description)
    {

        $ok = $this->categoryModel->update($id, $name, $description);

        return [
            "status" => $ok ? "success" : "error",
            "message" => $ok ? "Category updated" : "Update failed"
        ];
    }

    // Xóa danh mục
    public function delete($id)
    {

        $ok = $this->categoryModel->delete($id);

        return [
            "status" => $ok ? "success" : "error",
            "message" => $ok ? "Category deleted" : "Delete failed"
        ];
    }
}
