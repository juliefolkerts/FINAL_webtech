<?php
session_start();
require "../db.php"; 

$bodyClass = "bg-light";
include "header.php"; 

$message = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $usernameInput = filter_input(INPUT_POST, "username", FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $emailInput = filter_input(INPUT_POST, "email", FILTER_SANITIZE_EMAIL);
    $passwordInput = filter_input(INPUT_POST, "password", FILTER_UNSAFE_RAW);

    $username = $usernameInput !== null ? trim($usernameInput) : "";
    $emailSanitized = $emailInput !== null ? trim($emailInput) : "";
    $email = filter_var($emailSanitized, FILTER_VALIDATE_EMAIL);
    $passwordRaw = $passwordInput !== null ? trim($passwordInput) : "";

    if ($username === "" || !$email || $passwordRaw === "") {
        $message = "Please provide valid username, email, and password.";
    } else {
        $sql = "SELECT id FROM users WHERE username = ? LIMIT 1";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "s", $username);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $existingUser = mysqli_fetch_assoc($result);
        mysqli_stmt_close($stmt);

        if ($existingUser) {
            $message = "username unavailable";
        } else {
            $sql = "SELECT id FROM users WHERE email = ? LIMIT 1";
            $stmt = mysqli_prepare($conn, $sql);
            mysqli_stmt_bind_param($stmt, "s", $email);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
            $existingEmail = mysqli_fetch_assoc($result);
            mysqli_stmt_close($stmt);

            if ($existingEmail) {
                $message = "account with this email already exists";
            } else {
                $password = password_hash($passwordRaw, PASSWORD_DEFAULT);

                $sql = "INSERT INTO users (username, email, password) VALUES (?, ?, ?)";
                $stmt = mysqli_prepare($conn, $sql);
                mysqli_stmt_bind_param($stmt, "sss", $username, $email, $password);

                if (mysqli_stmt_execute($stmt)) {
                    $message = "Account created! You can now <a href='login.php'>log in</a>.";
                } else {
                    $message = "Error: " . mysqli_error($conn);
                }
            }
        }
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




</style>
<main class="container py-5" style="padding-top:100px; padding-right: 70px;">
    <div class="row justify-content-center" style="margin-top:60px">
      <div class="col-12 col-sm-8 col-md-6 col-lg-4">
        <div class="card shadow-sm border-0 rounded-4">
          <div class="card-body p-4">
            <h1 class="h4 mb-3 text-center text-pink">🌸 Flowers</h1>
            <p class="text-center small text-muted mb-4">Sign up</p>
            <?php if($message): ?>
              <p style="color:green;"><?= $message ?></p>
            <?php endif; ?>

            <form form method="POST" id="register-form">
              <div class="validation-message text-danger small mb-2"></div>
              <div class="mb-3">
                
                <input type="text" name="username" placeholder="username" class="form-control" required>
              </div>

              <div class="mb-3">
               
                <input type="email" name="email" placeholder="email" class="form-control" required>
              </div>

              

              <div class="mb-3">
              
              <input type="password" name="password" placeholder="●●●●●●●●" class="form-control" required>
              </div>

              <div class="d-grid mb-3">
                <button class="btn btn-signup" type="submit" style="background:#5fa8d3; color: white;">Sign up</button>
              </div>

        
            </form>
          </div>
        </div>
      </div>
    </div>
  </main>


</main>


<script src="form-validation.js"></script>
<?php include "footer.php"; ?>
