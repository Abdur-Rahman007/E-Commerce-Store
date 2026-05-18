<?php
include "../model/categoryModel.php";
session_start();

$db = new DatabaseConnection();
$connection = $db->openConnection();
$categoryModel = new CategoryModel();

if(isset($_GET["action"])){
    $action = $_GET["action"];
}
elseif(isset($_POST["action"])){
    $action = $_POST["action"];
}
else{
    $action = "list";
}

if($action == "save"){
    $id = $_POST["id"] ?? "";
    $name = trim($_POST["name"] ?? "");
    $parent_id = $_POST["parent_id"] ?? "";

    if(!$name){
        $_SESSION["categoryError"] = "Category name is required";
        Header("Location: ../view/admin/categories/categoryForm.php?id=$id");
        exit();
    }

    if($id){
        $result = $categoryModel->updateCategory($connection, $id, $name, $parent_id);
        $_SESSION["categoryMessage"] = $result ? "Category updated successfully" : "Category update failed";
    }else{
        $result = $categoryModel->addCategory($connection, $name, $parent_id);
        $_SESSION["categoryMessage"] = $result ? "Category added successfully" : "Category add failed";
    }

    Header("Location: ../view/admin/categories/categoryList.php");
    exit();
}


if($action == "delete"){
    $id = $_GET["id"] ?? "";
    if($id){
        $result = $categoryModel->deleteCategory($connection, $id);
        $_SESSION["categoryMessage"] = $result ? "Category deleted successfully" : "Category cannot be deleted because it is used";
    }

    Header("Location: ../view/admin/categories/categoryList.php");
    exit();
}

Header("Location: ../view/admin/categories/categoryList.php");
exit();
?>