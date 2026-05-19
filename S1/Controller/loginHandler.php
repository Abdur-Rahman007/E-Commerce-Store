<?php
include "../Model/DatabaseConnection.php";
session_start();

$email    = trim($_POST["email"] ?? "");
$password = $_POST["password"] ?? "";
$remember = isset($_POST["remember_me"]);

$hasEmailError = false;
$hasPasswordError = false;

if (!$email) {
    $hasEmailError = true;
    $_SESSION["loginEmailError"] = "Email is required";
} 
else{
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $hasEmailError = true;
        $_SESSION["loginEmailError"] = "Enter a valid email address.";
    }
    else {
        unset($_SESSION["loginEmailError"]);
    }
} 

if (!$password) {
    $hasPasswordError = true;
    $_SESSION["loginPasswordError"] = "Password is required";
} 
else {
    unset($_SESSION["loginPasswordError"]);
}

$_SESSION["email"] = $email;

if ($hasEmailError || $hasPasswordError) {
    header("Location: ../View/login.php");
    exit();
}

$db         = new DatabaseConnection();
$connection = $db->openConnection();
$user       = $db->getUserByEmail($connection, $email);

if (!$user || !password_verify($password, $user["password_hash"])) {
    $_SESSION["loginGeneralError"] = "Invalid email or password.";
    header("Location: ../View/login.php");
    exit();
}

$_SESSION["user_id"] = $user["id"];
$_SESSION["name"]    = $user["name"];
$_SESSION["role"]    = $user["role"];

if ($remember) {
    $token = bin2hex(random_bytes(32));
    $db->setRememberToken($connection, $user["id"], $token);
    setcookie("remember_token", $token, time() + (60 * 60 * 24 * 30), "/", "", false, true);
}

unset($_SESSION["loginEmailError"]);
unset($_SESSION["loginPasswordError"]);
unset($_SESSION["loginGeneralError"]); 
unset($_SESSION["loginOldEmail"]);


if ($user["role"] === "admin") {
    header("Location: ../../S2/View/admin/dashboard.php");
} else {
    header("Location: ../../S3/View/catalogue.php");
}
exit();
?>
