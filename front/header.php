<?php

?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <link rel="icon" type="image/png" href="images/amazon-flower.png">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Flowers</title>

  <link href="https://fonts.googleapis.com/css2?family=Varela&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="style.css">
  
  

</head>

<header>
    <nav>
        <a href="flowers.php" class="brand">🌸Flowers</a>
        <ul>
            <li><a href="gallery.php">Gallery</a></li>
            <li><a href="info.php">Information</a></li>
            <li><a href="search.php">Search</a></li>
  
      

            <?php if(isset($_SESSION["user_id"])): ?>
                <?php if($_SESSION["role"] === "admin"): ?>
                    <li><a href="../admin/index.php" class="admin-btn">Admin Panel</a></li>
                <?php endif; ?>
                
                <li><a href="favourites.php">My Favourites</a></li>
                <li><a href="logout.php">Logout</a></li>
                
            <?php else: ?>
                <li><a href="login.php">Login</a></li>
                <li><a href="register.php">Sign Up</a></li>
            <?php endif; ?>
        </ul>
    </nav>

    <button id="openNav" class="hamburger" onclick="w3_open()">☰</button>
</header>

<body class="<?php echo $bodyClass ?? ''; ?>" >
<div id="mySidebar" class="sidebar">
  <a href="javascript:void(0)" class="closebtn" onclick="w3_close()">×</a>
  <a href="flowers.php">🌸Flowers</a>
  <a href="gallery.php">Gallery</a>
  <a href="info.php">Information</a>
  <a href="search.php">Search</a>


            <?php if(isset($_SESSION["user_id"])): ?>
                <a href="favourites.php">My Favourites</a>
                <a href="../admin/index.php" class="admin-btn">Admin Panel</a></li>
                <a href="logout.php">Logout</a>
            <?php else: ?>
                <a href="login.php">Login</a>
                <a href="register.php">Sign Up</a>
            <?php endif; ?>
</div>

<div id="main">
