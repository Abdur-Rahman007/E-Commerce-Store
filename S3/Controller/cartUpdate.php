<?php
/**
 * S3/Controller/cartUpdate.php
 * POST: product_id, delta (+1 or -1)
 * Returns JSON: { new_qty, line_total, grand_total, total_items } or { removed, grand_total, total_items }
 */
header("Content-Type: application/json");
session_start();

$product_id = intval($_POST['product_id'] ?? 0);
$delta      = intval($_POST['delta']      ?? 0);

if (!$product_id || !in_array($delta, [1, -1])) {
    echo json_encode(["error" => "Invalid request."]);
    exit();
}

if (!isset($_SESSION['cart'][$product_id])) {
    echo json_encode(["error" => "Item not in cart."]);
    exit();
}

require_once "../Model/DatabaseConnection.php";

$db         = new DatabaseConnection();
$connection = $db->openConnection();

// Check stock before incrementing
if ($delta === 1) {
    $stockResult = $db->getProductStock($connection, $product_id);
    $product     = $stockResult->fetch_assoc();
    if ($_SESSION['cart'][$product_id] >= $product['stock_qty']) {
        echo json_encode(["error" => "Stock limit reached."]);
        $connection->close();
        exit();
    }
}

$new_qty = $_SESSION['cart'][$product_id] + $delta;

// If qty drops to 0 — remove from cart
if ($new_qty <= 0) {
    unset($_SESSION['cart'][$product_id]);
    $grand_total = calcGrandTotal($connection, $db);
    echo json_encode([
        "removed"     => true,
        "grand_total" => number_format($grand_total, 2),
        "total_items" => array_sum($_SESSION['cart'] ?? [])
    ]);
    $connection->close();
    exit();
}

// Update session
$_SESSION['cart'][$product_id] = $new_qty;

// Get price for line total
$products  = $db->getCartProducts($connection, [$product_id]);
$price     = $products[$product_id]['price'] ?? 0;
$line_total  = $new_qty * $price;
$grand_total = calcGrandTotal($connection, $db);

echo json_encode([
    "new_qty"     => $new_qty,
    "line_total"  => number_format($line_total,  2),
    "grand_total" => number_format($grand_total, 2),
    "total_items" => array_sum($_SESSION['cart'])
]);

$connection->close();

// ── Helper ─────────────────────────────────────────────
function calcGrandTotal($connection, $db) {
    if (empty($_SESSION['cart'])) return 0;
    $ids      = array_keys($_SESSION['cart']);
    $products = $db->getCartProducts($connection, $ids);
    $total    = 0;
    foreach ($_SESSION['cart'] as $pid => $qty) {
        if (isset($products[$pid])) {
            $total += $products[$pid]['price'] * $qty;
        }
    }
    return $total;
}
?>