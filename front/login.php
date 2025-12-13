

<?php 
session_start();
require "../db.php"; 

$bodyClass = "bg-light";
include "header.php"; 

$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = $_POST["email"];
    $pass = $_POST["password"];

    $sql = "SELECT * FROM users WHERE email = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "s", $email);
    mysqli_stmt_execute($stmt);

    $result = mysqli_stmt_get_result($stmt);
    $user = mysqli_fetch_assoc($result);

    if ($user && password_verify($pass, $user['password'])) {

    $_SESSION["user_id"] = $user["id"];
    $_SESSION["username"] = $user["username"];
    $_SESSION["role"] = $user["role"];  

    if ($user["role"] === "admin") {
        header("Location: ../admin/index.php");
    } else {
        header("Location: flowers.php");  // Normal users go to front page
    }
    exit;
}

}
?>
<style>

.form-control:focus {
    border-color: #5fa8d3 !important;     
    box-shadow: 0 0 0 0.2rem rgba(95,168,211,0.5) !important; 
    outline: none !important;
}

.btn-signup {
    background: #5fa8d3;
    color: white;
    transition: 0.2s;
}

.btn-signup:hover {
    background: #3e82ad !important;
    color: white !important;
}

.card a {
    color: #5fa8d3; 
    text-decoration: none;
}

.card a:hover {
    color: #3e82ad; 
}


</style>

<main class="container py-5" style="padding-top:100px; padding-right: 70px;">
    <div class="row justify-content-center" style="margin-top:65px">
      <div class="col-12 col-sm-8 col-md-6 col-lg-4">
        <div class="card shadow-sm border-0 rounded-4">
          <div class="card-body p-4">
            <h1 class="h4 mb-3 text-center text-pink">🌸 Flowers</h1>
            <p class="text-center small text-muted mb-4">Sign in</p>
            <?php if($error): ?>
                <p class="error-msg"><?= $error ?></p>
            <?php endif; ?>

            <form form method="POST">
              <div class="mb-3">
                <input type="email" name="email" placeholder="Email" class="form-control" required>
              </div>

              <div class="mb-3">
                <input type="password" name="password" placeholder="Password" class="form-control" required>
              </div>

              <div class="d-grid mb-3">
                <button class="btn btn-signup" style="background:#5fa8d3; color: white;" type="submit" style="background:#5fa8d3; color: white;">Sign in</button>
              </div>

              <div class="text-center">
                <p class="text-center mt-2">
                    Don't have an account? <a href="register.php">Register</a>
                </p>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </main>


</main>

<?php include "footer.php"; ?>
