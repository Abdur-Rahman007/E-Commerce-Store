<?php
/**
 * View/confirmation.php — Order Confirmation (Task 3, Req 4)
 */
session_start();
require_once "../Model/DatabaseConnection.php";

if (empty($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$order_id = isset($_GET['order_id']) ? intval($_GET['order_id']) : 0;
if (!$order_id) {
    header("Location: catalogue.php");
    exit();
}

$db         = new DatabaseConnection();
$connection = $db->openConnection();
$result     = $db->getOrderWithItems($connection, $order_id);

if ($result->num_rows === 0) {
    header("Location: catalogue.php");
    exit();
}

$rows  = [];
$order = null;

while ($row = $result->fetch_assoc()) {
    // Security — only the owner can view their order
    if ($row['user_id'] != $_SESSION['user_id']) {
        header("Location: catalogue.php");
        exit();
    }
    if (!$order) {
        $order = [
            'id'               => $row['id'],
            'shipping_address' => $row['shipping_address'],
            'payment_method'   => $row['payment_method'],
            'total_amount'     => $row['total_amount'],
            'status'           => $row['status'],
            'created_at'       => $row['created_at'],
        ];
    }
    $rows[] = $row;
}

$connection->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Confirmed — EStore</title>
    <link rel="stylesheet" href="CSS/style.css">
</head>
<body>

<!-- NAVBAR -->
<nav class="navbar">
    <span class="brand">EStore</span>
    <div class="nav-links">
        <a href="catalogue.php">Continue Shopping</a>
        <a href="cart.php" class="cart-icon">
            🛒 <span class="cart-badge" id="cart-count">0</span>
        </a>
        <?php if (!empty($_SESSION['user_id'])): ?>
            <a href="#">Hi, <?= htmlspecialchars($_SESSION['name'] ?? 'User') ?></a>
            <a href="../Controller/logout.php">Logout</a>
        <?php endif; ?>
    </div>
</nav>

<div class="container">
    <div class="confirmation-box">

        <div class="confirm-icon">✅</div>
        <h1>Order Placed Successfully!</h1>
        <p>Thank you for your purchase. Your order ID is <strong>#<?= $order['id'] ?></strong>.</p>

        <!-- ORDER ITEMS TABLE -->
        <table class="confirm-table">
            <thead>
                <tr>
                    <th>Product</th>
                    <th>Qty</th>
                    <th>Unit Price</th>
                    <th>Subtotal</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($rows as $item): ?>
                    <tr>
                        <td>
                            <?php if (!empty($item['primary_image_path'])): ?>
                                <img
                                    src="../<?= htmlspecialchars($item['primary_image_path']) ?>"
                                    class="confirm-img"
                                    alt="<?= htmlspecialchars($item['product_name']) ?>"
                                >
                            <?php endif; ?>
                            <?= htmlspecialchars($item['product_name']) ?>
                        </td>
                        <td><?= $item['quantity'] ?></td>
                        <td>৳<?= number_format($item['unit_price'], 2) ?></td>
                        <td>৳<?= number_format($item['unit_price'] * $item['quantity'], 2) ?></td>
                    </tr>
                <?php endforeach; ?>
                <tr class="confirm-total-row">
                    <td colspan="3" style="text-align:right;font-weight:700;">Total</td>
                    <td style="color:#e94560;font-weight:700;">৳<?= number_format($order['total_amount'], 2) ?></td>
                </tr>
            </tbody>
        </table>

        <!-- ORDER DETAILS -->
        <div class="confirm-details">
            <div class="confirm-detail-row">
                <span>Shipping to</span>
                <span><?= htmlspecialchars($order['shipping_address']) ?></span>
            </div>
            <div class="confirm-detail-row">
                <span>Payment</span>
                <span><?= htmlspecialchars($order['payment_method']) ?></span>
            </div>
            <div class="confirm-detail-row">
                <span>Status</span>
                <span class="status-badge"><?= htmlspecialchars($order['status']) ?></span>
            </div>
            <div class="confirm-detail-row">
                <span>Placed on</span>
                <span><?= date('F d, Y h:i A', strtotime($order['created_at'])) ?></span>
            </div>
        </div>

        <a href="catalogue.php" class="btn btn-primary" style="width:auto;margin-top:1.5rem;padding:0.7rem 2rem;">
            🛍 Continue Shopping
        </a>

    </div>
</div>

</body>
</html>