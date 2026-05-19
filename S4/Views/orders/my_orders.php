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
| USER ID
|--------------------------------------------------------------------------
| For demo/testing use:
| $userId = 1;
|
| For real login system use:
| $userId = $_SESSION['user']['id'];
|--------------------------------------------------------------------------
*/

$userId = 1;

/*
|--------------------------------------------------------------------------
| FETCH USER ORDERS
|--------------------------------------------------------------------------
*/

$sql = "
SELECT 
    id,
    total_amount,
    status,
    created_at
FROM orders
WHERE user_id = ?
ORDER BY id DESC
";

$stmt = $pdo->prepare($sql);
$stmt->execute([$userId]);

$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>My Orders</title>

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css"
          rel="stylesheet">

    <style>

        body{
            background:#f5f6fa;
        }

        .order-card{
            border:none;
            border-radius:12px;
        }

        .badge{
            font-size:14px;
        }

    </style>

</head>

<body>

<div class="container py-5">

    <h2 class="mb-4">
        My Orders
    </h2>

    <?php if (!empty($orders)): ?>

        <?php foreach ($orders as $order): ?>

            <?php

            /*
            |--------------------------------------------------------------------------
            | BADGE COLOR
            |--------------------------------------------------------------------------
            */

            $badge = 'secondary';

            switch ($order['status']) {

                case 'Pending':
                    $badge = 'warning';
                    break;

                case 'Processing':
                    $badge = 'info';
                    break;

                case 'Shipped':
                    $badge = 'primary';
                    break;

                case 'Delivered':
                    $badge = 'success';
                    break;

                case 'Cancelled':
                    $badge = 'danger';
                    break;
            }

            ?>

            <div class="card shadow-sm mb-4 order-card">

                <div class="card-body">

                    <div class="d-flex justify-content-between align-items-center">

                        <div>

                            <h5 class="mb-1">

                                Order #<?= e($order['id']) ?>

                            </h5>

                            <small class="text-muted">

                                <?php

                                if (!empty($order['created_at'])) {

                                    echo date(
                                        'd M Y h:i A',
                                        strtotime($order['created_at'])
                                    );

                                } else {

                                    echo 'N/A';

                                }

                                ?>

                            </small>

                        </div>

                        <div>

                            <span class="badge bg-<?= $badge ?> p-2">

                                <?= e($order['status']) ?>

                            </span>

                        </div>

                    </div>

                    <hr>

                    <div class="d-flex justify-content-between">

                        <strong>
                            Total Amount
                        </strong>

                        <strong>

                            ৳<?= number_format($order['total_amount'], 2) ?>

                        </strong>

                    </div>

                </div>

            </div>

        <?php endforeach; ?>

    <?php else: ?>

        <div class="alert alert-info">

            No Orders Found

        </div>

    <?php endif; ?>

</div>

</body>

</html>