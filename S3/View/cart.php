<?php
/**
 * View/cart.php — Shopping Cart Page (Task 3, Req 3)
 */
session_start();
require_once "../Model/DatabaseConnection.php";

$db         = new DatabaseConnection();
$connection = $db->openConnection();

$cartItems  = [];
$grandTotal = 0;
$cartCount  = 0;

if (!empty($_SESSION['cart'])) {
    $ids      = array_keys($_SESSION['cart']);
    $products = $db->getCartProducts($connection, $ids);

    foreach ($_SESSION['cart'] as $pid => $qty) {
        if (!isset($products[$pid])) continue;
        $p = $products[$pid];

        // Cap qty at current stock
        if ($qty > $p['stock_qty']) {
            $qty = $p['stock_qty'];
            $_SESSION['cart'][$pid] = $qty;
        }

        $lineTotal   = $p['price'] * $qty;
        $grandTotal += $lineTotal;
        $cartCount  += $qty;

        $cartItems[] = [
            'id'         => $pid,
            'name'       => $p['name'],
            'price'      => $p['price'],
            'qty'        => $qty,
            'line_total' => $lineTotal,
            'image'      => $p['primary_image_path'],
            'stock'      => $p['stock_qty'],
        ];
    }
}

$connection->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Cart — EStore</title>
    <link rel="stylesheet" href="CSS/style.css">
</head>
<body>

<!-- NAVBAR -->
<nav class="navbar">
    <span class="brand">EStore</span>
    <div class="nav-links">
        <a href="catalogue.php">← Continue Shopping</a>
        <a href="cart.php" class="cart-icon">
            🛒 <span class="cart-badge" id="cart-count"><?= $cartCount ?></span>
        </a>
        <?php if (!empty($_SESSION['user_id'])): ?>
            <a href="#">Hi, <?= htmlspecialchars($_SESSION['name'] ?? 'User') ?></a>
            <a href="../Controller/logout.php">Logout</a>
        <?php else: ?>
            <a href="login.php">Login</a>
        <?php endif; ?>
    </div>
</nav>

<div class="container">
    <h1 class="page-title">Shopping Cart</h1>

    <div id="cart-content">
        <?php if (empty($cartItems)): ?>
            <div class="empty-cart">
                <p>🛒 Your cart is empty.</p>
                <a href="catalogue.php" class="btn btn-primary" style="width:auto;margin-top:1rem;">
                    Shop Now
                </a>
            </div>

        <?php else: ?>

            <!-- CART TABLE -->
            <table class="cart-table">
                <thead>
                    <tr>
                        <th>Product</th>
                        <th>Name</th>
                        <th>Unit Price</th>
                        <th>Quantity</th>
                        <th>Line Total</th>
                        <th>Remove</th>
                    </tr>
                </thead>
                <tbody id="cart-tbody">
                    <?php foreach ($cartItems as $item): ?>
                        <tr id="row-<?= $item['id'] ?>" class="cart-item-row">

                            <!-- IMAGE -->
                            <td>
                                <?php if (!empty($item['image'])): ?>
                                    <img
                                        src="../<?= htmlspecialchars($item['image']) ?>"
                                        alt="<?= htmlspecialchars($item['name']) ?>"
                                        class="cart-img"
                                    >
                                <?php else: ?>
                                    <div class="cart-no-img">No Image</div>
                                <?php endif; ?>
                            </td>

                            <!-- NAME -->
                            <td>
                                <span class="cart-item-name">
                                    <?= htmlspecialchars($item['name']) ?>
                                </span>
                            </td>

                            <!-- UNIT PRICE -->
                            <td>৳<?= number_format($item['price'], 2) ?></td>

                            <!-- QUANTITY CONTROLS -->
                            <td>
                                <div class="qty-controls">
                                    <button
                                        class="qty-btn minus"
                                        onclick="updateQty(<?= $item['id'] ?>, -1)"
                                    >−</button>
                                    <span id="qty-<?= $item['id'] ?>"><?= $item['qty'] ?></span>
                                    <button
                                        class="qty-btn plus"
                                        onclick="updateQty(<?= $item['id'] ?>, 1)"
                                    >+</button>
                                </div>
                            </td>

                            <!-- LINE TOTAL -->
                            <td id="line-<?= $item['id'] ?>">
                                ৳<?= number_format($item['line_total'], 2) ?>
                            </td>

                            <!-- REMOVE -->
                            <td>
                                <button
                                    class="btn btn-remove"
                                    onclick="removeItem(<?= $item['id'] ?>)"
                                >✕ Remove</button>
                            </td>

                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <!-- CART SUMMARY -->
            <div class="cart-summary">
                <div class="summary-row">
                    <span>Items</span>
                    <span id="cart-count-summary"><?= $cartCount ?></span>
                </div>
                <div class="summary-row summary-total">
                    <span>Grand Total</span>
                    <span id="grand-total">৳<?= number_format($grandTotal, 2) ?></span>
                </div>
                <a href="checkout.php" class="btn btn-primary" style="display:block;text-align:center;margin-top:1rem;">
                    Proceed to Checkout →
                </a>
            </div>

        <?php endif; ?>
    </div>
</div>

<!-- TOAST -->
<div id="toast" class="toast"></div>

<script src="../Controller/JS/cart.js"></script>

</body>
</html>