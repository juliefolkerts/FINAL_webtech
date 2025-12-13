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

// check existing
$checkSql = "SELECT id FROM favourites WHERE user_id = ? AND flower_id = ? LIMIT 1";
$checkStmt = mysqli_prepare($conn, $checkSql);
mysqli_stmt_bind_param($checkStmt, "ii", $user_id, $flower_id);
mysqli_stmt_execute($checkStmt);
mysqli_stmt_store_result($checkStmt);
$exists = mysqli_stmt_num_rows($checkStmt) > 0;
mysqli_stmt_close($checkStmt);

if ($exists) {
    $delSql = "DELETE FROM favourites WHERE user_id = ? AND flower_id = ?";
    $delStmt = mysqli_prepare($conn, $delSql);
    mysqli_stmt_bind_param($delStmt, "ii", $user_id, $flower_id);
    $ok = mysqli_stmt_execute($delStmt);
    mysqli_stmt_close($delStmt);
    if ($ok) {
        echo json_encode(['success' => true, 'is_favourite' => 0, 'message' => 'Removed']);
        exit;
    } else {
        echo json_encode(['success' => false, 'message' => 'DB error']);
        exit;
    }
} else {
    $insSql = "INSERT INTO favourites (user_id, flower_id) VALUES (?, ?)";
    $insStmt = mysqli_prepare($conn, $insSql);
    mysqli_stmt_bind_param($insStmt, "ii", $user_id, $flower_id);
    $ok = mysqli_stmt_execute($insStmt);
    mysqli_stmt_close($insStmt);
    if ($ok) {
        echo json_encode(['success' => true, 'is_favourite' => 1, 'message' => 'Added']);
        exit;
    } else {
        echo json_encode(['success' => false, 'message' => 'DB error']);
        exit;
    }
}
