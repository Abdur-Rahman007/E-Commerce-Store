<?php
include_once "db.php";

class ProductModel{
    function getAllProducts($connection){
        $sql = "SELECT products.*, categories.name AS category_name, COALESCE(ROUND(AVG(reviews.rating), 1), 0) AS average_rating
                FROM products
                INNER JOIN categories
                ON products.category_id = categories.id
                LEFT JOIN reviews
                ON products.id = reviews.product_id
                GROUP BY products.id
                ORDER BY products.id DESC";
        $result = $connection->query($sql);
        return $result;
    }

    function getProductById($connection, $id){
        $sql = "SELECT * FROM products WHERE id='".$id."'";
        $result = $connection->query($sql);
        return $result;
    }

    function addProduct($connection, $category_id, $name, $description, $price, $stock_qty, $image_path, $is_available){
        $price = (int)$price;
        $stock_qty = (int)$stock_qty;
        $is_available = (int)$is_available;

        $sql = "INSERT INTO products (category_id, name, description, price, stock_qty, primary_image_path, is_available)
                VALUES('".$category_id."', '".$name."', '".$description."', '".$price."', '".$stock_qty."', '".$image_path."', '".$is_available."')";
        $result = $connection->query($sql);
        return $result;
    }

    function updateProduct($connection, $id, $category_id, $name, $description, $price, $stock_qty, $image_path, $is_available){
        $price = (int)$price;
        $stock_qty = (int)$stock_qty;
        $is_available = (int)$is_available;

        $sql = "UPDATE products
                SET category_id='".$category_id."',
                    name='".$name."',
                    description='".$description."',
                    price='".$price."',
                    stock_qty='".$stock_qty."',
                    primary_image_path='".$image_path."',
                    is_available='".$is_available."'
                WHERE id='".$id."'";
        $result = $connection->query($sql);
        return $result;
    }

    function deleteProduct($connection, $id){
        $sql = "DELETE FROM products WHERE id='".$id."'";
        $result = $connection->query($sql);
        return $result;
    }

    function toggleProductAvailability($connection, $id){
        $sql = "SELECT is_available FROM products WHERE id='".$id."'";
        $result = $connection->query($sql);

        if($result && $result->num_rows == 1){
            $row = $result->fetch_assoc();
            $newAvailability = $row["is_available"] ? 0 : 1;

            $sql = "UPDATE products SET is_available='".$newAvailability."' WHERE id='".$id."'";
            $result = $connection->query($sql);

            if($result){
                return $newAvailability;
            }
        }

        return "error";
    }
}

?>
