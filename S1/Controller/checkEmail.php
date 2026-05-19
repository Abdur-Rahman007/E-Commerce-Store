<?php
include "../Model/DatabaseConnection.php";

header("Content-Type: application/json");

$email = trim($_POST["email"] ?? "");

if (!$email || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(["available" => true]);
    exit();
}

$db         = new DatabaseConnection();
$connection = $db->openConnection();
$exists     = $db->emailExists($connection, $email);

echo json_encode(["available" => !$exists]);
exit();
?>
