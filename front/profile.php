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

// Fetch current user data
$sql = "SELECT username, email, profile_image FROM users WHERE id = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $userId);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$user = mysqli_fetch_assoc($result);
$profileImage = $user["profile_image"] ?? "";
$username = $user["username"] ?? "";
$initial = $username !== "" ? strtoupper(substr($username, 0, 1)) : "?";
?>

<style>

.form-control:focus {
    border-color: #5fa8d3 !important;     
    box-shadow: 0 0 0 0.2rem rgba(95,168,211,0.5) !important; 
    outline: none !important;
}

.btn-signup {
    background: #5fa8d3;
    color: white !important;
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

.profile-avatar {
    width: 160px;
    height: 160px;
    border-radius: 50%;
    object-fit: cover;
    border: 3px solid #f1f1f1;
}

.profile-avatar-placeholder {
    width: 160px;
    height: 160px;
    border-radius: 50%;
    background: #f8f9fa;
    border: 3px solid #f1f1f1;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 40px;
    color: #5fa8d3;
    font-weight: 600;
}

.profile-details {
    font-size: 1.1rem;
}

</style>

<main class="container py-5" style="padding-top:100px;">
    <div class="row justify-content-center" style="margin-top:65px">
      <div class="col-12 col-sm-10 col-md-8 col-lg-6">
        <div class="card shadow-sm border-0 rounded-4">
          <div class="card-body p-4">
            <h1 class="h4 mb-3 text-center text-pink" style="padding-right: 30px;">🌸 My Profile</h1>
            <p class="text-center small text-muted mb-4"></p>

            <div class="d-flex flex-column align-items-center mb-4">
              <?php if (!empty($profileImage)): ?>
                <img class="profile-avatar mb-3" src="<?= htmlspecialchars($profileImage) ?>" alt="Profile image">
              <?php else: ?>
                <div class="profile-avatar-placeholder mb-3"><?= htmlspecialchars($initial) ?></div>
              <?php endif; ?>
            </div>

            <div class="mb-4 text-center profile-details">
              <p><strong>Username:</strong> <?= htmlspecialchars($username) ?></p>
              <p><strong>Email:</strong> <?= htmlspecialchars($user["email"] ?? "") ?></p>
            </div>

            <div class="d-flex gap-2">
              <a class="btn btn-signup btn-sm w-100 d-flex align-items-center justify-content-center" href="orders.php">My Orders</a>
              <a class="btn btn-signup btn-sm w-100 d-flex align-items-center justify-content-center" href="profile-edit.php">Change Account Details</a>

              <a class="btn btn-flower btn-sm w-100 d-flex align-items-center justify-content-center" href="logout.php">logout</a>
            </div>

          </div>
        </div>
      </div>
    </div>
  </main>

</main>

<?php include "footer.php"; ?>
