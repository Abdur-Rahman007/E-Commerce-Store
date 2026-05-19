<?php require_once '../../config/helpers.php'; ?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Orders</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../../public/css/style.css">
</head>

<body>

<div class="container-fluid">
    <div class="row">

        <!-- Sidebar -->
        <div class="col-md-2 sidebar bg-dark text-white p-4 min-vh-100">
            <h4>Admin Panel</h4>
            <ul class="nav flex-column mt-4">
                <li class="nav-item">
                    <a href="#" class="nav-link text-white">Dashboard</a>
                </li>
                <li class="nav-item">
                    <a href="#" class="nav-link text-white fw-bold">Orders</a>
                </li>
            </ul>
        </div>

        <!-- Main Content -->
        <div class="col-md-10 p-4">

            <h2 class="mb-4">All Orders</h2>

            <!-- ─── FIX: Filter form was missing — controller reads $_GET['status'], 'from', 'to'] but there was no UI for it ─── -->
            <div class="card mb-4 shadow-sm">
                <div class="card-body">
                    <form method="GET" class="row g-3 align-items-end">
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Status</label>
                            <select name="status" class="form-select">
                                <option value="">All Statuses</option>
                                <?php foreach (['Pending','Processing','Shipped','Delivered','Cancelled'] as $s): ?>
                                    <option value="<?= $s ?>" <?= (($_GET['status'] ?? '') === $s) ? 'selected' : '' ?>>
                                        <?= $s ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="col-md-3">
                            <label class="form-label fw-semibold">From Date</label>
                            <input type="date" name="from" class="form-control"
                                   value="<?= e($_GET['from'] ?? '') ?>">
                        </div>

                        <div class="col-md-3">
                            <label class="form-label fw-semibold">To Date</label>
                            <input type="date" name="to" class="form-control"
                                   value="<?= e($_GET['to'] ?? '') ?>">
                        </div>

                        <div class="col-md-3 d-flex gap-2">
                            <button type="submit" class="btn btn-primary w-100">Filter</button>
                            <a href="?" class="btn btn-outline-secondary w-100">Reset</a>
                        </div>
                    </form>
                </div>
            </div>

            <table class="table table-bordered table-hover bg-white shadow-sm">

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
                            <td>#<?= e($order['id']) ?></td>

                            <td><?= e($order['name']) ?></td>

                            <td>৳<?= number_format(e($order['total_amount']), 2) ?></td>

                            <td><?= e(date('d M Y', strtotime($order['created_at']))) ?></td>

                            <td>
                                <span class="badge bg-<?= badgeColor($order['status']) ?>">
                                    <?= e($order['status']) ?>
                                </span>
                            </td>

                            <td>
                                <!-- ─── FIX: Added data-current so JS can revert on error ─── -->
                                <select class="form-select form-select-sm status-dropdown"
                                        data-id="<?= e($order['id']) ?>"
                                        data-current="<?= e($order['status']) ?>">
                                    <?php foreach (['Pending','Processing','Shipped','Delivered','Cancelled'] as $s): ?>
                                        <option value="<?= $s ?>"
                                            <?= $order['status'] === $s ? 'selected' : '' ?>>
                                            <?= $s ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </td>
                        </tr>

                    <?php endforeach; ?>

                <?php else: ?>

                    <tr>
                        <td colspan="6" class="text-center text-muted py-4">
                            No orders found.
                        </td>
                    </tr>

                <?php endif; ?>

                </tbody>
            </table>

        </div>
    </div>
</div>

<script src="../../public/js/orders.js"></script>

</body>
</html>
