<?php
include "../Model/DatabaseConnection.php";
session_start();

if (empty($_SESSION["user_id"])) {
    header("Location: ../View/login.php");
    exit();
}

$userId = $_SESSION["user_id"];
$action = $_POST["action"] ?? "";

$db = new DatabaseConnection();
$connection = $db->openConnection();

if ($action === "update_info") {

    $name  = trim($_POST["name"]      ?? "");
    $email = trim($_POST["email"]     ?? "");
    $phone = trim($_POST["phone"]     ?? "");
    $addr1 = trim($_POST["address_1"] ?? "");
    $addr2 = trim($_POST["address_2"] ?? "");

    $hasNameError  = false;
    $hasEmailError = false;

    if (!$name) {
        $hasNameError = true;
        $_SESSION["profileNameError"] = "Full name is required.";
    } else {
        unset($_SESSION["profileNameError"]);
    }

    if (!$email) {
        $hasEmailError = true;
        $_SESSION["profileEmailError"] = "Email is required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $hasEmailError = true;
        $_SESSION["profileEmailError"] = "Enter a valid email address.";
    } elseif ($db->emailExistsForOtherUser($connection, $email, $userId)) {
        $hasEmailError = true;
        $_SESSION["profileEmailError"] = "This email is already used by another account.";
    } else {
        unset($_SESSION["profileEmailError"]);
    }

    // Save old input for re-filling the form on error
    $_SESSION["profileOldName"]  = $name;
    $_SESSION["profileOldEmail"] = $email;
    $_SESSION["profileOldPhone"] = $phone;
    $_SESSION["profileOldAddr1"] = $addr1;
    $_SESSION["profileOldAddr2"] = $addr2;

    if ($hasNameError || $hasEmailError) {
        header("Location: ../View/profile.php");
        exit();
    }

    // Build JSON for shipping addresses (keep only non-empty)
    $addresses = array_values(array_filter([$addr1, $addr2]));
    $addressesJson = json_encode($addresses);

    $db->updateProfile($connection, $userId, $name, $email, $phone);
    $db->updateShippingAddresses($connection, $userId, $addressesJson);

    // Keep session name in sync
    $_SESSION["name"] = $name;

    // Clear old input
    unset($_SESSION["profileOldName"]);
    unset($_SESSION["profileOldEmail"]);
    unset($_SESSION["profileOldPhone"]);
    unset($_SESSION["profileOldAddr1"]);
    unset($_SESSION["profileOldAddr2"]);

    $_SESSION["profileSuccess"] = "Profile updated successfully.";
    header("Location: ../View/profile.php");
    exit();
}

if ($action === "change_password") {

    $current = $_POST["current_password"] ?? "";
    $new = $_POST["new_password"] ?? "";
    $confirm = $_POST["confirm_new"] ?? "";

    $hasCurrentError = false;
    $hasNewError = false;
    $hasConfirmError = false;

    if (!$current) {
        $hasCurrentError = true;
        $_SESSION["pwCurrentError"] = "Current password is required.";
    } else {
        unset($_SESSION["pwCurrentError"]);
    }

    if (!$new) {
        $hasNewError = true;
        $_SESSION["pwNewError"] = "New password is required.";
    } elseif (strlen($new) < 8) {
        $hasNewError = true;
        $_SESSION["pwNewError"] = "New password must be at least 8 characters.";
    } else {
        unset($_SESSION["pwNewError"]);
    }

    if (!$confirm) {
        $hasConfirmError = true;
        $_SESSION["pwConfirmError"] = "Please confirm your new password.";
    } elseif ($new !== $confirm) {
        $hasConfirmError = true;
        $_SESSION["pwConfirmError"] = "Passwords do not match.";
    } else {
        unset($_SESSION["pwConfirmError"]);
    }

    if ($hasCurrentError || $hasNewError || $hasConfirmError) {
        header("Location: ../View/profile.php");
        exit();
    }

    $user = $db->getUserById($connection, $userId);
    if (!$user || !password_verify($current, $user["password_hash"])) {
        $_SESSION["pwCurrentError"] = "Current password is incorrect.";
        header("Location: ../View/profile.php");
        exit();
    }

    $newHash = password_hash($new, PASSWORD_BCRYPT);
    $db->updatePassword($connection, $userId, $newHash);

    $_SESSION["profileSuccess"] = "Password changed successfully.";
    header("Location: ../View/profile.php");
    exit();
}

header("Location: ../View/profile.php");
exit();
?>
