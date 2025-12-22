<?php
session_start();
require "../db.php";

if (!isset($_SESSION["user_id"])) {
    die("Login required.");
}

$user_id = $_SESSION["user_id"];
$flower_id = intval($_GET["id"]);

$stmt = mysqli_prepare($conn, "SELECT * FROM favourites WHERE user_id = ? AND flower_id = ?");
mysqli_stmt_bind_param($stmt, "ii", $user_id, $flower_id);
mysqli_stmt_execute($stmt);
$check = mysqli_stmt_get_result($stmt);
mysqli_stmt_close($stmt);

if (mysqli_num_rows($check) > 0) {
    // remove
    $stmt = mysqli_prepare($conn, "DELETE FROM favourites WHERE user_id = ? AND flower_id = ?");
    mysqli_stmt_bind_param($stmt, "ii", $user_id, $flower_id);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
} else {
    // add
    mysqli_query($conn, "INSERT INTO favourites (user_id, flower_id) VALUES ($user_id, $flower_id)");
}

header("Location: favourites.php");
exit;
