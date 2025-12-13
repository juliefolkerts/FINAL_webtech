<?php 
include "admin-header.php"; 
require "../db.php"; 

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name = isset($_POST['name']) ? trim($_POST['name']) : '';
    $sku = isset($_POST['sku']) ? trim($_POST['sku']) : '';
    $category_id = isset($_POST['category_id']) ? (int) $_POST['category_id'] : 0;
    $price = isset($_POST['price']) && $_POST['price'] !== '' ? (float) $_POST['price'] : 0.00;
    $stock = isset($_POST['stock']) && $_POST['stock'] !== '' ? (int) $_POST['stock'] : 0;
    $color = isset($_POST['color']) ? trim($_POST['color']) : '';
    $description = isset($_POST['description']) ? trim($_POST['description']) : '';
    $image = isset($_POST['image']) ? trim($_POST['image']) : '';
    $visible = isset($_POST['visible']) ? 1 : 0;

    $sql = "INSERT INTO flowers (name, sku, category_id, price, stock, color, description, image, visible)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";

    $stmt = mysqli_prepare($conn, $sql);

    if ($stmt === false) {
        die("Prepare failed: " . mysqli_error($conn));
    }

    $types = "ssidisssi";
    mysqli_stmt_bind_param(
        $stmt,
        $types,
        $name,
        $sku,
        $category_id,
        $price,
        $stock,
        $color,
        $description,
        $image,
        $visible
    );

    $exec = mysqli_stmt_execute($stmt);

    if ($exec === false) {
        die("Execute failed: " . mysqli_stmt_error($stmt));
    }

    mysqli_stmt_close($stmt);

    header("Location: content-list.php");
    exit;
}

$categories = mysqli_query($conn, "SELECT * FROM categories");
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
        <h1 class="h4 m-0">Add New Flower</h1>
        <a class="btn btn-outline-dark" href="content-list.php">All Flowers</a>
      </header>

      <form class="row g-3" method="POST">

        <div class="col-md-6">
          <label class="form-label">Flower Name</label>
          <input class="form-control flower-input" name="name" required>
        </div>

        <div class="col-md-6">
          <label class="form-label">Flower Family</label>
          <select class="form-select flower-select" name="category_id" required>
            <option value="">Select family</option>
            <?php while ($cat = mysqli_fetch_assoc($categories)): ?>
              <option value="<?= $cat['id'] ?>"><?= htmlspecialchars($cat['name']) ?></option>
            <?php endwhile; ?>
          </select>
        </div>

        <div class="col-12">
          <label class="form-label">Description</label>
          <textarea class="form-control flower-input" name="description" rows="6"></textarea>
        </div>

        <div class="col-md-4">
          <label class="form-label">SKU</label>
          <input class="form-control flower-input" name="sku">
        </div>

        <div class="col-md-4">
          <label class="form-label">Color</label>
          <select class="form-select flower-select" name="color">
            <option>Red</option>
            <option>Pink</option>
            <option>Yellow</option>
            <option>White</option>
            <option>Purple</option>
          </select>
        </div>

        <div class="col-md-2">
          <label class="form-label">Price</label>
          <input type="number" step="0.01" class="form-control flower-input" name="price">
        </div>

        <div class="col-md-2">
          <label class="form-label">Stock</label>
          <input type="number" class="form-control flower-input" name="stock">
        </div>

        <div class="col-12">
          <div class="form-check form-switch my-3">
            <input class="form-check-input flower-switch" type="checkbox" name="visible" checked>
            <label class="form-check-label">Visible</label>
          </div>
        </div>

        <div class="col-12">
          <label class="form-label">Image URL</label>
          <input class="form-control flower-input" name="image">
        </div>

        <div class="col-12 d-flex gap-2 mb-5">
          <button class="btn btn-soft" type="submit">Save</button>
          <a class="btn btn-flower" href="content-list.php">Cancel</a>
        </div>

      </form>
    </section>
  </div>
</main>

<?php include "footer-admin.php"; ?>
