<?php 
include "../Model/DatabaseConnection.php";
session_start();

$nameError = $_SESSION["nameError"] ?? "";
$emailError = $_SESSION["emailError"] ?? "";
$phoneError = $_SESSION["phoneError"] ?? "";
$passwordError = $_SESSION["passwordError"] ?? "";
$confirmPasswordError = $_SESSION["confirm_password"] ?? "";
$generalError  = $_SESSION["generalError"]  ?? "";

$name = $_SESSION["name"] ?? "";
$email = $_SESSION["email"] ?? "";
$phone =  $_SESSION["phone"] ?? "";

$loggingError = $_SESSION["loggingError"] ?? "";
$isLoggedIn = $_SESSION["isLoggedIn"] ?? "";

if($isLoggedIn){
   // Header("Location: dashboard.php");
    exit();
}

unset($_SESSION["nameError"]);
unset($_SESSION["emailError"]);
unset($_SESSION["phoneError"]);
unset($_SESSION["passwordError"]);
unset($_SESSION["confirm_password"]);
unset($_SESSION["generalError"] );

unset($_SESSION["name"]);
unset($_SESSION["email"]);
unset($_SESSION["phone"]);

?>

<html>
    <head>
    <title>Register — E-Commerce Store</title>
    <link rel="stylesheet" href="CSS/style.css" />
    </head>
    <body class="auth-page">

    <div class="auth-card">
        <div class="auth-header">
            <h1>Create Account</h1>
            <?php if ($generalError): ?><p style="color:red;"><?php echo $generalError; ?></p><?php endif; ?>
        </div>
        <form id="registerForm" method="POST" action="../Controller/registerHandler.php" enctype="multipart/form-data" >
            <!-- Full Name -->
            <div class="form-group">
                <label>Full Name <span class="required">*</span></label>
                <input type="text" id="name" name="name" value="<?php echo $name ?>" placeholder="Enter your Full Name"/>
                <label style="color:red" class="error-msg" id="nameError"><?php echo $nameError; ?></label>
            </div>

            <!-- Email -->
            <div class="form-group">
                <label>Email Address <span class="required">*</span></label>
                <input type="email" id="email" name="email" onkeyup="checkEmail()" value="<?php echo $email ?>" placeholder="Enter your Email" />
                 <label style="color:red" class="error-msg" id="emailError"><?php echo $emailError; ?></label>
            </div>

            <!-- Phone -->
            <div class="form-group">
                <label>Phone <span class="optional">(optional)</span></label>
                <input type="tel" id="phone" name="phone" value="<?php echo $phone?>" placeholder="e.g. +8801XXXXXXXXX"/>
                <label style="color:red" class="error-msg" id="phoneError"><?php echo $phoneError; ?></label>
            </div>
            <!-- Password -->
            <div class="form-group <?= isset($errors['password']) ? 'has-error' : '' ?>">
                <label>Password <span class="required">*</span></label>
                <div class="input-icon-wrap">
                    <input type="password" id="password" name="password" placeholder="Minimum 8 characters" />
                    <button type="button" class="toggle-pw" data-target="password" title="Show/Hide">👁</button>
                </div>
                <label style="color:red" class="error-msg" id="passwordError"><?php echo $passwordError; ?></label>
            </div>

            <!-- Confirm Password -->
            <div class="form-group <?= isset($errors['confirm_password']) ? 'has-error' : '' ?>">
                <label for="confirm_password">Confirm Password <span class="required">*</span></label>
                <div class="input-icon-wrap">
                    <input type="password" id="confirm_password" name="confirm_password" placeholder="Re-enter your password" />
                    <button type="button" class="toggle-pw" data-target="confirm_password" title="Show/Hide">👁</button>
                </div>
                <label style="color:red" class="error-msg" id="confirmError"><?php echo $confirmPasswordError; ?></label>
            </div>
            <button type="submit" class="btn btn-primary btn-block">Create Account</button>
        </form>
        <p class="auth-footer">
            Already have an account? <a href="login.php">Log in</a>
        </p>
    </div>
    <script src="../Controller/JS/checkEmail.js"></script>
    <script src="../Controller/JS/registerValidation.js"></script>
    </body>



</html>