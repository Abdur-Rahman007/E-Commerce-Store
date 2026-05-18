<?php
/**
 * Controller/API/search.php
 * Receives POST: keyword
 * Returns JSON: { products: [...] } or { error: "..." }
 */
header("Content-Type: application/json");

require_once "../../Model/DatabaseConnection.php";

$keyword = isset($_POST["keyword"]) ? trim($_POST["keyword"]) : "";

$db         = new DatabaseConnection();
$connection = $db->openConnection();

if ($keyword === "") {
    // Empty search → return all available products
    $result = $db->getAvailableProducts($connection);
} else {
    $result = $db->searchProducts($connection, $keyword);
}

$products = [];
while ($row = $result->fetch_assoc()) {
    $products[] = $row;
}

echo json_encode(["products" => $products]);

$connection->close();
?>