<?php

session_start();
require_once "../Model/DatabaseConnection.php";


$db         = new DatabaseConnection();
$connection = $db->openConnection();

// Server-side initial load
$productsResult    = $db->getAvailableProducts($connection);
$categoriesResult  = $db->getAllCategories($connection);

$initialProducts = [];
while ($row = $productsResult->fetch_assoc()) {
    $initialProducts[] = $row;
}
$categories = [];
while ($row = $categoriesResult->fetch_assoc()) {
    $categories[] = $row;
}

$cartCount = isset($_SESSION['cart']) ? array_sum($_SESSION['cart']) : 0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shop — EStore</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

<!-- Navbar -->
<nav class="navbar">
    <span class="brand">🛍 EStore</span>
    <div class="nav-links">
        <a href="../../S4/Views/orders/my_orders.php">My Orders</a>
        <?php if (!empty($_SESSION['user_id'])): ?>
            <a href="cart.php" class="cart-icon">
            🛒 <span class="cart-badge" id="cart-count"><?= $cartCount ?></span>
            </a>
            <a href="../../S1/View/profile.php">Hi, <?= htmlspecialchars($_SESSION['name'] ?? 'User') ?></a>
            <a href="../../S1/Controller/logout.php">Logout</a>
        <?php else: ?>
            <a href="login.php">Login</a>
        <?php endif; ?>
    </div>
</nav>

<div class="container">
    <h1 class="page-title">All Products</h1>

    <!-- Filter Bar -->
    <div class="filter-bar">
        <input type="text" id="search-input" placeholder="🔍  Search products…" autocomplete="off" onkeyup="search()">
        <select id="category-filter" onchange="filterByCategory()">
        <option value="">All Categories</option>
        <?php foreach ($categories as $cat): ?>
            <option value="<?= $cat['id'] ?>"><?= htmlspecialchars($cat['name']) ?>
            </option>
        <?php endforeach; ?>
        </select>
    </div>

    <!-- Product Grid (server-side initial render) -->
    <div id="product-grid">
        <?php if (empty($initialProducts)): ?>
            <p style="color:#888;text-align:center;grid-column:1/-1;padding:2rem;">No products available.</p>
        <?php else: ?>
            <?php foreach ($initialProducts as $p): ?>
                <div class="product-card">
                    <a href="productDetail.php?id=<?= $p['id'] ?>">
                        <?php if (!empty($p['primary_image_path'])): ?>
                            <img src="../<?= htmlspecialchars($p['primary_image_path']) ?>" alt="<?= htmlspecialchars($p['name']) ?>" loading="lazy">
                        <?php else: ?>
                            <div class="no-img">No Image</div>
                        <?php endif; ?>
                    </a>
                    <div class="card-body">
                        <div class="card-category"><?= htmlspecialchars($p['category_name'] ?? 'Uncategorized') ?></div>
                        <div class="card-title"><?= htmlspecialchars($p['name']) ?></div>
                        <div class="card-rating">
                            <?php
                            $avg = floatval($p['avg_rating']);
                            for ($i = 1; $i <= 5; $i++): ?>
                                <span class="<?= $i <= round($avg) ? 'stars' : 'stars-empty' ?>">★</span>
                            <?php endfor; ?>
                            <?= number_format($avg, 1) ?>
                            <span>(<?= $p['review_count'] ?> review<?= $p['review_count'] != 1 ? 's' : '' ?>)</span>
                        </div>
                        <div class="card-price">৳<?= number_format($p['price'], 2) ?></div>
                        <button class="btn btn-primary" onclick="addToCart(<?= $p['id'] ?>, this)">Add to Cart</button>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<div id="toast" class="toast"></div>
<script src="../Controller/JS/search.js"></script>
<script src="../Controller/JS/search.js"></script>
<script src="../Controller/JS/addToCart.js"></script>
</body>
</html>
<?php $connection->close(); ?>