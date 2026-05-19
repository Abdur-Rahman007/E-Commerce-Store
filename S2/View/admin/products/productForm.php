<?php
include "../../../model/ProductModel.php";
include "../../../model/CategoryModel.php";
session_start();

$error = $_SESSION["productError"] ?? "";
unset($_SESSION["productError"]);

$id = $_GET["id"] ?? "";
$category_id = "";
$name = "";
$description = "";
$price = "";
$stock_qty = 0;
$primary_image_path = "";
$is_available = 1;

$db = new DatabaseConnection();
$connection = $db->openConnection();
$productModel = new ProductModel();
$categoryModel = new CategoryModel();

if($id){
    $productResult = $productModel->getProductById($connection, $id);
    if($productResult && $productResult->num_rows == 1){
        $product = $productResult->fetch_assoc();
        $category_id = $product["category_id"];
        $name = $product["name"];
        $description = $product["description"];
        $price = $product["price"];
        $stock_qty = $product["stock_qty"];
        $primary_image_path = $product["primary_image_path"];
        $is_available = $product["is_available"];
    }
}

$categories = $categoryModel->getAllCategories($connection);
?>

<html>

    <head>
        <title>Product Form</title>
        <link rel="stylesheet" href="product.css"/>
        <script src="../../../controller/JS/availability.js"></script>
    </head>

    <body>
        <div class="container">
            <h2><?php echo $id ? "Edit Product" : "Add Product"; ?></h2>
            <p><a href="productList.php">Back to Product List</a></p>
            <p class="error"><?php echo $error; ?></p>

            <form method="post" action="../../../controller/ProductController.php" enctype="multipart/form-data">
                <input type="hidden" name="action" value="save"/>
                <input type="hidden" name="id" value="<?php echo $id; ?>"/>
                <input type="hidden" name="old_image_path" value="<?php echo $primary_image_path; ?>"/>

                <table>
                    <tr>
                        <td>Category</td>
                        <td>
                            <select name="category_id">
                                <option value="">Select Category</option>
                                <?php
                                if($categories && $categories->num_rows > 0){
                                    while($row = $categories->fetch_assoc()){
                                        $selected = ($row["id"] == $category_id) ? "selected" : "";
                                        $categoryName = $row["name"];
                                        echo "<option value='".$row["id"]."' $selected>$categoryName</option>";
                                    }
                                }
                                ?>
                            </select>
                        </td>
                    </tr>

                    <tr>
                        <td>Product Name</td>
                        <td><input type="text" name="name" value="<?php echo $name; ?>"/></td>
                    </tr>

                    <tr>
                        <td>Description</td>
                        <td><textarea name="description"><?php echo $description; ?></textarea></td>
                    </tr>

                    <tr>
                        <td>Price</td>
                        <td><input type="number" step="1" min="1" name="price" value="<?php echo (int)$price; ?>"/></td>
                    </tr>

                    <tr>
                        <td>Stock Quantity</td>
                        <td><input type="number" name="stock_qty" id="stock_qty" value="<?php echo $stock_qty; ?>" onkeyup="checkAvailability()" onchange="checkAvailability()"/></td>
                    </tr>

                    <tr>
                        <td>Product Image</td>
                        <td>
                            <input type="file" name="primary_image"/>
                            <?php
                            if($primary_image_path){
                                echo "<br/><img src='../../../$primary_image_path' height='80px' width='80px'/>";
                            }
                            ?>
                        </td>
                    </tr>

                    <tr>
                        <td>Available</td>
                        <td>
                            <input type="checkbox" name="is_available" id="is_available" <?php echo $is_available ? "checked" : ""; ?>/>
                            <span id="availabilityText"></span>
                        </td>
                    </tr>

                    <tr>
                        <td></td>
                        <td><input type="submit" value="Save Product"/></td>
                    </tr>
                    
                </table>

            </form>

        </div>

    </body>

</html>
