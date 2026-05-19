<?php
/**
 * View/checkout.php — Checkout Page (Task 3, Req 4)
 */
session_start();
require_once "../Model/DatabaseConnection.php";

// Redirect if cart is empty
if (empty($_SESSION['cart'])) {
    header("Location: cart.php");
    exit();
}

$db         = new DatabaseConnection();
$connection = $db->openConnection();

// Fetch logged in user for saved addresses
$userResult = $db->getUserById($connection, $_SESSION['user_id']);
$user       = $userResult->fetch_assoc();

$savedAddresses = [];
if (!empty($user['shipping_addresses'])) {
    $decoded = json_decode($user['shipping_addresses'], true);
    if (is_array($decoded)) $savedAddresses = $decoded;
}

// Build cart summary for sidebar
$ids        = array_keys($_SESSION['cart']);
$products   = $db->getCartProducts($connection, $ids);
$cartItems  = [];
$grandTotal = 0;

foreach ($_SESSION['cart'] as $pid => $qty) {
    if (!isset($products[$pid])) continue;
    $p           = $products[$pid];
    $lineTotal   = $p['price'] * $qty;
    $grandTotal += $lineTotal;
    $cartItems[] = [
        'name'  => $p['name'],
        'qty'   => $qty,
        'line'  => $lineTotal,
        'price' => $p['price'],
    ];
}

$cartCount = array_sum($_SESSION['cart']);

// Retrieve previous errors and old POST if redirected back
$errors  = $_SESSION['checkout_errors'] ?? [];
$oldPost = $_SESSION['checkout_post']   ?? [];
unset($_SESSION['checkout_errors'], $_SESSION['checkout_post']);

$connection->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout — EStore</title>
    <link rel="stylesheet" href="CSS/style.css">
</head>
<body>

<!-- NAVBAR -->
<nav class="navbar">
    <span class="brand">EStore</span>
    <div class="nav-links">
        <a href="cart.php">← Back to Cart</a>
        <a href="cart.php" class="cart-icon">
            🛒 <span class="cart-badge" id="cart-count"><?= $cartCount ?></span>
        </a>
        <?php if (!empty($_SESSION['user_id'])): ?>
            <a href="#">Hi, <?= htmlspecialchars($_SESSION['name'] ?? 'User') ?></a>
            <a href="../Controller/logout.php">Logout</a>
        <?php endif; ?>
    </div>
</nav>

<div class="container">
    <h1 class="page-title">Checkout</h1>

    <!-- STOCK ERROR (server side) -->
    <?php if (!empty($errors['stock'])): ?>
        <div class="alert alert-error"><?= htmlspecialchars($errors['stock']) ?></div>
    <?php endif; ?>

    <div class="checkout-layout">

        <!-- ── FORM ───────────────────────────────── -->
        <form id="checkout-form" method="POST" action="../Controller/checkoutHandler.php" novalidate>

            <!-- SHIPPING ADDRESS -->
            <div class="checkout-section">
                <h2>📦 Shipping Address</h2>

                <div class="radio-group">

                    <?php if (!empty($savedAddresses)): ?>
                        <?php foreach ($savedAddresses as $addr): ?>
                            <?php if (empty(trim($addr))) continue; ?>
                            <label class="radio-label">
                                <input
                                    type="radio"
                                    name="address_choice"
                                    value="<?= htmlspecialchars($addr) ?>"
                                    <?= (($oldPost['address_choice'] ?? '') === $addr) ? 'checked' : '' ?>
                                >
                                <span><?= htmlspecialchars($addr) ?></span>
                            </label>
                        <?php endforeach; ?>
                    <?php endif; ?>

                    <!-- New address option -->
                    <label class="radio-label">
                        <input
                            type="radio"
                            name="address_choice"
                            value="new"
                            <?= (empty($savedAddresses) || ($oldPost['address_choice'] ?? '') === 'new') ? 'checked' : '' ?>
                            onchange="toggleNewAddress(this)"
                        >
                        <span>Use a new address</span>
                    </label>

                </div>

                <!-- New address textarea -->
                <div
                    id="new-address-block"
                    style="<?= (empty($savedAddresses) || ($oldPost['address_choice'] ?? '') === 'new') ? '' : 'display:none;' ?> margin-top:0.8rem;"
                >
                    <textarea
                        id="new_address"
                        name="new_address"
                        class="checkout-textarea"
                        placeholder="Enter your full shipping address…"
                    ><?= htmlspecialchars($oldPost['new_address'] ?? '') ?></textarea>
                </div>

                <div class="field-error" id="addr-error">
                    <?= htmlspecialchars($errors['address'] ?? '') ?>
                </div>
            </div>

            <!-- PAYMENT METHOD -->
            <div class="checkout-section">
                <h2>💳 Payment Method</h2>

                <div class="radio-group">
                    <label class="radio-label">
                        <input
                            type="radio"
                            name="payment_method"
                            value="Cash"
                            <?= (($oldPost['payment_method'] ?? 'Cash') === 'Cash') ? 'checked' : '' ?>
                        >
                        <span>💵 Cash on Delivery</span>
                    </label>
                    <label class="radio-label">
                        <input
                            type="radio"
                            name="payment_method"
                            value="Card"
                            <?= (($oldPost['payment_method'] ?? '') === 'Card') ? 'checked' : '' ?>
                        >
                        <span>💳 Card Payment</span>
                    </label>
                </div>

                <div class="field-error" id="pay-error">
                    <?= htmlspecialchars($errors['payment'] ?? '') ?>
                </div>
            </div>

            <button type="submit" class="btn btn-primary checkout-submit">
                Place Order →
            </button>

        </form>

        <!-- ── ORDER SUMMARY SIDEBAR ──────────────── -->
        <div class="checkout-summary">
            <h2>Order Summary</h2>

            <?php foreach ($cartItems as $item): ?>
                <div class="summary-row">
                    <span><?= htmlspecialchars($item['name']) ?> × <?= $item['qty'] ?></span>
                    <span>৳<?= number_format($item['line'], 2) ?></span>
                </div>
            <?php endforeach; ?>

            <div class="summary-row summary-total">
                <span>Total</span>
                <span>৳<?= number_format($grandTotal, 2) ?></span>
            </div>
        </div>

    </div>
</div>

<script src="../Controller/JS/checkout.js"></script>

</body>
</html>