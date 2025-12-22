<?php 
session_start();
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
            <li><a href="info.php">Information</a></li>
            <li><a href="search.php">Assortment</a></li>
  
      

            <?php if(isset($_SESSION["user_id"])): ?>
                <li><a href="favourites.php">Favourites</a></li>
                <li><a href="profile.php">My Profile</a></li>
                <li><a href="contact.php">Contact</a></li>
                <li><a href="logout.php">Logout</a></li>
                <?php if($_SESSION["role"] === "admin"): ?>
                    <li><a href="../admin/index.php" class="admin-btn">Admin Panel</a></li>
                <?php endif; ?>
                
            <?php else: ?>
                <li><a href="contact.php">Contact</a></li>
                <li><a href="login.php">Login</a></li>
                <li><a href="register.php">Sign Up</a></li>
            <?php endif; ?>
        </ul>
    </nav>

    <button id="openNav" class="hamburger" onclick="w3_open()">☰</button>
</header>



<body class="home-page">
  <div id="mySidebar" class="sidebar">
    <a href="javascript:void(0)" class="closebtn" onclick="w3_close()">×</a>
    <a href="flowers.php">🌸Flowers</a>
    <a href="info.php">Information</a>
    <a href="search.php">Assortment</a>


                <?php if(isset($_SESSION["user_id"])): ?>
                    <a href="favourites.php">Favourites</a>
                    <a href="profile.php">My Profile</a>
                    <a href="contact.php">Contact</a>
                    <a href="logout.php">Logout</a>
                    <?php if($_SESSION["role"] === "admin"): ?>
                        <a href="../admin/index.php" class="admin-btn">Admin Panel</a>
                    <?php endif; ?>
                    
                <?php else: ?>
                    <a href="contact.php">Contact</a>
                    <a href="login.php">Login</a>
                    <a href="register.php">Sign Up</a>
                <?php endif; ?>
    </div>

  <main id="main">

    <header class="hero">
      <div class="hero-content">
        <h1 style="font-size:20vw;">Flowers</h1>
      </div>
    </header>


    <section class="overlap-block">
      <div class="overlap-content">
        <h1>Welcome to <strong>🌸Flowers</strong></h1>
<p>
Discover the language of flowers — where every color and petal carries meaning. 
From vibrant reds that express love to soft whites symbolizing purity and peace, 
our blossoms help you say what words sometimes cannot.
Explore our gallery, learn about the emotions behind each flower, 
 and find the perfect bouquet to celebrate life’s most meaningful moments.
</p>

      </div>
    </section>


    <?php include "footer.php"; ?>

  </main>

</body>



