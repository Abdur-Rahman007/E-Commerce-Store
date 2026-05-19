<?php

session_start();

header("Content-Type: application/json");

require_once "../../../Model/DatabaseConnection.php";

$db = new DatabaseConnection();
$connection = $db->openConnection();

/**
 * Only allow POST requests
 */
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {

    echo json_encode([
        "success" => false,
        "message" => "Invalid request method"
    ]);

    exit;
}

/**
 * Get JSON body
 */
$data = json_decode(file_get_contents("php://input"), true);

/**
 * Validate product_id
 */
if (!isset($data['product_id'])) {

    echo json_encode([
        "success" => false,
        "message" => "Product ID missing"
    ]);

    exit;
}

$product_id = intval($data['product_id']);

/**
 * Get product stock
 */
$result = $db->getProductStock($connection, $product_id);

if ($result->num_rows === 0) {

    echo json_encode([
        "success" => false,
        "message" => "Product not found"
    ]);

    exit;
}

$product = $result->fetch_assoc();

/**
 * Check availability
 */
if (!$product['is_available']) {

    echo json_encode([
        "success" => false,
        "message" => "Product unavailable"
    ]);

    exit;
}

$stock = intval($product['stock_qty']);

/**
 * Initialize cart session
 */
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

/**
 * Current quantity in cart
 */
$current_qty = $_SESSION['cart'][$product_id] ?? 0;

/**
 * Cap at available stock
 */
if ($current_qty >= $stock) {

    echo json_encode([
        "success" => false,
        "message" => "Maximum stock reached",
        "cart_count" => array_sum($_SESSION['cart'])
    ]);

    exit;
}

/**
 * Add product to cart
 */
$_SESSION['cart'][$product_id] = $current_qty + 1;

/**
 * Total cart item count
 */
$cart_count = array_sum($_SESSION['cart']);

/**
 * Success response
 */
echo json_encode([
    "success" => true,
    "message" => "Product added to cart",
    "cart_count" => $cart_count
]);

$connection->close();

?>