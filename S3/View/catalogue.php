<?php
/**
 * View/catalogue.php — Product Catalogue (Task 3, Req 1)
 * AJAX search + category filter + cart support
 */

session_start();

require_once "../Model/DatabaseConnection.php";

// ⚠️ REMOVE requireLogin() if catalogue should be public
// requireLogin();

$db         = new DatabaseConnection();
$connection = $db->openConnection();

// Server-side initial load
$productsResult   = $db->getAvailableProducts($connection);
$categoriesResult = $db->getAllCategories($connection);

$initialProducts = [];
while ($row = $productsResult->fetch_assoc()) {
    $initialProducts[] = $row;
}

$categories = [];
while ($row = $categoriesResult->fetch_assoc()) {
    $categories[] = $row;
}

// Safe cart count
$cartCount = isset($_SESSION['cart']) ? array_sum($_SESSION['cart']) : 0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shop — EStore</title>
    <link rel="stylesheet" href="CSS/style.css"><script src="../Controller/JS/search.js"></script>
</head>
<body>

<!-- NAVBAR -->
<nav class="navbar">
    <span class="brand">EStore</span>

    <div class="nav-links">
        <a href="catalogue.php">Shop</a>

        <?php if (!empty($_SESSION['user_id'])): ?>
            <a href="cart.php" class="cart-icon">
                🛒 <span class="cart-badge" id="cart-count"><?= $cartCount ?></span>
            </a>

            <a href="#">
                Hi, <?= htmlspecialchars($_SESSION['name'] ?? 'User') ?>
            </a>

            <a href="../Controller/logout.php">Logout</a>

        <?php else: ?>
            <a href="login.php">Login</a>
        <?php endif; ?>
    </div>
</nav>

<!-- CONTENT -->
<div class="container">

    <h1 class="page-title">All Products</h1>

    <!-- FILTER BAR -->
    <div class="filter-bar">

        <input
            type="text"
            id="search-input"
            placeholder="🔍 Search products..."
            autocomplete="off"
            onkeyup="search()" 
        >

        <select id="category-filter">
            <option value="">All Categories</option>

            <?php foreach ($categories as $cat): ?>
                <option value="<?= $cat['id'] ?>">
                    <?= htmlspecialchars($cat['name']) ?>
                </option>
            <?php endforeach; ?>

        </select>

    </div>

    <!-- PRODUCT GRID -->
    <div id="product-grid">

        <?php if (empty($initialProducts)): ?>
            <p style="color:#888;text-align:center;grid-column:1/-1;padding:2rem;">
                No products available.
            </p>
        <?php else: ?>

            <?php foreach ($initialProducts as $p): ?>

                <div class="product-card">

                    <!-- PRODUCT IMAGE -->
                    <a href="productDetail.php?id=<?= $p['id'] ?>">

                        <?php if (!empty($p['primary_image_path'])): ?>
                            <img
                                src="../<?= htmlspecialchars($p['primary_image_path']) ?>"
                                alt="<?= htmlspecialchars($p['name']) ?>"
                                loading="lazy"
                            >
                        <?php else: ?>
                            <div class="no-img">No Image</div>
                        <?php endif; ?>

                    </a>

                    <div class="card-body">

                        <!-- CATEGORY -->
                        <div class="card-category">
                            <?= htmlspecialchars($p['category_name'] ?? 'Uncategorized') ?>
                        </div>

                        <!-- NAME -->
                        <div class="card-title">
                            <?= htmlspecialchars($p['name']) ?>
                        </div>

                        <!-- RATING -->
                        <div class="card-rating">

                            <?php
                            $avg = isset($p['avg_rating']) ? floatval($p['avg_rating']) : 0;
                            $reviewCount = $p['review_count'] ?? 0;

                            for ($i = 1; $i <= 5; $i++):
                            ?>
                                <span class="<?= $i <= round($avg) ? 'stars' : 'stars-empty' ?>">
                                    ★
                                </span>
                            <?php endfor; ?>

                            <?= number_format($avg, 1) ?>

                            <span>
                                (<?= $reviewCount ?> review<?= $reviewCount != 1 ? 's' : '' ?>)
                            </span>

                        </div>

                        <!-- PRICE -->
                        <div class="card-price">
                            ৳<?= number_format($p['price'], 2) ?>
                        </div>

                        <!-- STOCK + CART -->
                        <?php if (($p['stock_qty'] ?? 0) > 0): ?>

                            <button
                                class="btn btn-primary"
                                onclick="addToCart(<?= $p['id'] ?>, this)"
                            >
                                Add to Cart
                            </button>

                        <?php else: ?>

                            <button class="btn btn-disabled" disabled>
                                Out of Stock
                            </button>

                        <?php endif; ?>

                    </div>
                </div>

            <?php endforeach; ?>

        <?php endif; ?>

    </div>
</div>

<!-- TOAST -->
<div id="toast" class="toast"></div>

<!-- JS -->
<script src="../Controller/JS/search.js"></script>

</body>
</html>

<?php $connection->close(); ?>