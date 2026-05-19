<?php require_once '../../config/helpers.php'; ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Place Order</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../../public/css/style.css">
</head>
<body>

<div class="container py-5">

    <h2 class="mb-4">Checkout</h2>

    <?php if (!empty($success)): ?>
        <div class="alert alert-success">
            <?= e($success) ?>
            <a href="my_orders.php" class="alert-link ms-2">View My Orders →</a>
        </div>
    <?php endif; ?>

    <?php if (!empty($error)): ?>
        <div class="alert alert-danger"><?= e($error) ?></div>
    <?php endif; ?>

    <?php if (!empty($cartItems)): ?>

        <!-- Order Summary -->
        <div class="card mb-4 shadow-sm">
            <div class="card-header bg-dark text-white fw-bold">Order Summary</div>
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
                                <td><?= e($item['name'] ?? 'Product #' . $item['product_id']) ?></td>
                                <td class="text-center"><?= (int)$item['quantity'] ?></td>
                                <td class="text-end">৳<?= number_format($item['price'], 2) ?></td>
                                <td class="text-end">৳<?= number_format($item['price'] * $item['quantity'], 2) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                    <tfoot class="table-dark">
                        <tr>
                            <td colspan="3" class="text-end fw-bold">Total</td>
                            <td class="text-end fw-bold">৳<?= number_format($totalAmount, 2) ?></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        <!-- Shipping Form -->
        <div class="card shadow-sm">
            <div class="card-header bg-dark text-white fw-bold">Shipping Information</div>
            <div class="card-body">
                <form method="POST">
                    <div class="mb-3">
                        <label for="shipping_address" class="form-label fw-semibold">
                            Shipping Address <span class="text-danger">*</span>
                        </label>
                        <textarea id="shipping_address" name="shipping_address"
                                  class="form-control" rows="4"
                                  placeholder="House/Flat, Road, Area, City, District"
                                  required><?= e($_POST['shipping_address'] ?? '') ?></textarea>
                    </div>

                    <button type="submit" class="btn btn-primary px-5">
                        Confirm &amp; Place Order
                    </button>
                    <a href="javascript:history.back()" class="btn btn-outline-secondary ms-2">
                        Cancel
                    </a>
                </form>
            </div>
        </div>

    <?php elseif (empty($success)): ?>

        <div class="alert alert-info">
            Your cart is empty. <a href="/" class="alert-link">Continue shopping →</a>
        </div>

    <?php endif; ?>

</div>

</body>
</html>
