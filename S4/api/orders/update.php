<?php
// ─── FIX: This file was entirely missing — orders.js called it but it did not exist ───

require_once '../../config/database.php';
require_once '../../config/helpers.php';
require_once '../../Model/Order.php';

header('Content-Type: application/json');

// Only admin can update order status
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized. Admin access required.']);
    exit;
}

// Only accept POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
    exit;
}

$data     = json_decode(file_get_contents('php://input'), true);
$orderId  = $data['order_id'] ?? null;
$newStatus = $data['status']  ?? null;

if (!$orderId || !$newStatus) {
    echo json_encode(['success' => false, 'message' => 'Missing order_id or status.']);
    exit;
}

$order   = new Order($pdo);
$current = $order->getOrderById($orderId);

if (!$current) {
    echo json_encode(['success' => false, 'message' => 'Order not found.']);
    exit;
}

// Validate the status transition using the helper
if (!isValidStatusTransition($current['status'], $newStatus)) {
    echo json_encode([
        'success' => false,
        'message' => "Cannot change status from \"{$current['status']}\" to \"{$newStatus}\"."
    ]);
    exit;
}

$updated = $order->updateStatus($orderId, $newStatus);

if ($updated) {
    echo json_encode([
        'success' => true,
        'message' => "Order #{$orderId} status updated to \"{$newStatus}\"."
    ]);
} else {
    echo json_encode(['success' => false, 'message' => 'Database update failed. Please try again.']);
}
?>
