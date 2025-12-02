<?php
require_once("./app/Model/PaymentModel.php");
require_once("./app/Middleware/AdminMiddleware.php");
header("Access-Control-Allow-Origin: http://localhost:5173");
header("Access-Control-Allow-Credentials: true");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");

class PaymentService
{
    private $paymentModel;

    public function __construct()
    {
        $this->paymentModel = new PaymentModel();
    }

    // Lấy tất cả payment
    public function getAllPayments()
    {
        return $this->paymentModel->getAll();
    }

    // Lấy payment theo id
    public function getPaymentById($id)
    {
        $payment = $this->paymentModel->getById($id);
        if (!$payment) return ["error" => "Payment not found"];
        return $payment;
    }

    // Lấy payment theo order_id
    public function getPaymentByOrderId($order_id)
    {
        $payment = $this->paymentModel->getByOrderId($order_id);
        if (!$payment) return ["error" => "Payment not found for this order"];
        return $payment;
    }

    // Thêm payment mới
    public function addPayment($order_id, $method = 'cod', $status = 'pending', $paid_at = null)
    {

        $ok = $this->paymentModel->insert($order_id, $method, $status, $paid_at);
        return $ok ? ["message" => "Payment created successfully"] : ["error" => "Failed to create payment"];
    }

    // Cập nhật trạng thái thanh toán
    public function updatePaymentStatus($id, $status, $paid_at = null)
    {

        $ok = $this->paymentModel->updateStatus($id, $status, $paid_at);
        return $ok ? ["message" => "Payment status updated"] : ["error" => "Failed to update status"];
    }

    // Xóa payment
    public function deletePayment($id)
    {
        $ok = $this->paymentModel->delete($id);
        return $ok ? ["message" => "Payment deleted"] : ["error" => "Failed to delete payment"];
    }
}
