<?php
include "../model/ProductModel.php";
session_start();

$db = new DatabaseConnection();
$connection = $db->openConnection();
$productModel = new ProductModel();

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
    $category_id = $_POST["category_id"] ?? "";
    $name = trim($_POST["name"] ?? "");
    $description = trim($_POST["description"] ?? "");
    $price = $_POST["price"] ?? "";
    $stock_qty = $_POST["stock_qty"] ?? 0;
    $old_image_path = $_POST["old_image_path"] ?? "";
    $is_available = isset($_POST["is_available"]) ? 1 : 0;
    $uploadFile = $_FILES["primary_image"] ?? "";

    if(!$category_id || !$name || !$price){
        $_SESSION["productError"] = "Category, name and price are required";
        Header("Location: ../view/admin/products/productForm.php?id=$id");
        exit();
    }

    if($price <= 0){
        $_SESSION["productError"] = "Price must be greater than zero";
        Header("Location: ../view/admin/products/productForm.php?id=$id");
        exit();
    }

    $image_path = $old_image_path;
    if($uploadFile && $uploadFile["name"]){
        $uploadDirectory = "../public/uploads/products/";
        $path = $uploadDirectory . basename($uploadFile["name"]);
        $response = move_uploaded_file($uploadFile["tmp_name"], $path);
        $image_path = "public/uploads/products/" . basename($uploadFile["name"]);
    }

    if($id){
        $result = $productModel->updateProduct($connection, $id, $category_id, $name, $description, $price, $stock_qty, $image_path, $is_available);
        $_SESSION["productMessage"] = $result ? "Product updated successfully" : "Product update failed";
    }else{
        $result = $productModel->addProduct($connection, $category_id, $name, $description, $price, $stock_qty, $image_path, $is_available);
        $_SESSION["productMessage"] = $result ? "Product added successfully" : "Product add failed";
    }

    Header("Location: ../view/admin/products/productList.php");
    exit();
}

if($action == "delete"){
    $id = $_GET["id"] ?? "";
    if($id){
        $result = $productModel->deleteProduct($connection, $id);
        $_SESSION["productMessage"] = $result ? "Product deleted successfully" : "Product cannot be deleted because it is used";
    }

    Header("Location: ../view/admin/products/productList.php");
    exit();
}

Header("Location: ../view/admin/products/productList.php");
exit();

?>