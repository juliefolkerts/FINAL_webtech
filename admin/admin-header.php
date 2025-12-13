<?php
session_start();
if (!isset($_SESSION["user_id"]) || $_SESSION["role"] !== "admin") {
    header("Location: ../front/login.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <link rel="icon" type="image/png" href="images/amazon-flower.png">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Flowers</title>

  <link href="https://fonts.googleapis.com/css2?family=Varela&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="style-admin.css">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  

</head>

<body>
<header>
  <nav>
        <a href="../front/flowers.php" class="brand">🌸Flowers</a>
        
  </nav>
  
</header>
