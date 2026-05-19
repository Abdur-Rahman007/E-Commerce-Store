<?php
require_once '../config/database.php';
require_once '../config/helpers.php';
require_once '../Model/Order.php';

class OrderController {
    private $order;

    public function __construct($pdo) {
        $this->order = new Order($pdo);
    }

    // Show logged-in user's orders
    public function myOrders() {
        require_login();
        $orders = $this->order->getUserOrders($_SESSION['user_id']);
        include '../Views/orders/my_orders.php';
    }

    // ─── FIX: Added placeOrder() — was completely missing, preventing checkout ───
    public function placeOrder() {
        require_login();

        $cartItems  = $_SESSION['cart'] ?? [];
        $error      = null;
        $success    = null;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            $shippingAddress = trim($_POST['shipping_address'] ?? '');

            if (empty($shippingAddress)) {
                $error = 'Shipping address is required.';
            } elseif (empty($cartItems)) {
                $error = 'Your cart is empty.';
            } else {
                $totalAmount = array_sum(
                    array_map(fn($item) => $item['price'] * $item['quantity'], $cartItems)
                );

                $orderId = $this->order->createOrder(
                    $_SESSION['user_id'],
                    $cartItems,
                    $totalAmount,
                    $shippingAddress
                );

                if ($orderId) {
                    unset($_SESSION['cart']);   // clear cart after successful order
                    $cartItems = [];
                    $success   = 'Order #' . $orderId . ' placed successfully!';
                } else {
                    $error = 'Failed to place order. Please try again.';
                }
            }
        }

        $totalAmount = empty($cartItems) ? 0 :
            array_sum(array_map(fn($item) => $item['price'] * $item['quantity'], $cartItems));

        include '../Views/orders/place_order.php';
    }

    // Admin: view all orders with optional filters
    public function adminOrders() {
        require_admin();

        $status = $_GET['status'] ?? null;
        $from   = $_GET['from']   ?? null;
        $to     = $_GET['to']     ?? null;

        $orders = $this->order->getAllOrders($status, $from, $to);

        include '../Views/orders/admin_orders.php';
    }
}
?>
