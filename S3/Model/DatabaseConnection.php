<?php 

class DatabaseConnection{
    function openConnection(){
        $db_host = "localhost"; //127.0.0.1
        $db_user = "root";
        $db_password = "";
        $db_name = "ecommerce_store";

        $connection = new mysqli($db_host,$db_user, $db_password, $db_name);
        if($connection->connect_error){
            die("Could not connect to the database- ". $connection->connect_error);
        }

    return $connection;
    }

    function getAvailableProducts($connection) {
        $sql = "SELECT 
                    p.*, 
                    c.name AS category_name,
                    ROUND(IFNULL(AVG(r.rating), 0), 1) AS avg_rating,
                    COUNT(r.id) AS review_count
                FROM products p
                LEFT JOIN categories c 
                    ON p.category_id = c.id
                LEFT JOIN reviews r 
                    ON r.product_id = p.id
                WHERE p.is_available = 1
                GROUP BY p.id
                ORDER BY p.created_at DESC";

        $stmt = $connection->prepare($sql);

        if (!$stmt) {
            die("Prepare Failed: " . $connection->error);
        }

        $stmt->execute();

        return $stmt->get_result();
    }

    public function getProductStock($connection, $product_id) {

    $sql = "SELECT stock_qty, is_available 
            FROM products 
            WHERE id = ? 
            LIMIT 1";

    $stmt = $connection->prepare($sql);

    if (!$stmt) {
        die("Prepare failed: " . $connection->error);
    }

    $stmt->bind_param("i", $product_id);
    $stmt->execute();

    return $stmt->get_result();
}

    function searchProducts($connection, $keyword) {

        $sql = "SELECT 
                    p.*, 
                    c.name AS category_name,
                    ROUND(IFNULL(AVG(r.rating), 0), 1) AS avg_rating,
                    COUNT(r.id) AS review_count
                FROM products p
                LEFT JOIN categories c 
                    ON p.category_id = c.id
                LEFT JOIN reviews r 
                    ON r.product_id = p.id
                WHERE p.is_available = 1
                AND (p.name LIKE ? OR p.description LIKE ?)
                GROUP BY p.id
                ORDER BY p.created_at DESC";

        $stmt = $connection->prepare($sql);

        if (!$stmt) {
            die("Prepare Failed: " . $connection->error);
        }

        $search = "%" . $keyword . "%";

        $stmt->bind_param("ss", $search, $search);

        $stmt->execute();

        return $stmt->get_result();
    }


    function searchAndFilterProducts($connection, $keyword, $category_id) {
    $sql = "SELECT p.*, c.name AS category_name,
                   ROUND(IFNULL(AVG(r.rating), 0), 1) AS avg_rating,
                   COUNT(r.id) AS review_count
            FROM products p
            LEFT JOIN categories c ON p.category_id = c.id
            LEFT JOIN reviews    r ON r.product_id  = p.id
            WHERE p.is_available = 1";

    if ($keyword !== "" && $category_id > 0) {
        $sql .= " AND (p.name LIKE ? OR p.description LIKE ?) AND p.category_id = ?";
    } elseif ($keyword !== "") {
        $sql .= " AND (p.name LIKE ? OR p.description LIKE ?)";
    } elseif ($category_id > 0) {
        $sql .= " AND p.category_id = ?";
    }

    $sql .= " GROUP BY p.id ORDER BY p.created_at DESC";

    $stmt = $connection->prepare($sql);

    if ($keyword !== "" && $category_id > 0) {
        $like = "%" . $keyword . "%";
        $stmt->bind_param("ssi", $like, $like, $category_id);
    } elseif ($keyword !== "") {
        $like = "%" . $keyword . "%";
        $stmt->bind_param("ss", $like, $like);
    } elseif ($category_id > 0) {
        $stmt->bind_param("i", $category_id);
    }

    $stmt->execute();
    return $stmt->get_result();
}

    function getProductsByCategory($connection, $category_id) {
    $sql = "SELECT p.*, c.name AS category_name,
                   ROUND(IFNULL(AVG(r.rating), 0), 1) AS avg_rating,
                   COUNT(r.id) AS review_count
            FROM products p
            LEFT JOIN categories c ON p.category_id = c.id
            LEFT JOIN reviews   r ON r.product_id = p.id
            WHERE p.is_available = 1
              AND p.category_id = ?
            GROUP BY p.id
            ORDER BY p.created_at DESC";

    $stmt = $connection->prepare($sql);
    $stmt->bind_param("i", $category_id);
    $stmt->execute();
    return $stmt->get_result();
}


    function getProductById($connection, $product_id) {

        $sql = "SELECT 
                    p.*, 
                    c.name AS category_name,
                    ROUND(IFNULL(AVG(r.rating), 0), 1) AS avg_rating,
                    COUNT(r.id) AS review_count
                FROM products p
                LEFT JOIN categories c 
                    ON p.category_id = c.id
                LEFT JOIN reviews r 
                    ON r.product_id = p.id
                WHERE p.id = ?
                GROUP BY p.id";

        $stmt = $connection->prepare($sql);

        if (!$stmt) {
            die("Prepare Failed: " . $connection->error);
        }

        $stmt->bind_param("i", $product_id);

        $stmt->execute();

        return $stmt->get_result();
    }


    function getAllCategories($connection) {

        $sql = "SELECT * FROM categories ORDER BY name ASC";

        $stmt = $connection->prepare($sql);

        if (!$stmt) {
            die("Prepare Failed: " . $connection->error);
        }

        $stmt->execute();

        return $stmt->get_result();
    }


    function closeConnection($connection) {
        if ($connection) {
            $connection->close();
        }
    }

}




?>