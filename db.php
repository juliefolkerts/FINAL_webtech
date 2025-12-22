<?php
$host = "127.0.0.1";
$user = "root";
$pass = "";
$dbname = "endterm";
$port = 3306;

$conn = mysqli_connect($host, $user, $pass, $dbname, $port);

if (!$conn) {
    die("DB connection error: " . mysqli_connect_error());
}

