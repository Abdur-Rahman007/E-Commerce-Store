<?php

header("Content-Type: application/json");

include "../Model/DatabaseConnection.php";

$db = new DatabaseConnection();
$connection = $db->openConnection();

$category_id = isset($_GET['category_id'])
    ? intval($_GET['category_id'])
    : 0;


if ($category_id <= 0) {

    $result = $db->getAvailableProducts($connection);

} else {

    $result = $db->getProductsByCategory($connection, $category_id);
}

$products = [];

while ($row = $result->fetch_assoc()) {

    $products[] = [
        "id" => $row["id"],
        "name" => $row["name"],
        "price" => $row["price"],
        "stock_qty" => $row["stock_qty"],
        "description" => $row["description"],
        "primary_image_path" => $row["primary_image_path"],
        "category_name" => $row["category_name"],
        "avg_rating" => $row["avg_rating"],
        "review_count" => $row["review_count"]
    ];
}

echo json_encode($products);

$connection->close();

?>