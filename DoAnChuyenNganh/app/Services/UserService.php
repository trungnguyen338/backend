<?php
require_once "./app/Model/UserModel.php";
require_once("./app/Middleware/AdminMiddleware.php");
require_once("./app/Middleware/AuthMiddleware.php");
header("Access-Control-Allow-Origin: http://localhost:5173");
header("Access-Control-Allow-Credentials: true");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");

class UserService
{
    private $userModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
    }

    public function getAll()
    {

        return $this->userModel->getAll();
    }

    public function getById($id)
    {

        return $this->userModel->getById($id);
    }

    public function update($id, $data)
    {


        return $this->userModel->update(
            $id,
            $data["username"],
            $data["email"],
            $data["phone"],
            $data["role"]
        );
    }

    public function delete($id)
    {

        return $this->userModel->delete($id);
    }
}
