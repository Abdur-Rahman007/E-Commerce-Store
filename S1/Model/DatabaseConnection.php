<?php

class DatabaseConnection {

    function openConnection() {
        $db_host     = "localhost";
        $db_user     = "root";
        $db_password = "";
        $db_name     = "ecommerce_store";

        $connection = new mysqli($db_host, $db_user, $db_password, $db_name);
        if ($connection->connect_error) {
            die("Could not connect to the database: " . $connection->connect_error);
        }
        return $connection;
    }

    function emailExists($connection, $email) {
        $sql    = "SELECT id FROM users WHERE email='" . $email . "'";
        $result = $connection->query($sql);
        return $result->num_rows > 0;
    }

    
    function registerUser($connection, $name, $email, $phone, $passwordHash) {
        $sql    = "INSERT INTO users (name, email, phone, password_hash, role, shipping_addresses) VALUES ('" . $name . "', '" . $email . "', '" . $phone . "', '" . $passwordHash . "', 'customer', '[]')";
        $result = $connection->query($sql);
        return $result;
    }

}
?>
