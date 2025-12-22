<?php 
include "admin-header.php"; 
require "../db.php"; 

$id = isset($_GET["id"]) ? (int) $_GET["id"] : 0;

$stmt = mysqli_prepare($conn, "SELECT * FROM flowers WHERE id = ?");
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$flower = mysqli_fetch_assoc($result);
mysqli_stmt_close($stmt);

$categories = mysqli_query($conn, "SELECT * FROM categories");

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $name = isset($_POST['name']) ? trim($_POST['name']) : '';
    $sku = isset($_POST['sku']) ? trim($_POST['sku']) : '';
    $category_id = isset($_POST['category_id']) ? (int) $_POST['category_id'] : 0;
    $price = isset($_POST['price']) && $_POST['price'] !== '' ? (float) $_POST['price'] : 0.00;
    $stock = isset($_POST['stock']) && $_POST['stock'] !== '' ? (int) $_POST['stock'] : 0;
    $color = isset($_POST['color']) ? trim($_POST['color']) : '';
    $description = isset($_POST['description']) ? trim($_POST['description']) : '';
    $image = isset($_POST['image']) ? trim($_POST['image']) : '';
    $visible = isset($_POST['visible']) ? 1 : 0;

    $sql = "UPDATE flowers SET 
              name=?, sku=?, category_id=?, price=?, stock=?, color=?, description=?, image=?, visible=? 
            WHERE id=?";

    $stmt = mysqli_prepare($conn, $sql);
    if ($stmt === false) {
        die("Prepare failed: " . mysqli_error($conn));
    }

    $types = "ssidisssii";
    mysqli_stmt_bind_param(
        $stmt,
        $types,
        $name,
        $sku,
        $category_id,
        $price,
        $stock,
        $color,
        $description,
        $image,
        $visible,
        $id
    );

    $exec = mysqli_stmt_execute($stmt);
    if ($exec === false) {
        die("Execute failed: " . mysqli_stmt_error($stmt));
    }

    mysqli_stmt_close($stmt);

    header("Location: content-list.php");
    exit;
}
?>
