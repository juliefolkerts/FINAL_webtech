<?php
session_start();
require "../db.php";

header('Content-Type: application/json');

if (!isset($_SESSION["user_id"])) {
    echo json_encode(['success' => false, 'login_required' => true, 'message' => 'Login required']);
    exit;
}

$user_id = (int) $_SESSION["user_id"];

$sql = "SELECT c.flower_id, c.quantity, f.price
        FROM cart c
        JOIN flowers f ON f.id = c.flower_id
        WHERE c.user_id = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

$items = [];
$total = 0.0;

while ($row = mysqli_fetch_assoc($result)) {
    $quantity = (int) $row['quantity'];
    if ($quantity < 1) {
        $quantity = 1;
    }
    $price = (float) $row['price'];
    $items[] = [
        'flower_id' => (int) $row['flower_id'],
        'quantity' => $quantity,
        'price' => $price
    ];
    $total += ($price * $quantity);
}

if (count($items) === 0) {
    echo json_encode(['success' => false, 'message' => 'Cart is empty']);
    exit;
}

$orderSql = "INSERT INTO orders (user_id, total, status) VALUES (?, ?, 'pending')";
$orderStmt = mysqli_prepare($conn, $orderSql);
mysqli_stmt_bind_param($orderStmt, "id", $user_id, $total);
$ok = mysqli_stmt_execute($orderStmt);
if (!$ok) {
    echo json_encode(['success' => false, 'message' => 'Failed to create order']);
    exit;
}
$order_id = mysqli_insert_id($conn);
mysqli_stmt_close($orderStmt);

$itemSql = "INSERT INTO order_items (order_id, flower_id, quantity, price_at_order) VALUES (?, ?, ?, ?)";
$itemStmt = mysqli_prepare($conn, $itemSql);

foreach ($items as $item) {
    mysqli_stmt_bind_param($itemStmt, "iiid", $order_id, $item['flower_id'], $item['quantity'], $item['price']);
    if (!mysqli_stmt_execute($itemStmt)) {
        echo json_encode(['success' => false, 'message' => 'Failed to save order items']);
        exit;
    }
}
mysqli_stmt_close($itemStmt);

$clearSql = "DELETE FROM cart WHERE user_id = ?";
$clearStmt = mysqli_prepare($conn, $clearSql);
mysqli_stmt_bind_param($clearStmt, "i", $user_id);
mysqli_stmt_execute($clearStmt);
mysqli_stmt_close($clearStmt);

echo json_encode(['success' => true, 'order_id' => $order_id]);
