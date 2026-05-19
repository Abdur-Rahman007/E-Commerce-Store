<?php
include "../../../model/ProductModel.php";
session_start();

$message = $_SESSION["productMessage"] ?? "";
unset($_SESSION["productMessage"]);

$db = new DatabaseConnection();
$connection = $db->openConnection();
$productModel = new ProductModel();
$result = $productModel->getAllProducts($connection);
?>

<html>
    <head>
        <title>Product List</title>
        <link rel="stylesheet" href="product.css"/>
        <script src="../../../controller/JS/availability.js"></script>
    </head>
    <body>
        <div class="container">
            <h2>Product List</h2>
            <p><a href="../dashboard.php">Dashboard</a> | <a href="productForm.php">Add New Product</a></p>

            <p class="message"><?php echo $message; ?></p>

            <table border="1">
                <tr>
                    <th>ID</th>
                    <th>Image</th>
                    <th>Name</th>
                    <th>Category</th>
                    <th>Price</th>
                    <th>Stock</th>
                    <th>Average Rating</th>
                    <th>Available</th>
                    <th>Action</th>
                </tr>

                <?php
                if($result && $result->num_rows > 0){
                    while($row = $result->fetch_assoc()){
                        $id = $row["id"];
                        $name = htmlspecialchars($row["name"]);
                        $categoryName = htmlspecialchars($row["category_name"]);
                        $price = (int)$row["price"];
                        $stock = $row["stock_qty"];
                        $averageRating = $row["average_rating"] ?? 0;
                        $available = $row["is_available"] ? "In Stock" : "Out of Stock";
                        $availableClass = $row["is_available"] ? "stock-in" : "stock-out";
                        $image = $row["primary_image_path"] ? "../../../".$row["primary_image_path"] : "";
                        $imageTag = $image ? "<img src='$image' height='45px' width='45px'/>" : "No Image";

                        echo "<tr>
                                <td>$id</td>
                                <td>$imageTag</td>
                                <td>$name</td>
                                <td>$categoryName</td>
                                <td>$price</td>
                                <td>$stock</td>
                                <td>$averageRating</td>
                                <td><span id='availability_$id' class='availability-badge $availableClass'>$available</span></td>
                                <td>
                                    <a href='productForm.php?id=$id'>Edit</a> |
                                    <a href='../../../controller/ProductController.php?action=delete&id=$id' onclick=\"return confirm('Are you sure?')\">Delete</a>
                                </td>
                              </tr>";
                    }
                }else{
                    echo "<tr><td colspan='9'>No product found</td></tr>";
                }
                ?>
            </table>
        </div>
    </body>
</html>
