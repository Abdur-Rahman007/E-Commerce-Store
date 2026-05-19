<?php

class Order {

    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    // ─── FIX: Added createOrder() — was completely missing, preventing any order placement ───
    public function createOrder($userId, $items, $totalAmount, $shippingAddress) {
        try {
            $this->pdo->beginTransaction();

            $stmt = $this->pdo->prepare(
                "INSERT INTO orders (user_id, total_amount, shipping_address, status, created_at)
                 VALUES (?, ?, ?, 'Pending', NOW())"
            );
            $stmt->execute([$userId, $totalAmount, $shippingAddress]);
            $orderId = $this->pdo->lastInsertId();

            $itemStmt = $this->pdo->prepare(
                "INSERT INTO order_items (order_id, product_id, quantity, price)
                 VALUES (?, ?, ?, ?)"
            );

            foreach ($items as $item) {
                $itemStmt->execute([
                    $orderId,
                    $item['product_id'],
                    $item['quantity'],
                    $item['price']
                ]);
            }

            $this->pdo->commit();
            return $orderId;

        } catch (Exception $e) {
            $this->pdo->rollBack();
            return false;
        }
    }

    // Get logged in user orders
    public function getUserOrders($userId) {
        $stmt = $this->pdo->prepare(
            "SELECT * FROM orders WHERE user_id = ? ORDER BY created_at DESC"
        );
        $stmt->execute([$userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Get order items
    public function getOrderItems($orderId) {
        $stmt = $this->pdo->prepare(
            "SELECT oi.*, p.name, p.primary_image_path
             FROM order_items oi
             JOIN products p ON oi.product_id = p.id
             WHERE oi.order_id = ?"
        );
        $stmt->execute([$orderId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Get all orders for admin
    public function getAllOrders($status = null, $from = null, $to = null) {
        $query = "
            SELECT orders.*, users.name
            FROM orders
            JOIN users ON orders.user_id = users.id
            WHERE 1
        ";
        $params = [];

        if (!empty($status)) {
            $query .= " AND orders.status = ?";
            $params[] = $status;
        }

        if (!empty($from) && !empty($to)) {
            $query .= " AND DATE(orders.created_at) BETWEEN ? AND ?";
            $params[] = $from;
            $params[] = $to;
        }

        $query .= " ORDER BY orders.created_at DESC";

        $stmt = $this->pdo->prepare($query);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Get single order
    public function getOrderById($id) {
        $stmt = $this->pdo->prepare(
            "SELECT * FROM orders WHERE id = ?"
        );
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Update order status
    public function updateStatus($id, $status) {
        $stmt = $this->pdo->prepare(
            "UPDATE orders SET status = ? WHERE id = ?"
        );
        return $stmt->execute([$status, $id]);
    }
}
?>
