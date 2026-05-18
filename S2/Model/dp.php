<?php
class DatabaseConnection{
    function openConnection()
    {
        $db_host = "localhost";
        $db_user = "root";
        $db_password = "";
        $db_name = "ecommerce_store";

        $connection = new mysqli($db_host, $db_user, $db_password, $db_name);
        if($connection->connect_error){
            die("Could not connect to the database- ". $connection->connect_error);
        }

        return $connection;
    }
}

?>