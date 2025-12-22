<?php
session_start();
require "../db.php";

header('Content-Type: application/json');

if (!isset($_SESSION["user_id"])) {
    echo json_encode(['success' => false, 'login_required' => true, 'message' => 'Login required']);
    exit;
}

$user_id = (int) $_SESSION["user_id"];
$flower_id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
if ($flower_id <= 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid id']);
    exit;
}

$checkSql = "SELECT id FROM cart WHERE user_id = ? AND flower_id = ? LIMIT 1";
$checkStmt = mysqli_prepare($conn, $checkSql);
mysqli_stmt_bind_param($checkStmt, "ii", $user_id, $flower_id);
mysqli_stmt_execute($checkStmt);
mysqli_stmt_store_result($checkStmt);
$exists = mysqli_stmt_num_rows($checkStmt) > 0;
mysqli_stmt_close($checkStmt);

if ($exists) {
    $delSql = "DELETE FROM cart WHERE user_id = ? AND flower_id = ?";
    $delStmt = mysqli_prepare($conn, $delSql);
    mysqli_stmt_bind_param($delStmt, "ii", $user_id, $flower_id);
    $ok = mysqli_stmt_execute($delStmt);
    mysqli_stmt_close($delStmt);
    if ($ok) {
        echo json_encode(['success' => true, 'in_cart' => 0, 'message' => 'Removed']);
        exit;
    }
    echo json_encode(['success' => false, 'message' => 'DB error']);
    exit;
}

$insSql = "INSERT INTO cart (user_id, flower_id) VALUES (?, ?)";
$insStmt = mysqli_prepare($conn, $insSql);
mysqli_stmt_bind_param($insStmt, "ii", $user_id, $flower_id);
$ok = mysqli_stmt_execute($insStmt);
mysqli_stmt_close($insStmt);
if ($ok) {
    echo json_encode(['success' => true, 'in_cart' => 1, 'message' => 'Added']);
    exit;
}

echo json_encode(['success' => false, 'message' => 'DB error']);
