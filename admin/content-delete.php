<?php 
require "../db.php"; 

$id = intval($_GET["id"]);

$stmt = mysqli_prepare($conn, "DELETE FROM flowers WHERE id = ?");
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
mysqli_stmt_close($stmt);

header("Location: content-list.php");
exit;
?>
