<?php
session_start();
?>

<html>
    <head>
        <title>Admin Dashboard</title>
        <link rel="stylesheet" href="dashboard.css"/>
    </head>
    <body>
        <div class="dashboard">
            <h2>E-Commerce Admin Dashboard</h2>
            <p>Manage category and product information from here.</p>

            <div class="menu">
                <a href="categories/categoryList.php">Category Management</a>
                <a href="products/productList.php">Product Management</a>
            </div>
        </div>
    </body>
</html>
