<?php include "admin-header.php"; ?>
<?php require "../db.php"; ?>
<style>
  .btn-edit,
  .btn-delete {
    display: inline-block;
    padding: 6px 14px;
    font-size: 0.85rem;
    font-weight: ;
    border-radius: 6px;
    text-decoration: none;
    transition: 0.25s;
  }

  .btn-edit {
    background: white;
  border: 1px solid #5fa8d3;
  color: #5fa8d3 !important;
  font-weight: 400;
  transition: all 0.3s ease;
  }

  .btn-edit:hover {
    background: #5fa8d3;
    color: white !important;
  }

  .btn-delete {
    background: white;
  border: 1px solid #313131;
  color: #313131;
  font-weight: 400;
  transition: all 0.3s ease;
  }

  .btn-delete:hover {
    background: #fd5a88;
  color: rgb(252, 251, 252);
  }

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
</style>

<body>

  <main class="container-fluid">
    <div class="row">
      <!-- SIDEBAR -->
      <?php include "sidebar-admin.php"; ?>

      <!-- MAIN CONTENT -->
      <section class="col-12 col-md-9 col-lg-10 p-4">
        <header class="d-flex justify-content-between align-items-center mb-3">
          <h1 class="h4 m-0">Pages</h1>
          <a class="btn btn-outline-dark" href="index.php">Back to Dashboard</a>
        </header>

        <div class="d-flex justify-content-between align-items-center mb-3">
          <h2 class="h6 m-0">Site Pages</h2>
          <a class="btn btn-soft" href="#">Add New Page</a>
        </div>

        <?php
          $pages = [
            ["title" => "Home", "slug" => "flowers.php", "status" => "Published"],
            ["title" => "Information", "slug" => "info.php", "status" => "Published"],
            ["title" => "Search", "slug" => "search.php", "status" => "Published"],
            ["title" => "Favourites", "slug" => "favourites.php", "status" => "Published"],
            ["title" => "Cart", "slug" => "cart.php", "status" => "Published"],
            ["title" => "Profile", "slug" => "profile.php", "status" => "Published"],
            ["title" => "Contact", "slug" => "contact.php", "status" => "Published"],
          ];
        ?>

        <div class="table-responsive">
          <table class="table table-striped table-hover align-middle">
            <thead>
              <tr>
                <th>Title</th>
                <th>Slug</th>
                <th>Status</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($pages as $page): ?>
                <?php
                  $statusClass = ($page["status"] === "Published") ? "bg-success" : "bg-blocked";
                ?>
                <tr>
                  <td><?= htmlspecialchars($page["title"]); ?></td>
                  <td><?= htmlspecialchars("/" . $page["slug"]); ?></td>
                  <td><span class="badge <?= $statusClass; ?>"><?= htmlspecialchars($page["status"]); ?></span></td>
                  <td>
                    <?php $slugBase = pathinfo($page["slug"], PATHINFO_FILENAME); ?>
                    <a class="btn btn-edit" href="<?= htmlspecialchars($slugBase . "-edit.php"); ?>">Edit</a>
                  </td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      </section>
    </div>
  </main>

  <!-- FOOTER -->
<?php include "footer-admin.php"; ?>
</body>
