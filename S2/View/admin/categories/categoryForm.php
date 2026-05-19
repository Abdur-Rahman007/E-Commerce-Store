<?php
include "../../../model/CategoryModel.php";
session_start();

$error = $_SESSION["categoryError"] ?? "";
unset($_SESSION["categoryError"]);

$id = $_GET["id"] ?? "";
$name = "";
$parent_id = "";

$db = new DatabaseConnection();
$connection = $db->openConnection();
$categoryModel = new CategoryModel();

if($id){
    $categoryResult = $categoryModel->getCategoryById($connection, $id);
    if($categoryResult && $categoryResult->num_rows == 1){
        $category = $categoryResult->fetch_assoc();
        $name = $category["name"];
        $parent_id = $category["parent_id"];
    }
}

$categories = $categoryModel->getAllCategories($connection);

?>

<html>
    <head>
        <title>Category Form</title>
        <link rel="stylesheet" href="category.css"/>
    </head>
    <body>
        <div class="container">
            <h2><?php echo $id ? "Edit Category" : "Add Category"; ?></h2>
            <p><a href="categoryList.php">Back to Category List</a></p>
            <p class="error"><?php echo $error; ?></p>

            <form method="post" action="../../../controller/CategoryController.php">
                <input type="hidden" name="action" value="save"/>
                <input type="hidden" name="id" value="<?php echo $id; ?>"/>

                <table>
                    <tr>
                        <td>Category Name</td>
                        <td><input type="text" name="name" value="<?php echo $name; ?>"/></td>
                    </tr>
                    <tr>
                        <td>Parent Category</td>
                        <td>
                            <select name="parent_id">
                                <option value="">Main Category</option>
                                <?php
                                if($categories && $categories->num_rows > 0){
                                    while($row = $categories->fetch_assoc()){
                                        if($row["id"] == $id){
                                            continue;
                                        }
                                        $selected = ($row["id"] == $parent_id) ? "selected" : "";
                                        $categoryName = $row["name"];
                                        echo "<option value='".$row["id"]."' $selected>$categoryName</option>";
                                    }
                                }
                                ?>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td></td>
                        <td><input type="submit" value="Save Category"/></td>
                    </tr>
                </table>
            </form>
        </div>
    </body>
</html>
