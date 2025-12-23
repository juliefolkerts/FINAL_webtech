<?php
include "admin-header.php";
require "../db.php";

$infoPath = "../front/info.php";
$infoMarkup = is_readable($infoPath) ? file_get_contents($infoPath) : false;

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

$currentTitle = "";
$sections = [];

if ($infoMarkup !== false) {
  if (preg_match('/<h1 class="info-title">\s*(.*?)\s*<\/h1>/s', $infoMarkup, $titleMatch)) {
    $currentTitle = trim(strip_tags($titleMatch[1]));
  }

  $sectionPattern = '/<section class="info-section">.*?(?=<section class="info-section">|<\/body>)/s';
  if (preg_match_all($sectionPattern, $infoMarkup, $sectionMatches)) {
    foreach ($sectionMatches[0] as $block) {
      $sectionTitle = "";
      $sectionText = "";
      $sectionImage = "";

      if (preg_match('/<h2>\s*(.*?)\s*<\/h2>/s', $block, $h2Match)) {
        $sectionTitle = trim(strip_tags($h2Match[1]));
      }

      if (preg_match('/<p>\s*(.*?)(?:<\/p>|\s*<\/div>)/s', $block, $pMatch)) {
        $sectionText = trim(strip_tags($pMatch[1]));
      }

      if (preg_match('/<img\s+[^>]*src="([^"]+)"/i', $block, $imgMatch)) {
        $sectionImage = trim($imgMatch[1]);
      }

      $sections[] = [
        "title" => $sectionTitle,
        "text" => $sectionText,
        "image" => $sectionImage
      ];
    }
  }
}

$cmsContent = $cmsReady ? fetch_cms_content($conn, "info") : null;
if (is_array($cmsContent)) {
  if (isset($cmsContent["title"])) {
    $currentTitle = $cmsContent["title"];
  }
  if (!empty($cmsContent["sections"]) && is_array($cmsContent["sections"])) {
    $sections = $cmsContent["sections"];
  }
}

$errors = [];
$success = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
  $pageTitle = isset($_POST["page_title"]) ? trim($_POST["page_title"]) : "";
  $sectionTitles = isset($_POST["section_title"]) && is_array($_POST["section_title"]) ? $_POST["section_title"] : [];
  $sectionTexts = isset($_POST["section_text"]) && is_array($_POST["section_text"]) ? $_POST["section_text"] : [];
  $currentImages = isset($_POST["current_image"]) && is_array($_POST["current_image"]) ? $_POST["current_image"] : [];

  if (!$cmsReady) {
    $errors[] = "Unable to initialize CMS storage.";
  }

  if ($infoMarkup === false) {
    $errors[] = "Unable to read front/info.php.";
  }

  $uploadDir = "../front/images/";
  if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0755, true);
  }

  $updatedSections = [];
  $totalSections = max(count($sectionTitles), count($sectionTexts), count($currentImages));

  for ($i = 0; $i < $totalSections; $i++) {
    $title = isset($sectionTitles[$i]) ? trim($sectionTitles[$i]) : "";
    $text = isset($sectionTexts[$i]) ? trim($sectionTexts[$i]) : "";
    $image = isset($currentImages[$i]) ? trim($currentImages[$i]) : "";

    if (isset($_FILES["section_image"]["error"][$i]) && $_FILES["section_image"]["error"][$i] === UPLOAD_ERR_OK) {
      $originalName = basename($_FILES["section_image"]["name"][$i]);
      $extension = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));
      $safeName = preg_replace('/[^a-zA-Z0-9_\-]/', '_', pathinfo($originalName, PATHINFO_FILENAME));
      $fileName = $safeName . "_" . uniqid() . ($extension ? "." . $extension : "");
      $targetPath = $uploadDir . $fileName;

      if (!move_uploaded_file($_FILES["section_image"]["tmp_name"][$i], $targetPath)) {
        $errors[] = "Image upload failed for section " . ($i + 1) . ".";
      } else {
        $image = "images/" . $fileName;
      }
    }

    $updatedSections[] = [
      "title" => $title,
      "text" => $text,
      "image" => $image
    ];
  }

  if (empty($errors)) {
    $updatedMarkup = $infoMarkup;

    $pageTitleEsc = htmlspecialchars($pageTitle, ENT_QUOTES);
    $updatedMarkup = preg_replace(
      '/<h1 class="info-title">\s*.*?<\/h1>/s',
      '<h1 class="info-title">' . $pageTitleEsc . '</h1>',
      $updatedMarkup,
      1,
      $titleCount
    );

    if (($titleCount ?? 0) === 0) {
      $errors[] = "Failed to locate the info page title in front/info.php.";
    }

    $sectionIndex = 0;
    $updatedMarkup = preg_replace_callback(
      '/<section class="info-section">.*?(?=<section class="info-section">|<\/body>)/s',
      function ($matches) use (&$sectionIndex, $updatedSections) {
        $block = $matches[0];

        $title = $updatedSections[$sectionIndex]["title"] ?? "";
        $text = $updatedSections[$sectionIndex]["text"] ?? "";
        $image = $updatedSections[$sectionIndex]["image"] ?? "";

        $titleEsc = htmlspecialchars($title, ENT_QUOTES);
        $textEsc = htmlspecialchars($text, ENT_QUOTES);
        $imageEsc = htmlspecialchars($image, ENT_QUOTES);

        $block = preg_replace(
          '/<h2>\s*.*?<\/h2>/s',
          '<h2>' . $titleEsc . '</h2>',
          $block,
          1
        );

        $block = preg_replace_callback(
          '/<p>\s*.*?(<\/p>|\s*<\/div>)/s',
          function ($m) use ($textEsc) {
            $ending = $m[1];
            $replacement = '<p>' . $textEsc . '</p>';
            if (stripos($ending, '</div>') !== false) {
              return $replacement . $ending;
            }
            return $replacement;
          },
          $block,
          1
        );

        $block = preg_replace(
          '/(<img\s+[^>]*src=")([^"]*)(")/i',
          '$1' . $imageEsc . '$3',
          $block,
          1
        );

        $sectionIndex++;

        return $block;
      },
      $updatedMarkup,
      -1,
      $sectionCount
    );

    if (!empty($sections) && ($sectionCount ?? 0) === 0) {
      $errors[] = "Failed to locate info sections in front/info.php.";
    }

    if (empty($errors)) {
      if (file_put_contents($infoPath, $updatedMarkup) === false) {
        $errors[] = "Unable to write changes to front/info.php.";
      } else {
        $infoMarkup = $updatedMarkup;
        $currentTitle = $pageTitle;
        $sections = $updatedSections;
      }
    }
  }

  if (empty($errors)) {
    $cmsPayload = [
      "title" => $currentTitle,
      "sections" => $sections
    ];

    if (!save_cms_content($conn, "info", $cmsPayload)) {
      $errors[] = "Unable to save CMS content.";
    }
  }

  if (empty($errors)) {
    $success = "Info page updated successfully.";
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
        <h1 class="h4 m-0">Edit Info Page</h1>
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
          <label class="form-label">Page Title</label>
          <input class="form-control flower-input" type="text" name="page_title" value="<?= htmlspecialchars($currentTitle); ?>" required>
        </div>

        <?php foreach ($sections as $index => $section): ?>
          <?php
            $rawImage = $section["image"] ?? "";
            $previewImage = $rawImage;
            if (!empty($rawImage) && !preg_match('/^(https?:\/\/|\/|\.{1,2}\/)/', $rawImage)) {
              $previewImage = "../front/" . $rawImage;
            }
          ?>
          <div class="col-12">
            <h2 class="h6 mb-3">Section <?= $index + 1; ?></h2>
            <div class="row g-3">
              <div class="col-12">
                <label class="form-label">Heading</label>
                <input class="form-control flower-input" type="text" name="section_title[]" value="<?= htmlspecialchars($section["title"] ?? ""); ?>" required>
              </div>
              <div class="col-12">
                <label class="form-label">Text</label>
                <textarea class="form-control flower-input" name="section_text[]" rows="4" required><?= htmlspecialchars($section["text"] ?? ""); ?></textarea>
              </div>

              <div class="col-12">
                <label class="form-label">Current Image</label>
                <input type="hidden" name="current_image[]" value="<?= htmlspecialchars($section["image"] ?? ""); ?>">
                <?php if (!empty($section["image"])): ?>
                  <div class="mb-2">
                    <img src="<?= htmlspecialchars($previewImage); ?>" alt="Current section image" style="max-width: 200px; height: auto; border: 1px solid #ddd; border-radius: 6px;">
                  </div>
                <?php else: ?>
                  <p class="text-muted mb-2">No image set.</p>
                <?php endif; ?>
              </div>

              <div class="col-12">
                <label class="form-label">Upload New Image</label>
                <input class="form-control flower-input" type="file" name="section_image[]" accept="image/*">
              </div>
            </div>
          </div>
        <?php endforeach; ?>

        <div class="col-12 d-flex gap-2 mb-5">
          <button class="btn btn-soft" type="submit">Save</button>
          <a class="btn btn-flower" href="pages.php">Cancel</a>
        </div>
      </form>
    </section>
  </div>
</main>

<?php include "footer-admin.php"; ?>
