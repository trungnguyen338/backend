<?php
require_once("./app/Model/ShipModel.php");
require_once("./app/Middleware/AdminMiddleware.php");
header("Access-Control-Allow-Origin: http://localhost:5173");
header("Access-Control-Allow-Credentials: true");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");

class ShipProviderService
{
    private $shipModel;

    public function __construct()
    {
        $this->shipModel = new ShipProviderModel();
    }

    public function getAll()
    {
        return $this->shipModel->getAll();
    }

    public function getById($id)
    {
        $provider = $this->shipModel->getById($id);
        if (!$provider) return ["error" => "Shipping provider not found"];
        return $provider;
    }

    public function insert($data)
    {


        $ok = $this->shipModel->insert(
            $data['name'],
            $data['phone'] ?? null,
            $data['price'] ?? 0
        );
        return $ok ? ["message" => "Shipping provider added successfully"] : ["error" => "Failed to add shipping provider"];
    }

    public function update($data)
    {
        if (!isset($data['id'])) return ["error" => "Missing id"];
        $ok = $this->shipModel->update(
            $data['id'],
            $data['name'],
            $data['phone'] ?? null,
            $data['price'] ?? 0
        );
        return $ok ? ["message" => "Shipping provider updated successfully"] : ["error" => "Failed to update shipping provider"];
    }

    public function delete($id)
    {
        $ok = $this->shipModel->delete($id);
        return $ok ? ["message" => "Shipping provider deleted successfully"] : ["error" => "Failed to delete shipping provider"];
    }
    public function getprice($id)
    {
        $price = $this->shipModel->getPrice($id);
        return $price ? ["message" => "success", "price" => $price] : ["error" => "Failed"];
    }
}
