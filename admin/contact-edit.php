<?php
include "admin-header.php";
require "../db.php";

$contactPath = "../front/contact.php";
$contactMarkup = is_readable($contactPath) ? file_get_contents($contactPath) : false;

function ensure_cms_table($conn) {
  $sql = "CREATE TABLE IF NOT EXISTS cms_pages (
    page_key VARCHAR(50) PRIMARY KEY,
    content LONGTEXT NOT NULL,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
  )";
  return mysqli_query($conn, $sql);
}

function fetch_cms_content($conn, $pageKey) {
  $stmt = mysqli_prepare($conn, "SELECT content FROM cms_pages WHERE page_key = ?");
  if (!$stmt) {
    return null;
  }
  mysqli_stmt_bind_param($stmt, "s", $pageKey);
  mysqli_stmt_execute($stmt);
  mysqli_stmt_bind_result($stmt, $content);
  $data = null;
  if (mysqli_stmt_fetch($stmt)) {
    $decoded = json_decode($content, true);
    if (is_array($decoded)) {
      $data = $decoded;
    }
  }
  mysqli_stmt_close($stmt);
  return $data;
}

function save_cms_content($conn, $pageKey, $payload) {
  $json = json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
  if ($json === false) {
    return false;
  }
  $stmt = mysqli_prepare(
    $conn,
    "INSERT INTO cms_pages (page_key, content) VALUES (?, ?)
     ON DUPLICATE KEY UPDATE content = VALUES(content)"
  );
  if (!$stmt) {
    return false;
  }
  mysqli_stmt_bind_param($stmt, "ss", $pageKey, $json);
  $ok = mysqli_stmt_execute($stmt);
  mysqli_stmt_close($stmt);
  return $ok;
}

$cmsReady = ensure_cms_table($conn);

$currentEmail = "";
$currentPhone = "";

if ($contactMarkup !== false) {
  $contactMarkup = preg_replace(
    '/(mailto:[^"]+)"\s*([a-zA-Z0-9._%+\-@]+)<\/a>/',
    '$1">$2</a>',
    $contactMarkup
  );
  $contactMarkup = preg_replace(
    '/(tel:[^"]+)"\s*([^<]+)<\/a>/',
    '$1">$2</a>',
    $contactMarkup
  );

  if (preg_match('/Email:\s*<a[^>]*href="mailto:([^"]+)"[^>]*>([^<]*)<\/a>/i', $contactMarkup, $matches)) {
    $currentEmail = trim($matches[2]) !== "" ? $matches[2] : $matches[1];
  }
  if (preg_match('/Phone:\s*<a[^>]*href="tel:([^"]+)"[^>]*>([^<]*)<\/a>/i', $contactMarkup, $matches)) {
    $currentPhone = trim($matches[2]) !== "" ? $matches[2] : $matches[1];
  }
}

$cmsContent = $cmsReady ? fetch_cms_content($conn, "contact") : null;
if (is_array($cmsContent)) {
  if (isset($cmsContent["email"])) {
    $currentEmail = $cmsContent["email"];
  }
  if (isset($cmsContent["phone"])) {
    $currentPhone = $cmsContent["phone"];
  }
}

$errors = [];
$success = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
  $email = isset($_POST["email"]) ? trim($_POST["email"]) : "";
  $phone = isset($_POST["phone"]) ? trim($_POST["phone"]) : "";

  if (!$cmsReady) {
    $errors[] = "Unable to initialize CMS storage.";
  }

  if ($contactMarkup === false) {
    $errors[] = "Unable to read front/contact.php.";
  }

  if (empty($errors)) {
    $emailEsc = htmlspecialchars($email, ENT_QUOTES);
    $phoneEsc = htmlspecialchars($phone, ENT_QUOTES);

    $updatedMarkup = $contactMarkup;

    $updatedMarkup = preg_replace_callback(
      '/(Email:\s*<a[^>]*href="mailto:)([^"]+)(")([^>]*>)([^<]*)(<\/a>)/i',
      function ($m) use ($emailEsc) {
        return $m[1] . $emailEsc . $m[3] . $m[4] . $emailEsc . $m[6];
      },
      $updatedMarkup,
      1,
      $emailCount
    );

    $updatedMarkup = preg_replace_callback(
      '/(Phone:\s*<a[^>]*href="tel:)([^"]+)(")([^>]*>)([^<]*)(<\/a>)/i',
      function ($m) use ($phoneEsc) {
        return $m[1] . $phoneEsc . $m[3] . $m[4] . $phoneEsc . $m[6];
      },
      $updatedMarkup,
      1,
      $phoneCount
    );

    if (($emailCount ?? 0) === 0 || ($phoneCount ?? 0) === 0) {
      $errors[] = "Failed to locate email/phone markup in front/contact.php.";
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
    $cmsPayload = [
      "email" => $currentEmail,
      "phone" => $currentPhone
    ];

    if (!save_cms_content($conn, "contact", $cmsPayload)) {
      $errors[] = "Unable to save CMS content.";
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
