<?php
include "../../../model/CategoryModel.php";
session_start();

$message = $_SESSION["categoryMessage"] ?? "";
unset($_SESSION["categoryMessage"]);

$db = new DatabaseConnection();
$connection = $db->openConnection();
$categoryModel = new CategoryModel();
$result = $categoryModel->getAllCategories($connection);
?>


<html>
    <head>
        <title>Category List</title>
        <link rel="stylesheet" href="category.css"/>
    </head>
    <body>
        <div class="container">
            <h2>Category List</h2>
            <p><a href="../dashboard.php">Dashboard</a> | <a href="categoryForm.php">Add New Category</a></p>

            <p class="message"><?php echo $message; ?></p>

            <table border="1">
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Parent Category</th>
                    <th>Action</th>
                </tr>

                <?php
                if($result && $result->num_rows > 0){
                    while($row = $result->fetch_assoc()){
                        $id = $row["id"];
                        $name = $row["name"];
                        $parentName = $row["parent_name"] ? $row["parent_name"] : "Main Category";

                        echo "<tr>
                                <td>$id</td>
                                <td>$name</td>
                                <td>$parentName</td>
                                <td>
                                    <a href='categoryForm.php?id=$id'>Edit</a> |
                                    <a href='../../../controller/CategoryController.php?action=delete&id=$id' onclick=\"return confirm('Are you sure?')\">Delete</a>
                                </td>
                              </tr>";
                    }
                }else{
                    echo "<tr><td colspan='4'>No category found</td></tr>";
                }
                ?>
            </table>
        </div>
    </body>
</html>