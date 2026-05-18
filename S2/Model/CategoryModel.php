<?php
include "db.php";

class CategoryModel{
    function getAllCategories($connection){
        $sql = "SELECT categories.*, parent_categories.name AS parent_name
                FROM categories
                LEFT JOIN categories AS parent_categories
                ON categories.parent_id = parent_categories.id
                ORDER BY categories.id DESC";
        $result = $connection->query($sql);
        return $result;
    }

    function getCategoryById($connection, $id){
        $sql = "SELECT * FROM categories WHERE id='".$id."'";
        $result = $connection->query($sql);
        return $result;
    }

    function addCategory($connection, $name, $parent_id){
        $name = $connection->real_escape_string($name);
        $parentValue = $parent_id ? "'".$parent_id."'" : "NULL";
        $sql = "INSERT INTO categories (name, parent_id) VALUES('".$name."', ".$parentValue.")";
        $result = $connection->query($sql);
        return $result;
    }

    function updateCategory($connection, $id, $name, $parent_id){
        $name = $connection->real_escape_string($name);
        $parentValue = $parent_id ? "'".$parent_id."'" : "NULL";
        $sql = "UPDATE categories SET name='".$name."', parent_id=".$parentValue." WHERE id='".$id."'";
        $result = $connection->query($sql);
        return $result;
    }

    function deleteCategory($connection, $id){
        $sql = "DELETE FROM categories WHERE id='".$id."'";
        $result = $connection->query($sql);
        return $result;
    }
}

?>