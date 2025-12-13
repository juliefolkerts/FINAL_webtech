<?php
$host = "localhost";
$user = "root";
$pass = "";
$dbname = "endterm";

$conn = mysqli_connect(
    "127.0.0.1",
    "root",
    "",
    "endterm"
);


if (!$conn) {
    die("DB connection error: " . mysqli_connect_error());
}
?>
