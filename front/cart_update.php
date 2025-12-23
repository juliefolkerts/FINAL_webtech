<?php
session_start();
require "../db.php";

header('Content-Type: application/json');

if (!isset($_SESSION["user_id"])) {
    echo json_encode(['success' => false, 'login_required' => true, 'message' => 'Login required']);
    exit;
}

$user_id = (int) $_SESSION["user_id"];
$flower_id = isset($_POST['id']) ? (int) $_POST['id'] : 0;
$quantity = isset($_POST['quantity']) ? (int) $_POST['quantity'] : 1;

if ($flower_id <= 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid id']);
    exit;
}

if ($quantity < 1) {
    $quantity = 1;
}

$checkSql = "SELECT id FROM cart WHERE user_id = ? AND flower_id = ? LIMIT 1";
$checkStmt = mysqli_prepare($conn, $checkSql);
mysqli_stmt_bind_param($checkStmt, "ii", $user_id, $flower_id);
mysqli_stmt_execute($checkStmt);
mysqli_stmt_store_result($checkStmt);
$exists = mysqli_stmt_num_rows($checkStmt) > 0;
mysqli_stmt_close($checkStmt);

if ($exists) {
    $updateSql = "UPDATE cart SET quantity = ? WHERE user_id = ? AND flower_id = ?";
    $updateStmt = mysqli_prepare($conn, $updateSql);
    mysqli_stmt_bind_param($updateStmt, "iii", $quantity, $user_id, $flower_id);
    $ok = mysqli_stmt_execute($updateStmt);
    mysqli_stmt_close($updateStmt);
    if ($ok) {
        echo json_encode(['success' => true, 'quantity' => $quantity]);
        exit;
    }
    echo json_encode(['success' => false, 'message' => 'DB error']);
    exit;
}

$insSql = "INSERT INTO cart (user_id, flower_id, quantity) VALUES (?, ?, ?)";
$insStmt = mysqli_prepare($conn, $insSql);
mysqli_stmt_bind_param($insStmt, "iii", $user_id, $flower_id, $quantity);
$ok = mysqli_stmt_execute($insStmt);
mysqli_stmt_close($insStmt);

if ($ok) {
    echo json_encode(['success' => true, 'quantity' => $quantity]);
    exit;
}

echo json_encode(['success' => false, 'message' => 'DB error']);
