<?php
session_start();
require "../db.php";

if (!isset($_SESSION["user_id"])) {
    die("Login required.");
}

$user_id = $_SESSION["user_id"];
$flower_id = intval($_GET["id"]);

$check = mysqli_query($conn, "SELECT * FROM favourites WHERE user_id=$user_id AND flower_id=$flower_id");

if (mysqli_num_rows($check) > 0) {
    // remove
    mysqli_query($conn, "DELETE FROM favourites WHERE user_id=$user_id AND flower_id=$flower_id");
} else {
    // add
    mysqli_query($conn, "INSERT INTO favourites (user_id, flower_id) VALUES ($user_id, $flower_id)");
}

header("Location: favourites.php");
exit;
