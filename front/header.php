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
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@24,700,0,0">
  
  

</head>

<header>
    <nav>
        <a href="flowers.php" class="brand">🌸Flowers</a>
        <ul>
            <li><a href="info.php"><span class="material-symbols-rounded">info</span></a></li>
            <li><a href="search.php"><span class="material-symbols-rounded">search</span></a></li>
  
      

            <?php if(isset($_SESSION["user_id"])): ?>
                <li><a href="favourites.php"><span class="material-symbols-rounded">favorite</span></a></li>
                <li><a href="cart.php"><span class="material-symbols-rounded">shopping_cart</span></a></li>
                <li><a href="profile.php"><span class="material-symbols-rounded">person</span></a></li>
                <li><a href="contact.php"><span class="material-symbols-rounded">mail</span></a></li>
                
                <?php if($_SESSION["role"] === "admin"): ?>
                    <li><a href="../admin/index.php" class="admin-btn"><span class="material-symbols-rounded">admin_panel_settings</span></a></li>
                <?php endif; ?>
                
            <?php else: ?>
                <li><a href="contact.php"><span class="material-symbols-rounded">mail</span></a></li>
                <li><a href="login.php"><span class="material-symbols-rounded">person</span></a></li>
            
                
            <?php endif; ?>
        </ul>
    </nav>

    <button id="openNav" class="hamburger" onclick="w3_open()">☰</button>
</header>

<body class="<?php echo $bodyClass ?? ''; ?>" >
    <div id="mySidebar" class="sidebar">
        <a href="javascript:void(0)" class="closebtn" onclick="w3_close()">×</a>
        <a href="flowers.php"><strong>🌸Flowers</strong></a>
        <a href="info.php" style="display:flex; align-items:center; gap:8px;"><span class="material-symbols-rounded">info</span> <strong>Information</strong></a>
        <a href="search.php" style="display:flex; align-items:center; gap:8px;"><span class="material-symbols-rounded">search</span> <strong>Assortment</strong></a>


                    <?php if(isset($_SESSION["user_id"])): ?>
                        <a href="favourites.php" style="display:flex; align-items:center; gap:8px;"><span class="material-symbols-rounded">favorite</span> <strong>Favourites</strong></a>
                        <a href="cart.php" style="display:flex; align-items:center; gap:8px;"><span class="material-symbols-rounded">shopping_cart</span> <strong>Cart</strong></a>
                        <a href="profile.php" style="display:flex; align-items:center; gap:8px;"><span class="material-symbols-rounded">person</span> <strong>My Profile</strong></a>
                        <a href="contact.php" style="display:flex; align-items:center; gap:8px;"><span class="material-symbols-rounded">mail</span> <strong>Contact</strong></a>
                        
                        <?php if($_SESSION["role"] === "admin"): ?>
                            <a href="../admin/index.php" class="admin-btn" style="display:flex; align-items:center; gap:8px;"><span class="material-symbols-rounded">admin_panel_settings</span> <strong>Admin Panel</strong></a>
                        <?php endif; ?>
                        
                    <?php else: ?>
                        <a href="contact.php" style="display:flex; align-items:center; gap:8px;"><span class="material-symbols-rounded">mail</span> <strong>Contact</strong></a>
                        <a href="login.php" style="display:flex; align-items:center; gap:8px;"><span class="material-symbols-rounded">person</span> <strong>Login</strong></a>
                    <?php endif; ?>
  </div>

<div id="main">
