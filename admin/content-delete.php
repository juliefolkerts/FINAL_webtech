<?php 
require "../db.php"; 

$id = intval($_GET["id"]);

mysqli_query($conn, "DELETE FROM flowers WHERE id=$id");

header("Location: content-list.php");
exit;
?>
