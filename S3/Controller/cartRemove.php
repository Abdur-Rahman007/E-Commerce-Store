<?php
/**
 * S3/Controller/cartRemove.php
 * POST: product_id
 * Returns JSON: { grand_total, total_items } or { error }
 */
header("Content-Type: application/json");
session_start();

$product_id = intval($_POST['product_id'] ?? 0);

if (!$product_id) {
    echo json_encode(["error" => "Invalid product."]);
    exit();
}

if (isset($_SESSION['cart'][$product_id])) {
    unset($_SESSION['cart'][$product_id]);
}

require_once "../Model/DatabaseConnection.php";

$db          = new DatabaseConnection();
$connection  = $db->openConnection();
$grand_total = 0;

if (!empty($_SESSION['cart'])) {
    $ids      = array_keys($_SESSION['cart']);
    $products = $db->getCartProducts($connection, $ids);
    foreach ($_SESSION['cart'] as $pid => $qty) {
        if (isset($products[$pid])) {
            $grand_total += $products[$pid]['price'] * $qty;
        }
    }
}

echo json_encode([
    "success"     => true,
    "grand_total" => number_format($grand_total, 2),
    "total_items" => array_sum($_SESSION['cart'] ?? [])
]);

$connection->close();
?>