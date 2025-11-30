<?php
require_once("./app/Model/CartModel.php");
require_once("./app/Model/Cart_ItemModel.php");
require_once("./app/Model/ProductModel.php");
header("Access-Control-Allow-Origin: http://localhost:5173");
header("Access-Control-Allow-Credentials: true");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");

class CartService
{
    private $cartModel;
    private $cartItemModel;
    private $productModel;

    public function __construct()
    {
        $this->cartModel = new CartModel();
        $this->cartItemModel = new CartItemModel();
        $this->productModel = new Product();
    }

    // Lấy giỏ hàng + item (có info variant)
    public function getCart($user_id)
    {
        $cart = $this->cartModel->getCartByUser($user_id);

        if (!$cart) {
            $cart_id = $this->cartModel->createCart($user_id);
            $cart = $this->cartModel->getCartById($cart_id);
        }

        $items = $this->cartItemModel->getItemsByCart($cart['id']); // Đã join variant, product, color, size

        return [
            'cart' => $cart,
            'items' => $items
        ];
    }

    // Thêm variant vào giỏ
    public function addToCart($user_id, $product_variant_id, $quantity = 1)
    {
        // Lấy stock của variant
        $variant = $this->productModel->getVariantById($product_variant_id);
        if (!$variant) return ["error" => "Variant không tồn tại"];
        if ($variant['stock'] < $quantity) return ["error" => "Sản phẩm đã hết hàng"];

        $cart = $this->cartModel->getCartByUser($user_id);
        $cart_id = $cart ? $cart['id'] : $this->cartModel->createCart($user_id);

        $item = $this->cartItemModel->findItem($cart_id, $product_variant_id);

        if ($item) {
            $newQty = $item['quantity'] + $quantity;
            if ($newQty > $variant['stock']) $newQty = $variant['stock']; // không vượt stock
            $this->cartItemModel->updateQuantity($item['id'], $newQty);
        } else {
            $this->cartItemModel->addItem($cart_id, $product_variant_id, $quantity);
        }

        return $this->getCart($user_id);
    }

    // Update số lượng variant trong cart
    public function updateItem($item_id, $quantity)
    {
        $item = $this->cartItemModel->getItemById($item_id);
        if (!$item) return ["error" => "Item không tồn tại"];

        $variant = $this->productModel->getVariantById($item['product_variant_id']);
        if (!$variant) return ["error" => "Variant không tồn tại"];

        if ($quantity <= 0) {
            $this->cartItemModel->removeItem($item_id);
        } else {
            $quantity = min($quantity, $variant['stock']); // không vượt stock
            $this->cartItemModel->updateQuantity($item_id, $quantity);
        }
        return true;
    }

    // Xóa 1 item
    public function removeItem($item_id)
    {
        return $this->cartItemModel->removeItem($item_id);
    }

    // Xóa toàn bộ giỏ
    public function clearCart($user_id)
    {
        $cart = $this->cartModel->getCartByUser($user_id);
        if ($cart) {
            return $this->cartItemModel->clearCart($cart['id']);
        }
        return false;
    }
    // Đồng bộ toàn bộ giỏ hàng từ frontend lên backend
    public function syncCart($user_id, $items)
    {
        // Lấy cart
        $cart = $this->cartModel->getCartByUser($user_id);
        $cart_id = $cart ? $cart['id'] : $this->cartModel->createCart($user_id);

        // Xóa cũ
        $this->cartItemModel->clearCart($cart_id);

        // Thêm item mới
        foreach ($items as $item) {

            // FE có thể gửi variantId hoặc product_variant_id
            $variant_id = $item['variantId'] ?? $item['product_variant_id'] ?? null;

            if (!$variant_id) continue;

            $variant = $this->productModel->getVariantById($variant_id);
            if (!$variant) continue;

            $quantity = min($item['quantity'], $variant['stock']);

            $this->cartItemModel->addItem($cart_id, $variant_id, $quantity);
        }

        return $this->getCart($user_id);
    }
}
