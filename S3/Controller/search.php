<?php
header("Content-Type: application/json");

require_once "../Model/DatabaseConnection.php";

$keyword     = isset($_POST["keyword"])     ? trim($_POST["keyword"])       : "";
$category_id = isset($_POST["category_id"]) ? intval($_POST["category_id"]) : 0;

$db         = new DatabaseConnection();
$connection = $db->openConnection();

$result   = $db->searchAndFilterProducts($connection, $keyword, $category_id);
$products = [];
while ($row = $result->fetch_assoc()) {
    $products[] = $row;
}

echo json_encode(["products" => $products]);
$connection->close();
?>