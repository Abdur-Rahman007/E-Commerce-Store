<?php
session_start();

function require_login() {
    if (!isset($_SESSION['user_id'])) {
        header("Location: /login.php");
        exit;
    }
}

function require_admin() {
    if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
        header("Location: /login.php");
        exit;
    }
}

function e($string) {
    return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
}

function badgeColor($status) {
    return match($status) {
        'Pending' => 'warning',
        'Processing' => 'primary',
        'Shipped' => 'info',
        'Delivered' => 'success',
        'Cancelled' => 'danger',
        default => 'secondary'
    };
}

function isValidStatusTransition($current, $new) {
    $allowed = [
        'Pending' => ['Processing', 'Cancelled'],
        'Processing' => ['Shipped', 'Cancelled'],
        'Shipped' => ['Delivered', 'Cancelled'],
        'Delivered' => [],
        'Cancelled' => []
    ];

    return in_array($new, $allowed[$current] ?? []);
}
?>