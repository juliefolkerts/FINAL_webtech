<?php
session_start();
require "../db.php";

$q = "%" . ($_GET["q"] ?? "") . "%";

// prepare query
$sql = "SELECT * FROM flowers WHERE name LIKE ? OR description LIKE ? OR IFNULL(keywords,'') LIKE ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "sss", $q, $q, $q);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

$user_id = $_SESSION['user_id'] ?? null;

$data = [];
while ($row = mysqli_fetch_assoc($result)) {
  
    $row['image'] = $row['image']; 

    if ($user_id) {
        $fstmt = mysqli_prepare($conn, "SELECT 1 FROM favourites WHERE user_id = ? AND flower_id = ?");
        mysqli_stmt_bind_param($fstmt, "ii", $user_id, $row['id']);
        mysqli_stmt_execute($fstmt);
        mysqli_stmt_store_result($fstmt);
        $is_fav = (mysqli_stmt_num_rows($fstmt) > 0) ? 1 : 0;
        mysqli_stmt_close($fstmt);
    } else {
        $is_fav = 0;
    }
    $row['is_favourite'] = $is_fav;

    $data[] = $row;
}

header('Content-Type: application/json');
echo json_encode($data);
