<?php
include "admin-header.php";
require "../db.php";

$flowersPath = "../front/flowers.php";
$stylePath = "../front/style.css";
$imagesDir = "../front/images/";

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

$styleCss = is_readable($stylePath) ? file_get_contents($stylePath) : false;
$flowersMarkup = is_readable($flowersPath) ? file_get_contents($flowersPath) : false;

$currentImage = "";
if ($styleCss !== false && preg_match('/\.hero\s*\{[^}]*background:\s*url\([\'"]?([^\'")]+)[\'"]?\)/s', $styleCss, $matches)) {
  $currentImage = $matches[1];
}

$currentIntroHtml = "";
if ($flowersMarkup !== false && preg_match('/<section class="overlap-block">.*?<p>(.*?)<\/p>/s', $flowersMarkup, $matches)) {
  $currentIntroHtml = $matches[1];
}

$currentIntroText = trim(
  html_entity_decode(
    strip_tags(
      str_replace(["<br>", "<br/>", "<br />"], "\n", $currentIntroHtml)
    )
  )
);

$cmsContent = $cmsReady ? fetch_cms_content($conn, "flowers") : null;
if (is_array($cmsContent)) {
  if (!empty($cmsContent["hero_image"])) {
    $currentImage = $cmsContent["hero_image"];
  }
  if (isset($cmsContent["intro_text"])) {
    $currentIntroText = $cmsContent["intro_text"];
  }
}

$errors = [];
$success = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
  $introText = isset($_POST["intro_text"]) ? trim($_POST["intro_text"]) : "";
  $newImagePath = $currentImage;

  if (!$cmsReady) {
    $errors[] = "Unable to initialize CMS storage.";
  }

  if (isset($_FILES["hero_image"]) && $_FILES["hero_image"]["error"] === UPLOAD_ERR_OK) {
    if (!is_dir($imagesDir)) {
      mkdir($imagesDir, 0755, true);
    }

    $originalName = basename($_FILES["hero_image"]["name"]);
    $extension = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));
    $safeName = preg_replace('/[^a-zA-Z0-9_\-]/', '_', pathinfo($originalName, PATHINFO_FILENAME));
    $fileName = $safeName . "_" . uniqid() . ($extension ? "." . $extension : "");
    $targetPath = $imagesDir . $fileName;

    if (!move_uploaded_file($_FILES["hero_image"]["tmp_name"], $targetPath)) {
      $errors[] = "Image upload failed.";
    } else {
      $newImagePath = "images/" . $fileName;
    }
  }

  if ($styleCss === false) {
    $errors[] = "Unable to read front/style.css.";
  }
  if ($flowersMarkup === false) {
    $errors[] = "Unable to read front/flowers.php.";
  }

  if (empty($errors)) {
    if ($newImagePath !== "" && $styleCss !== false) {
      $updatedCss = preg_replace(
        '/(\.hero\s*\{[^}]*background:\s*url\()[\'"]?[^\'")]+[\'"]?(\)[^;]*;)/s',
        '$1' . $newImagePath . '$2',
        $styleCss,
        1,
        $count
      );

      if ($count === 0) {
        $updatedCss = preg_replace(
          '/\.hero\s*\{/',
          ".hero {\n  background: url('" . $newImagePath . "') center/cover no-repeat;",
          $styleCss,
          1
        );
      }

      if (file_put_contents($stylePath, $updatedCss) === false) {
        $errors[] = "Unable to update front/style.css.";
      } else {
        $styleCss = $updatedCss;
        $currentImage = $newImagePath;
      }
    }

    $introHtml = htmlspecialchars($introText, ENT_QUOTES);
    $updatedMarkup = preg_replace(
      '/(<section class="overlap-block">.*?<p>)(.*?)(<\/p>)/s',
      '$1' . "\n" . $introHtml . "\n" . '$3',
      $flowersMarkup,
      1
    );

    if ($updatedMarkup === null) {
      $errors[] = "Failed to update front/flowers.php content.";
    } else {
      if (file_put_contents($flowersPath, $updatedMarkup) === false) {
        $errors[] = "Unable to write changes to front/flowers.php.";
      } else {
        $flowersMarkup = $updatedMarkup;
        $currentIntroText = $introText;
      }
    }
  }

  if (empty($errors)) {
    $cmsPayload = [
      "hero_image" => $currentImage,
      "intro_text" => $currentIntroText
    ];

    if (!save_cms_content($conn, "flowers", $cmsPayload)) {
      $errors[] = "Unable to save CMS content.";
    }
  }

  if (empty($errors)) {
    $success = "Flowers page updated successfully.";
  }
}

$currentImageName = $currentImage ? basename($currentImage) : "None";
$currentImagePreview = $currentImage;
if (!empty($currentImage) && !preg_match('/^(https?:\/\/|\/|\.{1,2}\/)/', $currentImage)) {
  $currentImagePreview = "../front/" . $currentImage;
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
        <h1 class="h4 m-0">Edit Flowers Page</h1>
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

      <form class="row g-3" method="POST" enctype="multipart/form-data">
        <div class="col-12">
          <label class="form-label">Current Image</label>
          <?php if (!empty($currentImage)): ?>
            <div class="mb-2">
              <img src="<?= htmlspecialchars($currentImagePreview); ?>" alt="Current hero image" style="max-width: 240px; height: auto; border: 1px solid #ddd; border-radius: 6px;">
            </div>
          <?php else: ?>
            <p class="text-muted mb-2">No image set.</p>
          <?php endif; ?>
        </div>

        <div class="col-12">
          <label class="form-label">Upload Image (hero background)</label>
          <input class="form-control flower-input" type="file" name="hero_image" accept="image/*" id="heroImageInput">
          <div class="form-text">
            Current image: <strong id="currentImageName"><?= htmlspecialchars($currentImageName); ?></strong>
          </div>
          <div class="form-text" id="selectedImageName"></div>
        </div>

        <div class="col-12">
          <label class="form-label">Welcome Text</label>
          <textarea class="form-control flower-input" name="intro_text" rows="6"><?= htmlspecialchars($currentIntroText); ?></textarea>
        </div>

        <div class="col-12 d-flex gap-2 mb-5">
          <button class="btn btn-soft" type="submit">Save</button>
          <a class="btn btn-flower" href="pages.php">Cancel</a>
        </div>
      </form>
    </section>
  </div>
</main>

<script>
  const heroInput = document.getElementById('heroImageInput');
  const selectedName = document.getElementById('selectedImageName');

  heroInput.addEventListener('change', function () {
    if (this.files && this.files.length > 0) {
      selectedName.textContent = "Selected file: " + this.files[0].name;
    } else {
      selectedName.textContent = "";
    }
  });
</script>

<?php include "footer-admin.php"; ?>
