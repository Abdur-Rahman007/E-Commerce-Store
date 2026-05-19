<?php
session_start();
include "../Model/DatabaseConnection.php";

if (empty($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}

$db = new DatabaseConnection();
$connection = $db->openConnection();
$user = $db->getUserById($connection, $_SESSION["user_id"]);

if (!$user) {
    session_destroy();
    header("Location: login.php");
    exit();
}

// Decode saved shipping addresses
$addresses = json_decode($user["shipping_addresses"] ?? "[]", true);
$addr1     = $addresses[0] ?? "";
$addr2     = $addresses[1] ?? "";

// Pull errors, old values, and success message
$profNameError  = $_SESSION["profileNameError"]  ?? "";
$profEmailError = $_SESSION["profileEmailError"] ?? "";
$pwCurrentError = $_SESSION["pwCurrentError"]    ?? "";
$pwNewError     = $_SESSION["pwNewError"]        ?? "";
$pwConfirmError = $_SESSION["pwConfirmError"]    ?? "";
$successMsg     = $_SESSION["profileSuccess"]    ?? "";

// Use old POSTed values if validation failed, else use DB values
$dispName  = $_SESSION["profileOldName"]  ?? $user["name"];
$dispEmail = $_SESSION["profileOldEmail"] ?? $user["email"];
$dispPhone = $_SESSION["profileOldPhone"] ?? $user["phone"];
$dispAddr1 = $_SESSION["profileOldAddr1"] ?? $addr1;
$dispAddr2 = $_SESSION["profileOldAddr2"] ?? $addr2;

unset($_SESSION["profileNameError"]);
unset($_SESSION["profileEmailError"]);
unset($_SESSION["pwCurrentError"]);
unset($_SESSION["pwNewError"]);
unset($_SESSION["pwConfirmError"]);
unset($_SESSION["profileSuccess"]);
unset($_SESSION["profileOldName"]);
unset($_SESSION["profileOldEmail"]);
unset($_SESSION["profileOldPhone"]);
unset($_SESSION["profileOldAddr1"]);
unset( $_SESSION["profileOldAddr2"]);
?>

<html>
<head>
    <title>My Profile</title>
    <link rel="stylesheet" href="CSS/style.css" />
</head>
<body class="dashboard-page">

<nav class="navbar">
    <div class="nav-brand">🛍 EStore</div>
    <div class="nav-links">
        <a href="../../S3/View/catalogue.php">Go To Shop</a>
        <a href="profile.php" class="active">My Profile</a>
        <a href="../../S4/Views/orders/my_orders.php">My Orders</a>
        <a href="../Controller/logout.php" class="btn btn-sm btn-outline">Logout</a>
    </div>
</nav>

<main class="container">
    <h2 class="page-title">My Profile</h2>

    <?php if ($successMsg): ?> <p style="color:green;"><?php echo $successMsg; ?></p> <?php endif; ?>

    <section class="card">
        <h3 class="card-title">Personal Information</h3>

        <form id="profileForm" method="POST" action="../Controller/profileHandler.php">
            <input type="hidden" name="action" value="update_info" />

            <!-- Name -->
            <div class="form-group">
                <label >Full Name <span class="required">*</span></label>
                <input type="text" id="name" name="name" value="<?php echo $dispName; ?>" placeholder="Your Full Name"/>
                <label style="color:red;" id="profNameError"><?php echo $profNameError; ?> </label>
            </div>

            <!-- Email -->
            <div class="form-group">
                <label>Email Address <span class="required">*</span></label>
                <input type="email" id="email" name="email"  value="<?php echo $dispEmail; ?>" placeholder="Your Email"/>
                <label style="color:red;" id="profEmailError"><?php echo $profEmailError; ?></label>
            </div>

            <!-- Phone -->
            <div class="form-group">
                <label>Phone <span class="optional">(optional)</span></label>
                <input type="tel" id="phone" name="phone" value="<?= $dispPhone ?>" placeholder="+8801XXXXXXXXX"/>
                <label style="color:red;" id="profPhoneError"></label>
            </div>

            <hr class="divider" />
            <h4>Saved Shipping Addresses <span class="optional">(up to 2)</span></h4>

            <!-- Address 1 -->
            <div class="form-group">
                <label>Address 1</label>
                <input type="text" id="address_1" name="address_1" value="<?php $dispAddr1; ?>" placeholder="House, Road, Area, City"/>
            </div>

            <!-- Address 2 -->
            <div class="form-group">
                <label>Address 2</label>
                <input type="text" id="address_2" name="address_2" value="<?php $dispAddr2; ?>" placeholder="Optional second address"/>
            </div>

            <button type="submit" class="btn btn-primary">Save Changes</button>
        </form>
    </section>

    <section class="card">
        <h3 class="card-title">Change Password</h3>

        <form id="passwordForm" method="POST" action="../Controller/profileHandler.php">
            <input type="hidden" name="action" value="change_password" />

            <!-- Current Password -->
            <div class="form-group">
                <label for="current_password">Current Password <span class="required">*</span></label>
                <div class="input-icon-wrap">
                    <input type="password" id="current_password" name="current_password" placeholder="Enter current password"/>
                </div>
                <label style="color:red;"><?php echo $pwCurrentError; ?></label>
            </div>

            <!-- New Password -->
            <div class="form-group">
                <label for="new_password">New Password <span class="required">*</span></label>
                <div class="input-icon-wrap">
                    <input type="password" id="new_password" name="new_password" placeholder="Minimum 8 characters"/>
                </div>
                <label style="color:red;" id="pwNewError"><?php echo $pwNewError; ?></label>
            </div>

            <!-- Confirm New -->
            <div class="form-group">
                <label>Confirm New Password <span class="required">*</span></label>
                <div class="input-icon-wrap">
                    <input type="password" id="confirm_new" name="confirm_new" placeholder="Repeat new password"/>
                </div>
               <label style="color:red;" id="pwConfirmError"><?php echo $pwConfirmError; ?></label>
            </div>

            <button type="submit" class="btn btn-warning">Change Password</button>
        </form>
    </section>
</main>

<script src="../Controller/JS/profileValidation.js"></script>
</body>
</html>
