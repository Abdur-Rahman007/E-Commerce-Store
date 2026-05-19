<?php

require_once '../../config/database.php';
require_once '../../config/helpers.php';

/*
|--------------------------------------------------------------------------
| UPDATE STATUS
|--------------------------------------------------------------------------
*/

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['order_id'], $_POST['new_status'])) {

    $orderId    = $_POST['order_id'];
    $newStatus  = $_POST['new_status'];

    $update = $pdo->prepare("
        UPDATE orders 
        SET status = ?
        WHERE id = ?
    ");

    $update->execute([$newStatus, $orderId]);

    header("Location: adminOrder.php");
    exit;
}

/*
|--------------------------------------------------------------------------
| FILTERS
|--------------------------------------------------------------------------
*/

$status = $_GET['status'] ?? '';
$from   = $_GET['from'] ?? '';
$to     = $_GET['to'] ?? '';

/*
|--------------------------------------------------------------------------
| QUERY
|--------------------------------------------------------------------------
*/

$sql = "
SELECT 
    orders.id,
    orders.total_amount,
    orders.status,
    orders.created_at,
    users.name
FROM orders
LEFT JOIN users 
    ON orders.user_id = users.id
WHERE 1=1
";

$params = [];

/* Status Filter */
if (!empty($status)) {
    $sql .= " AND orders.status = ?";
    $params[] = $status;
}

/* From Date */
if (!empty($from)) {
    $sql .= " AND DATE(orders.created_at) >= ?";
    $params[] = $from;
}

/* To Date */
if (!empty($to)) {
    $sql .= " AND DATE(orders.created_at) <= ?";
    $params[] = $to;
}

$sql .= " ORDER BY orders.id DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);

$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Admin Orders</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>

        body{
            background:#f5f6fa;
        }

        .sidebar{
            min-height:100vh;
        }

        .table td,
        .table th{
            vertical-align:middle;
        }

    </style>

</head>

<body>

<div class="container-fluid">

    <div class="row">

        <!-- Sidebar -->
        <div class="col-md-2 bg-dark text-white p-4 sidebar">

            <h3 class="mb-4">Admin Panel</h3>

            <ul class="nav flex-column">

                <li class="nav-item mb-2">
                    <a href="#" class="nav-link text-white">
                        Dashboard
                    </a>
                </li>

                <li class="nav-item">
                    <a href="#" class="nav-link text-white fw-bold">
                        Orders
                    </a>
                </li>

            </ul>

        </div>

        <!-- Main Content -->
        <div class="col-md-10 p-4">

            <h2 class="mb-4">
                All Orders
            </h2>

            <!-- FILTER -->
            <div class="card shadow-sm mb-4">

                <div class="card-body">

                    <form method="GET" class="row g-3">

                        <div class="col-md-3">

                            <label class="form-label">
                                Status
                            </label>

                            <select name="status" class="form-select">

                                <option value="">
                                    All
                                </option>

                                <?php foreach (['Pending','Processing','Shipped','Delivered','Cancelled'] as $s): ?>

                                    <option value="<?= $s ?>"
                                        <?= ($status === $s) ? 'selected' : '' ?>>

                                        <?= $s ?>

                                    </option>

                                <?php endforeach; ?>

                            </select>

                        </div>

                        <div class="col-md-3">

                            <label class="form-label">
                                From Date
                            </label>

                            <input type="date"
                                   name="from"
                                   class="form-control"
                                   value="<?= e($from) ?>">

                        </div>

                        <div class="col-md-3">

                            <label class="form-label">
                                To Date
                            </label>

                            <input type="date"
                                   name="to"
                                   class="form-control"
                                   value="<?= e($to) ?>">

                        </div>

                        <div class="col-md-3 d-flex align-items-end gap-2">

                            <button type="submit"
                                    class="btn btn-primary w-100">

                                Filter

                            </button>

                            <a href="adminOrder.php"
                               class="btn btn-secondary w-100">

                                Reset

                            </a>

                        </div>

                    </form>

                </div>

            </div>

            <!-- TABLE -->
            <div class="card shadow-sm">

                <div class="card-body">

                    <table class="table table-bordered table-hover">

                        <thead class="table-dark">

                            <tr>

                                <th>ID</th>
                                <th>Customer</th>
                                <th>Total</th>
                                <th>Date</th>
                                <th>Status</th>
                                <th>Update Status</th>

                            </tr>

                        </thead>

                        <tbody>

                        <?php if (!empty($orders)): ?>

                            <?php foreach ($orders as $order): ?>

                                <tr>

                                    <td>
                                        #<?= e($order['id']) ?>
                                    </td>

                                    <td>
                                        <?= e($order['name']) ?>
                                    </td>

                                    <td>
                                        ৳<?= number_format($order['total_amount'], 2) ?>
                                    </td>

                                    <td>

                                        <?php

                                        echo !empty($order['created_at'])
                                            ? date('d M Y', strtotime($order['created_at']))
                                            : 'N/A';

                                        ?>

                                    </td>

                                    <td>

                                        <?php

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

                                        <span class="badge bg-<?= $badge ?>">

                                            <?= e($order['status']) ?>

                                        </span>

                                    </td>

                                    <!-- UPDATE STATUS -->
                                    <td>

                                        <form method="POST">

                                            <input type="hidden"
                                                   name="order_id"
                                                   value="<?= $order['id'] ?>">

                                            <div class="d-flex gap-2">

                                                <select name="new_status"
                                                        class="form-select form-select-sm">

                                                    <?php foreach (['Pending','Processing','Shipped','Delivered','Cancelled'] as $s): ?>

                                                        <option value="<?= $s ?>"
                                                            <?= ($order['status'] === $s) ? 'selected' : '' ?>>

                                                            <?= $s ?>

                                                        </option>

                                                    <?php endforeach; ?>

                                                </select>

                                                <button type="submit"
                                                        class="btn btn-sm btn-success">

                                                    Update

                                                </button>

                                            </div>

                                        </form>

                                    </td>

                                </tr>

                            <?php endforeach; ?>

                        <?php else: ?>

                            <tr>

                                <td colspan="6" class="text-center py-4">

                                    No Orders Found

                                </td>

                            </tr>

                        <?php endif; ?>

                        </tbody>

                    </table>

                </div>

            </div>

        </div>

    </div>

</div>

</body>

</html>