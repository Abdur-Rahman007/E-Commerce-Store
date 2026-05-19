<?php
include "../Model/DatabaseConnection.php";
session_start();

if (!empty($_SESSION["user_id"])) {
    $db         = new DatabaseConnection();
    $connection = $db->openConnection();
    $db->clearRememberToken($connection, $_SESSION["user_id"]);
}

if (isset($_COOKIE["remember_token"])) {
    setcookie("remember_token", "", - 1 , "/");
}

session_unset();
session_destroy();

header("Location: ../View/login.php");
exit();
?>