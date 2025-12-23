<?php
session_start();
require "../db.php";

if (!isset($_SESSION["user_id"]) || $_SESSION["role"] !== "admin") {
    header("Location: ../front/login.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: orders.php");
    exit;
}

$orderId = isset($_POST["id"]) ? (int) $_POST["id"] : 0;
if ($orderId <= 0) {
    header("Location: orders.php");
    exit;
}

$updateSql = "UPDATE orders SET status = 'completed' WHERE id = ?";
$updateStmt = mysqli_prepare($conn, $updateSql);
mysqli_stmt_bind_param($updateStmt, "i", $orderId);
mysqli_stmt_execute($updateStmt);
mysqli_stmt_close($updateStmt);

header("Location: order-details.php?id=" . $orderId);
exit;
