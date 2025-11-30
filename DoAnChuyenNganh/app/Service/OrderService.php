<?php
require_once("./app/Model/OrderModel.php");
require_once("./app/Model/Order_ItemModel.php");
require_once("./app/Model/CartModel.php");
require_once("./app/Model/Cart_ItemModel.php");
require_once("./app/Model/PaymentModel.php");
require_once("./app/Model/ShipModel.php");
require_once("./app/Model/UserAdressModel.php");
header("Access-Control-Allow-Origin: http://localhost:5173");
header("Access-Control-Allow-Credentials: true");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");
session_start();

class OrderService
{
    private $orderModel;
    private $orderItemModel;
    private $cartModel;
    private $cartItemModel;
    private $shipModel;
    private $paymentModel;
    private $productModel;
    private $userAddressModel;

    public function __construct()
    {
        $this->orderModel = new OrderModel();
        $this->orderItemModel = new OrderItemModel();
        $this->cartModel = new CartModel();
        $this->cartItemModel = new CartItemModel();
        $this->shipModel = new ShipProviderModel();
        $this->paymentModel = new PaymentModel();
        $this->productModel = new Product();
        $this->userAddressModel = new UserAddressModel();
    }

    // Lấy tất cả đơn hàng
    public function getAllOrders()
    {
        return $this->orderModel->getAll();
    }

    // Lấy chi tiết đơn hàng
    public function getOrder($order_id)
    {
        $order = $this->orderModel->getById($order_id);
        if (!$order) return ["error" => "Order not found"];
        $items = $this->orderItemModel->getByOrderId($order_id);
        return ["order" => $order, "items" => $items];
    }
    // Thêm địa chỉ nhận hàng


    public function getOrdersByUser($user_id)
    {
        return $this->orderModel->getByUser($user_id);
    }

    // Thêm địa chỉ mới cho user
    public function addUserAddress($user_id, $address, $phone, $is_default = 0)
    {
        $id = $this->userAddressModel->insert($user_id, $address, $phone, $is_default);
        return $id ? ["message" => "Address added", "address_id" => $id] : ["error" => "Failed to add address"];
    }

    //Lấy thông tin địa chỉ theo ID
    public function getAddressById($address_id)
    {
        return $this->userAddressModel->getById($address_id);
    }

    public function createOrder($user_id, $total = 0, $status = "pending", $shipping_id = null, $delivery_status = "pending")
    {
        $order_id = $this->orderModel->insert($user_id, $total, $status, $shipping_id, $delivery_status);
        if (!$order_id) return ["error" => "Failed to create order"];
        return ["message" => "Order created successfully", "order_id" => $order_id];
    }

    public function addItem($order_id, $product_variant_id, $quantity, $price)
    {
        $ok = $this->orderItemModel->insert($order_id, $product_variant_id, $quantity, $price);
        return $ok ? ["message" => "Item added successfully"] : ["error" => "Failed to add item"];
    }

    public function updateStatus($order_id, $status)
    {
        $this->orderModel->updateStatus($order_id, $status);
        return ["message" => "Status updated"];
    }

    public function updateDeliveryStatus($order_id, $delivery_status)
    {
        $this->orderModel->updateDeliveryStatus($order_id, $delivery_status);
        return ["message" => "Delivery status updated"];
    }

    public function updateShipping($order_id, $shipping_id)
    {
        $this->orderModel->updateShipping($order_id, $shipping_id);
        return ["message" => "Shipping updated"];
    }

    public function updateTotal($order_id, $total)
    {
        $this->orderModel->updateTotal($order_id, $total);
        return ["message" => "Total updated"];
    }

    public function deleteItem($item_id)
    {
        $this->orderItemModel->deleteItem($item_id);
        return ["message" => "Item deleted"];
    }

    public function clearOrderItems($order_id)
    {
        $this->orderItemModel->deleteByOrder($order_id);
        return ["message" => "All items removed"];
    }

    // Lấy giỏ hàng và thông tin checkout
    public function getCartCheckout($user_id)
    {
        $cart = $this->cartModel->getCartByUser($user_id);
        if (!$cart) return ["error" => "Cart not found"];

        $items = $this->cartItemModel->getItemsByCart($cart['id']);
        $addresses = $this->userAddressModel->getByUser($user_id);

        $total = 0;
        foreach ($items as &$item) {
            $variant = $this->productModel->getVariantById($item['product_variant_id']);
            $item['price'] = $variant['price'];
            $item['color_id'] = $variant['color_id'];
            $item['size_id'] = $variant['size_id'];
            $total += $item['quantity'] * $variant['price'];
        }

        return [
            "cart" => $cart,
            "items" => $items,
            "total" => $total,
            "shippingOptions" => $this->shipModel->getAll(),
            "paymentOptions" => $this->paymentModel->getAll(),
            "addresses" => $addresses
        ];
    }

    // Xác nhận đơn hàng
    public function confirmOrder($user_id, $shipping_id, $payment_method, $address_id)
    {
        $cart = $this->cartModel->getCartByUser($user_id);
        if (!$cart) return ["error" => "Cart not found"];

        $items = $this->cartItemModel->getItemsByCart($cart['id']);
        if (empty($items)) return ["error" => "Cart is empty"];

        // Tính tổng
        $total = 0;
        foreach ($items as &$item) {
            $variant = $this->productModel->getVariantById($item['product_variant_id']);
            $item['price'] = $variant['price'];
            $total += $item['quantity'] * $variant['price'];
        }

        // Tạo order
        $order_id = $this->orderModel->insert($user_id, $total, 'pending', $shipping_id, $address_id);
        $this->paymentModel->insert($order_id, $payment_method, 'pending', null);

        // Thêm từng item
        foreach ($items as $item) {
            $this->orderItemModel->insert($order_id, $item['product_variant_id'], $item['quantity'], $item['price']);
        }


        // Xóa giỏ hàng
        $this->cartItemModel->clearCart($cart['id']);

        // Lấy thông tin địa chỉ chọn
        $address = $this->userAddressModel->getById($address_id);

        return [
            "message" => "Order confirmed",
            "order_id" => $order_id,
            "total" => $total,
            "item" => $item,
            "shipping_id" => $shipping_id,
            "payment_method" => $payment_method,
            "address" => $address // Trả luôn địa chỉ để hiển thị
        ];
    }
}
