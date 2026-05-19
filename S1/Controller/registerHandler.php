<?php
include "../Model/DatabaseConnection.php";

session_start();

$name     = trim($_POST["name"]);
$email    = trim($_POST["email"]);
$phone    = trim($_POST["phone"]);
$password = $_POST["password"];
$confirm  = $_POST["confirm_password"];


$hasNameError = true;
$hasEmailError = true;
$hasPhoneError = true;
$hasPasswordError = true;
$hasConfirmPasswordError = true;

if (!$name){
    $hasNameError = true;
    $_SESSION["nameError"]= "Full name is required";
} 
else{
    if(strlen($name) < 3) {
        $hasNameError = true;
        $_SESSION["nameError"]= "Name must be at least 3 characters";
    }
    else{
        $hasNameError = false;
        unset($_SESSION["nameError"]);
    }
}

if (!$email) {
    $hasEmailError = true;
    $_SESSION["emailError"] = "Email address is required";
} 
else{
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
         $hasEmailError = true;
        $_SESSION["emailError"]= "Please enter a valid email address";
    }
    else{
        $hasEmailError = false;
        unset( $_SESSION["emailError"]);
    }
}

if ($phone && !preg_match('/^[0-9+\-\s()]{7,15}$/', $phone)) {
    $hasPhoneError = true;
    $_SESSION["phoneError"] = "Phone number format is invalid";
}
else{
    $hasPhoneError = false;
    unset($_SESSION["phoneError"]);
}

if (!$password) {
    $hasPasswordError = true;
    $_SESSION["passwordError"] = "Password is required";
} else{
    if (strlen($password) < 8) {
        $hasPasswordError = true;
        $_SESSION["passwordError"] = "Password must be at least 8 characters";
    }
    else{
        $hasPasswordError = false;
        unset($_SESSION["passwordError"]);
    }
}

if (!$confirm) {
    $hasConfirmPasswordError = true;
    $_SESSION["confirm_password"] = "Please confirm your password";
} else{
    if ($password !== $confirm) {
        $hasConfirmPasswordError = true;
        $_SESSION["confirm_password"]  = "Passwords do not match";
    }
    else{
        $hasConfirmPasswordError = false;
        unset($_SESSION["confirmPasswordError"]);
    }
}

if (empty($_SESSION["emailError"])) {
    $db  = new DatabaseConnection();
    $connection = $db->openConnection();
    if ($db->emailExists($connection, $email)) {
        $_SESSION["emailError"] = "This email is already registered";
    }
}

$_SESSION["name"] = $name;
$_SESSION["email"] = $email;
$_SESSION["phone"] = $phone;

if($hasNameError || $hasEmailError || $hasPhoneError || $hasPasswordError || $hasConfirmPasswordError){
    Header("Location: ../View/register.php");
    exit();
}

$db = new DatabaseConnection();
$connection = $db->openConnection();

if ($db->emailExists($connection, $email)) {
    $_SESSION["emailError"] = "This email is already registered.";
    header("Location: ../View/register.php");
    exit();
}

$hash   = password_hash($password, PASSWORD_BCRYPT);
$result = $db->registerUser($connection, $name, $email, $phone, $hash);

if ($result) {
    $_SESSION["successMsg"] = "Account created successfully! Please log in.";
    unset($_SESSION["name"]);
    unset($_SESSION["email"]); 
    unset($_SESSION["phone"]);
    header("Location: ../View/login.php");
} else {
    $_SESSION["generalError"] = "Registration failed. Please try again.";
    header("Location: ../View/register.php");
}
exit();
?>
