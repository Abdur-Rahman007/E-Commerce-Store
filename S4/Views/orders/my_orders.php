<?php require_once '../../config/helpers.php'; ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Orders</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../../public/css/style.css">
</head>

<body>

<div class="container py-5">

    <h2 class="mb-4">My Orders</h2>

    <?php if (!empty($orders)): ?>

        <?php foreach($orders as $order): ?>

            <div class="card mb-4 shadow-sm">
                <div class="card-body">

                    <div class="d-flex justify-content-between align-items-center">

                        <div>
                            <h5>
                                Order #<?= e($order['id']) ?>
                            </h5>

                            <p class="text-muted mb-0">
                                <?= e($order['created_at']) ?>
                            </p>
                        </div>

                        <div>
                            <span class="badge bg-<?= badgeColor($order['status']) ?>">
                                <?= e($order['status']) ?>
                            </span>
                        </div>

                    </div>

                    <hr>

                    <h6 class="mt-3">
                        Total: ৳<?= e($order['total_amount']) ?>
                    </h6>

                </div>
            </div>

        <?php endforeach; ?>

    <?php else: ?>

        <div class="alert alert-info">
            No orders found.
        </div>

    <?php endif; ?>

</div>

</body>
</html>