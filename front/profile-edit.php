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
    $action = filter_input(INPUT_POST, "action", FILTER_SANITIZE_FULL_SPECIAL_CHARS) ?? "";

    if ($action === "update_email") {
        $newEmailInput = filter_input(INPUT_POST, "email", FILTER_SANITIZE_EMAIL);
        $newEmailSanitized = $newEmailInput !== null ? trim($newEmailInput) : "";
        $newEmail = filter_var($newEmailSanitized, FILTER_VALIDATE_EMAIL);

        if (!$newEmail) {
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
        $currentPasswordInput = filter_input(INPUT_POST, "current_password", FILTER_UNSAFE_RAW);
        $newPasswordInput = filter_input(INPUT_POST, "new_password", FILTER_UNSAFE_RAW);
        $confirmPasswordInput = filter_input(INPUT_POST, "confirm_password", FILTER_UNSAFE_RAW);

        $currentPassword = $currentPasswordInput !== null ? trim($currentPasswordInput) : "";
        $newPassword = $newPasswordInput !== null ? trim($newPasswordInput) : "";
        $confirmPassword = $confirmPasswordInput !== null ? trim($confirmPasswordInput) : "";

        if ($currentPassword === "" || $newPassword === "" || $confirmPassword === "") {
            $error = "Password fields cannot be empty.";
        } elseif ($newPassword !== $confirmPassword) {
            $error = "Passwords do not match.";
        } else {
            $sql = "SELECT password FROM users WHERE id = ?";
            $stmt = mysqli_prepare($conn, $sql);
            mysqli_stmt_bind_param($stmt, "i", $userId);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
            $userData = mysqli_fetch_assoc($result);

            if (!$userData || !password_verify($currentPassword, $userData["password"] ?? "")) {
                $error = "wrong password";
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
    } elseif ($action === "update_profile_image") {
        $imageInput = filter_input(INPUT_POST, "current_image", FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $image = $imageInput !== null ? trim($imageInput) : "";

        if (isset($_FILES["profile_image"]) && $_FILES["profile_image"]["error"] === UPLOAD_ERR_OK) {
            $upload_dir = "../front/images/";
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0755, true);
            }

            $original_name = basename($_FILES["profile_image"]["name"]);
            $extension = strtolower(pathinfo($original_name, PATHINFO_EXTENSION));
            $safe_name = preg_replace('/[^a-zA-Z0-9_\-]/', '_', pathinfo($original_name, PATHINFO_FILENAME));
            $file_name = $safe_name . "_" . uniqid() . ($extension ? "." . $extension : "");
            $target_path = $upload_dir . $file_name;

            if (!move_uploaded_file($_FILES["profile_image"]["tmp_name"], $target_path)) {
                $error = "Image upload failed.";
            } else {
                $image = "images/" . $file_name;
            }
        }

        if (!$error) {
            $sql = "UPDATE users SET profile_image = ? WHERE id = ?";
            $stmt = mysqli_prepare($conn, $sql);
            mysqli_stmt_bind_param($stmt, "si", $image, $userId);

            if (mysqli_stmt_execute($stmt)) {
                $success = "Profile image updated successfully.";
            } else {
                $error = "Error updating profile image: " . mysqli_error($conn);
            }
        }
    }
}

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

.profile-avatar {
    width: 120px;
    height: 120px;
    border-radius: 50%;
    object-fit: cover;
    border: 3px solid #f1f1f1;
}

.profile-avatar-placeholder {
    width: 120px;
    height: 120px;
    border-radius: 50%;
    background: #f8f9fa;
    border: 3px solid #f1f1f1;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 32px;
    color: #5fa8d3;
    font-weight: 600;
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

            <div class="d-flex flex-column align-items-center mb-4">
              <?php if (!empty($profileImage)): ?>
                <img class="profile-avatar mb-3" src="<?= htmlspecialchars($profileImage) ?>" alt="Profile image">
              <?php else: ?>
                <div class="profile-avatar-placeholder mb-3"><?= htmlspecialchars($initial) ?></div>
              <?php endif; ?>

              <form method="POST" enctype="multipart/form-data" class="w-100">
                <input type="hidden" name="action" value="update_profile_image">
                <input type="hidden" name="current_image" value="<?= htmlspecialchars($profileImage) ?>">
                <label class="form-label">Upload Profile Image</label>
                <input class="form-control" type="file" name="profile_image" accept="image/*" required>
                <div class="d-grid mt-3">
                  <button class="btn btn-signup" type="submit">Update Profile Image</button>
                </div>
              </form>
            </div>

            <div class="mb-3">
              <p><strong>Username:</strong> <?= htmlspecialchars($username) ?></p>
              <p><strong>Email:</strong> <?= htmlspecialchars($user["email"] ?? "") ?></p>
            </div>

            <hr>

            <h5 class="mb-3">Change Email</h5>
            <form method="POST" class="mb-4" id="update-email-form">
              <input type="hidden" name="action" value="update_email">
              <div class="validation-message text-danger small mb-2"></div>
              <div class="mb-3">
                <input type="email" name="email" placeholder="New email" class="form-control" required>
              </div>
              <div class="d-grid mb-3">
                <button class="btn btn-signup" type="submit">Update Email</button>
              </div>
            </form>

            <h5 class="mb-3">Change Password</h5>
            <form method="POST" id="update-password-form">
              <input type="hidden" name="action" value="update_password">
              <div class="validation-message text-danger small mb-2"></div>
              <div class="mb-3">
                <input type="password" name="current_password" placeholder="Current password" class="form-control" required>
              </div>
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
            <div class="d-grid mb-3">
              <a class="btn btn-flower btn-sm" href="profile.php">Back to Profile</a>
            </div>

          </div>
        </div>
      </div>
    </div>
  </main>

</main>

<script src="form-validation.js"></script>
<?php include "footer.php"; ?>
