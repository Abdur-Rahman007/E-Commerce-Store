<?php
/**
 * S3/Controller/checkoutHandler.php
 * Handles checkout form POST.
 * Validates → creates order → decrements stock → clears cart → redirects.
 */
session_start();
require_once "../Model/DatabaseConnection.php";

// Only accept POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../View/checkout.php");
    exit();
}

// Must be logged in
if (empty($_SESSION['user_id'])) {
    header("Location: ../View/login.php");
    exit();
}

$db         = new DatabaseConnection();
$connection = $db->openConnection();

$errors = [];

// ── 1. Cart must be non-empty ──────────────────────────
if (empty($_SESSION['cart'])) {
    header("Location: ../View/cart.php");
    exit();
}

// ── 2. Resolve shipping address ────────────────────────
$address_choice   = trim($_POST['address_choice'] ?? "");
$new_address      = trim($_POST['new_address']    ?? "");
$shipping_address = "";

if ($address_choice === "new") {
    if (strlen($new_address) < 10) {
        $errors['address'] = "Please enter a valid address (at least 10 characters).";
    } else {
        $shipping_address = $new_address;
    }
} elseif (!empty($address_choice)) {
    $shipping_address = $address_choice;
} else {
    $errors['address'] = "Please select a shipping address.";
}

// ── 3. Payment method ──────────────────────────────────
$payment_method = trim($_POST['payment_method'] ?? "");
if (!in_array($payment_method, ['Cash', 'Card'])) {
    $errors['payment'] = "Please choose a valid payment method.";
}

// Redirect back if validation failed
if (!empty($errors)) {
    $_SESSION['checkout_errors'] = $errors;
    $_SESSION['checkout_post']   = $_POST;
    header("Location: ../View/checkout.php");
    exit();
}

// ── 4. Validate stock & compute total ─────────────────
$cart_ids  = array_keys($_SESSION['cart']);
$products  = $db->getCartProducts($connection, $cart_ids);
$total     = 0;
$stockErrs = [];

foreach ($_SESSION['cart'] as $pid => $qty) {
    if (!isset($products[$pid])) {
        $stockErrs[] = "A product is no longer available.";
        continue;
    }
    $p = $products[$pid];
    if ($p['stock_qty'] < $qty) {
        $stockErrs[] = htmlspecialchars($p['name']) . " only has {$p['stock_qty']} left in stock.";
    }
    $total += $p['price'] * $qty;
}

if (!empty($stockErrs)) {
    $_SESSION['checkout_errors'] = ['stock' => implode(" ", $stockErrs)];
    $_SESSION['checkout_post']   = $_POST;
    header("Location: ../View/checkout.php");
    exit();
}

// ── 5. Write to DB inside a transaction ───────────────
$connection->begin_transaction();

try {
    // Insert order row
    $order_id = $db->createOrder(
        $connection,
        $_SESSION['user_id'],
        $shipping_address,
        $payment_method,
        $total
    );

    // Insert order items + decrement stock
    foreach ($_SESSION['cart'] as $pid => $qty) {
        $p = $products[$pid];
        $db->createOrderItem($connection, $order_id, $pid, $qty, $p['price']);

        $affected = $db->decrementStock($connection, $pid, $qty);
        if ($affected === 0) {
            throw new Exception("Insufficient stock for " . $p['name']);
        }
    }

    $connection->commit();

    // ── 6. Clear cart & redirect to confirmation ───────
    unset($_SESSION['cart']);
    header("Location: ../View/confirmation.php?order_id=" . $order_id);
    exit();

} catch (Exception $e) {
    $connection->rollback();
    $_SESSION['checkout_errors'] = ['stock' => $e->getMessage()];
    $_SESSION['checkout_post']   = $_POST;
    header("Location: ../View/checkout.php");
    exit();
}
?>