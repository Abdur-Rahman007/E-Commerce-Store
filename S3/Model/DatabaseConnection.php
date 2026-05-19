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


function getCartProducts($connection, array $product_ids) {
    if (empty($product_ids)) return [];

    $placeholders = implode(",", array_fill(0, count($product_ids), "?"));
    $types        = str_repeat("i", count($product_ids));

    $sql  = "SELECT p.*, c.name AS category_name
             FROM products p
             LEFT JOIN categories c ON p.category_id = c.id
             WHERE p.id IN ($placeholders)";

    $stmt = $connection->prepare($sql);
    $stmt->bind_param($types, ...$product_ids);
    $stmt->execute();

    $result   = $stmt->get_result();
    $products = [];

    while ($row = $result->fetch_assoc()) {
        $products[$row['id']] = $row;
    }

    return $products;
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

    function createOrder($connection, $user_id, $shipping_address, $payment_method, $total_amount) {
    $sql  = "INSERT INTO orders (user_id, shipping_address, payment_method, total_amount, status, created_at) 
             VALUES (?, ?, ?, ?, 'Pending', NOW())";
    $stmt = $connection->prepare($sql);
    $stmt->bind_param("issd", $user_id, $shipping_address, $payment_method, $total_amount);
    $stmt->execute();
    return $stmt->insert_id;
}

    function getUserById($connection, $user_id) {
    $sql  = "SELECT id, name, email, phone, shipping_addresses 
             FROM users 
             WHERE id = ?";
    $stmt = $connection->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    return $stmt->get_result();
}

function createOrderItem($connection, $order_id, $product_id, $quantity, $unit_price) {
    $sql  = "INSERT INTO order_items (order_id, product_id, quantity, unit_price) 
             VALUES (?, ?, ?, ?)";
    $stmt = $connection->prepare($sql);
    $stmt->bind_param("iiid", $order_id, $product_id, $quantity, $unit_price);
    $stmt->execute();
}

function decrementStock($connection, $product_id, $quantity) {
    $sql  = "UPDATE products 
             SET stock_qty = stock_qty - ? 
             WHERE id = ? AND stock_qty >= ?";
    $stmt = $connection->prepare($sql);
    $stmt->bind_param("iii", $quantity, $product_id, $quantity);
    $stmt->execute();
    return $stmt->affected_rows;
}

function getOrderWithItems($connection, $order_id) {
    $sql  = "SELECT o.*, 
                    oi.quantity, 
                    oi.unit_price,
                    p.name AS product_name, 
                    p.primary_image_path,
                    u.id AS user_id
             FROM orders o
             JOIN order_items oi ON oi.order_id = o.id
             JOIN products    p  ON p.id = oi.product_id
             JOIN users       u  ON u.id = o.user_id
             WHERE o.id = ?";
    $stmt = $connection->prepare($sql);
    $stmt->bind_param("i", $order_id);
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