<?php
//n
ob_start();

//error check
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
require "../db.php";

$q = "%" . ($_GET["q"] ?? "") . "%";

//fixed query
$sql = "SELECT * FROM flowers WHERE visible = 1 AND (name LIKE ? OR description LIKE ? OR IFNULL(keywords,'') LIKE ?)";
$stmt = mysqli_prepare($conn, $sql);
//error check:
if (!$stmt) {
    die("Prepare failed: " . mysqli_error($conn));
}


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
//n
while (ob_get_level() > 0) {
    ob_end_clean();
}
header('Content-Type: application/json; charset=utf-8');
echo json_encode($data);

