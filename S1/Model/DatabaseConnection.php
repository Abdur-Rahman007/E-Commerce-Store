<?php

class DatabaseConnection {

    function openConnection() {
        $db_host     = "localhost";
        $db_user     = "root";
        $db_password = "";
        $db_name     = "ecommerce_store";

        $connection = new mysqli($db_host, $db_user, $db_password, $db_name);
        if ($connection->connect_error) {
            die("Could not connect to the database: ".$connection->connect_error);
        }
        return $connection;
    }

    function emailExists($connection, $email) {
        $sql    = "SELECT id FROM users WHERE email='".$email."'";
        $result = $connection->query($sql);
        return $result->num_rows > 0;
    }

    function emailExistsForOtherUser($connection, $email, $userId) {
        $sql    = "SELECT id FROM users WHERE email='" . $email . "' AND id != '" . $userId . "'";
        $result = $connection->query($sql);
        return $result->num_rows > 0;
    }
    
    function registerUser($connection, $name, $email, $phone, $passwordHash) {
        $sql    = "INSERT INTO users (name, email, phone, password_hash, role, shipping_addresses) VALUES ('".$name."', '".$email."', '".$phone."', '".$passwordHash."','customer', '[]')";
        $result = $connection->query($sql);
        return $result;
    }

    function getUserByToken($connection, $token) {
        $sql    = "SELECT * FROM users WHERE remember_token='".$token."'";
        $result = $connection->query($sql);
        return $result->fetch_assoc();
    }

    function getUserByEmail($connection, $email) {
        $sql    = "SELECT * FROM users WHERE email='".$email."'";
        $result = $connection->query($sql);
        return $result->fetch_assoc();
    }

     function setRememberToken($connection, $userId, $token) {
        $sql    = "UPDATE users SET remember_token='".$token."' WHERE id='".$userId."'";
        $result = $connection->query($sql);
        return $result;
    }

    function clearRememberToken($connection, $userId) {
        $sql    = "UPDATE users SET remember_token=NULL WHERE id='" . $userId . "'";
        $result = $connection->query($sql);
        return $result;
    }
    
    function getUserById($connection, $id) {
        $sql    = "SELECT * FROM users WHERE id='" . $id . "'";
        $result = $connection->query($sql);
        return $result->fetch_assoc();
    }

    function updateProfile($connection, $userId, $name, $email, $phone) {
        $sql    = "UPDATE users SET name='" . $name . "', email='" . $email . "', phone='" . $phone . "' WHERE id='" . $userId . "'";
        $result = $connection->query($sql);
        return $result;
    }

    function updateShippingAddresses($connection, $userId, $addressesJson) {
        $sql    = "UPDATE users SET shipping_addresses='" . $addressesJson . "' WHERE id='" . $userId . "'";
        $result = $connection->query($sql);
        return $result;
    }

    function updatePassword($connection, $userId, $newHash) {
        $sql    = "UPDATE users SET password_hash='" . $newHash . "' WHERE id='" . $userId . "'";
        $result = $connection->query($sql);
        return $result;
    }

}
?>
