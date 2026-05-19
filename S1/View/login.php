<?php
session_start();
include "../Model/DatabaseConnection.php";

if (empty($_SESSION["user_id"]) && isset($_COOKIE["remember_token"])) {
    $db         = new DatabaseConnection();
    $connection = $db->openConnection();
    $user       = $db->getUserByToken($connection, $_COOKIE["remember_token"]);
    if ($user) {
        $_SESSION["user_id"] = $user["id"];
        $_SESSION["name"]    = $user["name"];
        $_SESSION["role"]    = $user["role"];
    }
    if (!empty($_SESSION["user_id"])) {
        if ($_SESSION["role"] === "admin") {
            header("Location: admin_dashboard.php");
        } else {
            header("Location: catalogue.php");
        }
        exit();

    }
}



$loginEmailError = $_SESSION["loginEmailError"] ?? "";
$loginPasswordError = $_SESSION["loginPasswordError"] ?? "";
$loginGeneralError = $_SESSION["loginGeneralError"] ?? "";
$email = $_SESSION["email"]  ?? "";
$successMsg = $_SESSION["successMsg"] ?? "";

unset($_SESSION["loginEmailError"], $_SESSION["loginPasswordError"],
      $_SESSION["loginGeneralError"], $_SESSION["loginOldEmail"], $_SESSION["successMsg"]);
?>

<html>
<head>
    <link rel="stylesheet" href="CSS/style.css" />
</head>
<body class="auth-page">

<div class="auth-card">
    <div class="auth-header">
        <h1>Welcome Back</h1>
        <p>Log in to your account</p>
    </div>

    <?php if ($successMsg): ?> <p style="color:green;"><?php echo $successMsg; ?></p> <?php endif; ?>

    <?php if ($loginGeneralError): ?> <p style="color:red;"><?php echo $loginGeneralError; ?></p> <?php endif; ?>

    <form id="loginForm" method="POST" action="../Controller/loginHandler.php" >
        <!-- Email -->
        <div class="form-group">
                <label>Email Address <span class="required">*</span></label>
                <input type="email" id="email" name="email" value="<?php echo $email; ?>" placeholder="Your Email" />
                <label style="color:red" class="error-msg" id="emailError"><?php echo $loginEmailError; ?></label>
        </div>

        <!-- Password -->
        <div class="form-group">
            <label>Password <span class="required">*</span></label>
            <div class="input-icon-wrap">
                <input type="password" id="password" name="password" placeholder="Your password" />
            </div>
            <label style="color:red" class="error-msg" id="passwordError"><?php echo $loginPasswordError; ?></label>
        </div>

        <!-- Remember Me -->
        <div class="form-group form-check">
            <label class="check-label">
                <input type="checkbox" name="remember_me" id="remember_me" />
                <span>Remember me for 30 days</span>
            </label>
        </div>

        <button type="submit" class="btn btn-primary btn-block">Log In</button>
    </form>

    <p class="auth-footer">
        Don't have an account? <a href="register.php">Sign up</a>
    </p>
</div>

<script src="../Controller/JS/loginValidation.js"></script>
</body>
</html>
