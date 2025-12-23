<?php
session_start();
require "../db.php";

$bodyClass = "bg-light";
include "header.php";

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit;
}

$userId = $_SESSION["user_id"];
$success = "";
$error = "";

// Handle form submissions
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $action = $_POST["action"] ?? "";

    if ($action === "update_email") {
        $newEmail = trim($_POST["email"] ?? "");

        if (!filter_var($newEmail, FILTER_VALIDATE_EMAIL)) {
            $error = "Please enter a valid email address.";
        } else {
            $sql = "UPDATE users SET email = ? WHERE id = ?";
            $stmt = mysqli_prepare($conn, $sql);
            mysqli_stmt_bind_param($stmt, "si", $newEmail, $userId);

            if (mysqli_stmt_execute($stmt)) {
                $success = "Email updated successfully.";
            } else {
                $error = "Error updating email: " . mysqli_error($conn);
            }
        }
    } elseif ($action === "update_password") {
        $newPassword = $_POST["new_password"] ?? "";
        $confirmPassword = $_POST["confirm_password"] ?? "";

        if ($newPassword === "" || $confirmPassword === "") {
            $error = "Password fields cannot be empty.";
        } elseif ($newPassword !== $confirmPassword) {
            $error = "Passwords do not match.";
        } else {
            $hashed = password_hash($newPassword, PASSWORD_DEFAULT);

            $sql = "UPDATE users SET password = ? WHERE id = ?";
            $stmt = mysqli_prepare($conn, $sql);
            mysqli_stmt_bind_param($stmt, "si", $hashed, $userId);

            if (mysqli_stmt_execute($stmt)) {
                $success = "Password updated successfully.";
            } else {
                $error = "Error updating password: " . mysqli_error($conn);
            }
        }
    }
}

// Fetch current user data
$sql = "SELECT username, email FROM users WHERE id = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $userId);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$user = mysqli_fetch_assoc($result);
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

.btn-flower {
    background: white;
    border: 1px solid #313131;
    color: #313131 !important; 
    font-weight: 500;
    transition: all 0.3s ease;
}
.btn-flower:hover {
    background: #fd5a88;
    color: rgb(252, 251, 252) !important;
}

.card a {
    color: #5fa8d3; 
    text-decoration: none;
}

.card a:hover {
    color: #3e82ad; 
}

</style>

<main class="container py-5" style="padding-top:100px;">
    <div class="row justify-content-center" style="margin-top:65px">
      <div class="col-12 col-sm-10 col-md-8 col-lg-6">
        <div class="card shadow-sm border-0 rounded-4">
          <div class="card-body p-4">
            <h1 class="h4 mb-3 text-center text-pink" style="padding-right: 30px;">🌸 Flowers</h1>
            <p class="text-center small text-muted mb-4">My Profile</p>

            <?php if ($success): ?>
                <div class="alert alert-success"><?= $success ?></div>
            <?php endif; ?>

            <?php if ($error): ?>
                <div class="alert alert-danger"><?= $error ?></div>
            <?php endif; ?>

            <div class="mb-3">
              <p><strong>Username:</strong> <?= htmlspecialchars($user["username"] ?? "") ?></p>
              <p><strong>Email:</strong> <?= htmlspecialchars($user["email"] ?? "") ?></p>
            </div>

            <hr>

            <h5 class="mb-3">Change Email</h5>
            <form method="POST" class="mb-4">
              <input type="hidden" name="action" value="update_email">
              <div class="mb-3">
                <input type="email" name="email" placeholder="New email" class="form-control" required>
              </div>
              <div class="d-grid mb-3">
                <button class="btn btn-signup" type="submit">Update Email</button>
              </div>
            </form>

            <h5 class="mb-3">Change Password</h5>
            <form method="POST">
              <input type="hidden" name="action" value="update_password">
              <div class="mb-3">
                <input type="password" name="new_password" placeholder="New password" class="form-control" required>
              </div>
              <div class="mb-3">
                <input type="password" name="confirm_password" placeholder="Confirm new password" class="form-control" required>
              </div>
              <div class="d-grid mb-3">
                <button class="btn btn-signup" type="submit">Update Password</button>
              </div>
            </form>

            <div class="d-grid mt-4">
              <a class="btn btn-flower" href="logout.php">logout</a>
            </div>

          </div>
        </div>
      </div>
    </div>
  </main>

</main>

<?php include "footer.php"; ?>
