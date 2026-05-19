<?php

require_once '../../config/database.php';
require_once '../../config/helpers.php';

/*
|--------------------------------------------------------------------------
| START SESSION
|--------------------------------------------------------------------------
*/

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/*
|--------------------------------------------------------------------------
| DEMO USER
|--------------------------------------------------------------------------
| Demo SQL uses user_id = 1
|--------------------------------------------------------------------------
*/

$userId = 1;

/*
|--------------------------------------------------------------------------
| FETCH CART ITEMS FROM PRODUCTS
|--------------------------------------------------------------------------
| Demo version without cart table
|--------------------------------------------------------------------------
*/

$sql = "
SELECT 
    id AS product_id,
    name,
    price
FROM products
LIMIT 2
";

$stmt = $pdo->query($sql);

$products = $stmt->fetchAll(PDO::FETCH_ASSOC);

/*
|--------------------------------------------------------------------------
| CREATE DEMO CART
|--------------------------------------------------------------------------
*/

$cartItems = [];

foreach ($products as $product) {

    $cartItems[] = [
        'product_id' => $product['product_id'],
        'name'       => $product['name'],
        'price'      => $product['price'],
        'quantity'   => 1
    ];
}

/*
|--------------------------------------------------------------------------
| TOTAL AMOUNT
|--------------------------------------------------------------------------
*/

$totalAmount = 0;

foreach ($cartItems as $item) {

    $totalAmount += $item['price'] * $item['quantity'];

}

/*
|--------------------------------------------------------------------------
| PLACE ORDER
|--------------------------------------------------------------------------
*/

$success = '';
$error   = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $shippingAddress = trim($_POST['shipping_address'] ?? '');

    if (empty($shippingAddress)) {

        $error = 'Shipping address is required';

    } else {

        try {

            $pdo->beginTransaction();

            /*
            |--------------------------------------------------------------------------
            | INSERT ORDER
            |--------------------------------------------------------------------------
            */

            $sql = "
            INSERT INTO orders
            (
                user_id,
                shipping_address,
                total_amount,
                status
            )
            VALUES
            (
                ?,
                ?,
                ?,
                'Pending'
            )
            ";

            $stmt = $pdo->prepare($sql);

            $stmt->execute([
                $userId,
                $shippingAddress,
                $totalAmount
            ]);

            $orderId = $pdo->lastInsertId();

            /*
            |--------------------------------------------------------------------------
            | INSERT ORDER ITEMS
            |--------------------------------------------------------------------------
            */

            foreach ($cartItems as $item) {

                $sql = "
                INSERT INTO order_items
                (
                    order_id,
                    product_id,
                    quantity,
                    unit_price
                )
                VALUES
                (
                    ?,
                    ?,
                    ?,
                    ?
                )
                ";

                $stmt = $pdo->prepare($sql);

                $stmt->execute([
                    $orderId,
                    $item['product_id'],
                    $item['quantity'],
                    $item['price']
                ]);
            }

            $pdo->commit();

            $success = 'Order placed successfully';

            /*
            |--------------------------------------------------------------------------
            | CLEAR DEMO CART
            |--------------------------------------------------------------------------
            */

            $cartItems = [];

        } catch (PDOException $e) {

            $pdo->rollBack();

            $error = $e->getMessage();

        }

    }

}

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Place Order</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css"
          rel="stylesheet">

    <style>

        body{
            background:#f5f6fa;
        }

        .card{
            border:none;
            border-radius:12px;
        }

    </style>

</head>

<body>

<div class="container py-5">

    <h2 class="mb-4">
        Checkout
    </h2>

    <!-- SUCCESS -->
    <?php if (!empty($success)): ?>

        <div class="alert alert-success">

            <?= e($success) ?>

            <a href="my_orders.php"
               class="alert-link ms-2">

                View My Orders →

            </a>

        </div>

    <?php endif; ?>

    <!-- ERROR -->
    <?php if (!empty($error)): ?>

        <div class="alert alert-danger">

            <?= e($error) ?>

        </div>

    <?php endif; ?>

    <!-- CART -->
    <?php if (!empty($cartItems)): ?>

        <!-- ORDER SUMMARY -->
        <div class="card shadow-sm mb-4">

            <div class="card-header bg-dark text-white fw-bold">

                Order Summary

            </div>

            <div class="card-body p-0">

                <table class="table table-bordered mb-0">

                    <thead class="table-light">

                        <tr>

                            <th>Product</th>
                            <th class="text-center">Qty</th>
                            <th class="text-end">Unit Price</th>
                            <th class="text-end">Subtotal</th>

                        </tr>

                    </thead>

                    <tbody>

                    <?php foreach ($cartItems as $item): ?>

                        <tr>

                            <td>

                                <?= e($item['name']) ?>

                            </td>

                            <td class="text-center">

                                <?= (int)$item['quantity'] ?>

                            </td>

                            <td class="text-end">

                                ৳<?= number_format($item['price'], 2) ?>

                            </td>

                            <td class="text-end">

                                ৳<?= number_format(
                                    $item['price'] * $item['quantity'],
                                    2
                                ) ?>

                            </td>

                        </tr>

                    <?php endforeach; ?>

                    </tbody>

                    <tfoot class="table-dark">

                        <tr>

                            <td colspan="3"
                                class="text-end fw-bold">

                                Total

                            </td>

                            <td class="text-end fw-bold">

                                ৳<?= number_format($totalAmount, 2) ?>

                            </td>

                        </tr>

                    </tfoot>

                </table>

            </div>

        </div>

        <!-- SHIPPING FORM -->
        <div class="card shadow-sm">

            <div class="card-header bg-dark text-white fw-bold">

                Shipping Information

            </div>

            <div class="card-body">

                <form method="POST">

                    <div class="mb-3">

                        <label class="form-label fw-semibold">

                            Shipping Address

                        </label>

                        <textarea
                            name="shipping_address"
                            class="form-control"
                            rows="4"
                            required><?= e($_POST['shipping_address'] ?? '') ?></textarea>

                    </div>

                    <button type="submit"
                            class="btn btn-primary px-5">

                        Confirm & Place Order

                    </button>

                </form>

            </div>

        </div>

    <?php elseif (empty($success)): ?>

        <div class="alert alert-info">

            Your cart is empty

        </div>

    <?php endif; ?>

</div>

</body>

</html>