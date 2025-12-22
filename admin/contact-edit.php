<?php
include "admin-header.php";
require "../db.php";

$contactPath = "../front/contact.php";
$contactMarkup = is_readable($contactPath) ? file_get_contents($contactPath) : false;

$currentEmail = "";
$currentPhone = "";

if ($contactMarkup !== false) {
  if (preg_match('/Email:\s*<a[^>]*href="mailto:([^"]+)"[^>]*>([^<]*)<\/a>/i', $contactMarkup, $matches)) {
    $currentEmail = $matches[2] !== "" ? $matches[2] : $matches[1];
  }
  if (preg_match('/Phone:\s*<a[^>]*href="tel:([^"]+)"[^>]*>([^<]*)<\/a>/i', $contactMarkup, $matches)) {
    $currentPhone = $matches[2] !== "" ? $matches[2] : $matches[1];
  }
}

$errors = [];
$success = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
  $email = isset($_POST["email"]) ? trim($_POST["email"]) : "";
  $phone = isset($_POST["phone"]) ? trim($_POST["phone"]) : "";

  if ($contactMarkup === false) {
    $errors[] = "Unable to read front/contact.php.";
  }

  if (empty($errors)) {
    $updatedMarkup = $contactMarkup;

    $updatedMarkup = preg_replace(
      '/(Email:\s*<a[^>]*href="mailto:)[^"]+(")([^>]*>)[^<]*(<\/a>)/i',
      '$1' . htmlspecialchars($email, ENT_QUOTES) . '$2$3' . htmlspecialchars($email, ENT_QUOTES) . '$4',
      $updatedMarkup,
      1
    );

    $updatedMarkup = preg_replace(
      '/(Phone:\s*<a[^>]*href="tel:)[^"]+(")([^>]*>)[^<]*(<\/a>)/i',
      '$1' . htmlspecialchars($phone, ENT_QUOTES) . '$2$3' . htmlspecialchars($phone, ENT_QUOTES) . '$4',
      $updatedMarkup,
      1
    );

    if ($updatedMarkup === null) {
      $errors[] = "Failed to update contact information.";
    } else {
      if (file_put_contents($contactPath, $updatedMarkup) === false) {
        $errors[] = "Unable to write changes to front/contact.php.";
      } else {
        $contactMarkup = $updatedMarkup;
        $currentEmail = $email;
        $currentPhone = $phone;
      }
    }
  }

  if (empty($errors)) {
    $success = "Contact page updated successfully.";
  }
}
?>
<style>
.btn-soft {
  background: white;
  border: 1px solid #5fa8d3;
  color: #5fa8d3;
  font-weight: 500;
  transition: all 0.3s ease;
}
.btn-soft:hover {
  background: #5fa8d3;
  color: white;
}

.btn-flower {
  background: white;
  border: 1px solid #313131;
  color: #313131;
  font-weight: 500;
  transition: all 0.3s ease;
}
.btn-flower:hover {
  background: #fd5a88;
  color: rgb(252, 251, 252);
}
</style>

<main class="container-fluid">
  <div class="row">
    <?php include "sidebar-admin.php"; ?>

    <section class="col-12 col-md-9 col-lg-10 p-4">
      <header class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="h4 m-0">Edit Contact Page</h1>
        <a class="btn btn-outline-dark" href="pages.php">All Pages</a>
      </header>

      <?php if (!empty($errors)): ?>
        <div class="alert alert-danger">
          <ul class="mb-0">
            <?php foreach ($errors as $error): ?>
              <li><?= htmlspecialchars($error); ?></li>
            <?php endforeach; ?>
          </ul>
        </div>
      <?php elseif ($success): ?>
        <div class="alert alert-success"><?= htmlspecialchars($success); ?></div>
      <?php endif; ?>

      <form class="row g-3" method="POST">
        <div class="col-12">
          <label class="form-label">Email</label>
          <input class="form-control flower-input" type="email" name="email" value="<?= htmlspecialchars($currentEmail); ?>" required>
        </div>

        <div class="col-12">
          <label class="form-label">Phone</label>
          <input class="form-control flower-input" type="text" name="phone" value="<?= htmlspecialchars($currentPhone); ?>" required>
        </div>

        <div class="col-12 d-flex gap-2 mb-5">
          <button class="btn btn-soft" type="submit">Save</button>
          <a class="btn btn-flower" href="pages.php">Cancel</a>
        </div>
      </form>
    </section>
  </div>
</main>

<?php include "footer-admin.php"; ?>
