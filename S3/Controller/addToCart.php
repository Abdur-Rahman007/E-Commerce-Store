<?php
/**
 * S3/Controller/cartAdd.php
 * POST: product_id
 * Returns JSON: { total_items } or { error }
 */
header("Content-Type: application/json");
session_start();

if (!isset($_POST['product_id']) || !is_numeric($_POST['product_id'])) {
    echo json_encode(["error" => "Invalid product."]);
    exit();
}

$product_id = intval($_POST['product_id']);

require_once "../Model/DatabaseConnection.php";

$db         = new DatabaseConnection();
$connection = $db->openConnection();

$result = $db->getProductStock($connection, $product_id);

if ($result->num_rows === 0) {
    echo json_encode(["error" => "Product not found."]);
    $connection->close();
    exit();
}

$product = $result->fetch_assoc();

if (!$product['is_available'] || $product['stock_qty'] <= 0) {
    echo json_encode(["error" => "This product is out of stock."]);
    $connection->close();
    exit();
}

// Init cart if not set
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

$current_qty = isset($_SESSION['cart'][$product_id]) ? $_SESSION['cart'][$product_id] : 0;

// Cap at stock limit
if ($current_qty >= $product['stock_qty']) {
    echo json_encode(["error" => "Cannot add more — stock limit reached."]);
    $connection->close();
    exit();
}

$_SESSION['cart'][$product_id] = $current_qty + 1;

echo json_encode([
    "success"     => true,
    "total_items" => array_sum($_SESSION['cart'])
]);

$connection->close();
?>