<?php 
include "admin-header.php"; 
require "../db.php"; 
?>

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

  .actions-cell {
    display: flex;
    flex-wrap: nowrap;
    align-items: center;
    gap: 8px;
    white-space: nowrap;
  }

  @media (max-width: 576px) {
    .actions-cell {
      justify-content: flex-start;
      min-width: max-content;
    }
  }
</style>

<body>

<main class="container-fluid ">
  <div class="row">

    <?php include "sidebar-admin.php"; ?>

    <section class="col-12 col-md-9 col-lg-10 p-4">

      <header class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h4 m-0">All Flowers</h1>
        <a class="btn btn-outline-dark" href="content-add.php">Add New Flower</a>
      </header>

      <?php
      $result = mysqli_query($conn, "
          SELECT flowers.*, categories.name AS category_name
          FROM flowers
          LEFT JOIN categories ON flowers.category_id = categories.id
          ORDER BY flowers.id ASC
      ");
      ?>

      <div class="table-responsive">
        <table class="table table-striped table-hover align-middle">
          <thead class="">
            <tr>
              <th>ID</th>
              <th>Name</th>
              <th>SKU</th>
              <th>Category</th>
              <th>Price</th>
              <th>Stock</th>
              <th>Visible</th>
              <th style="width:150px;">Actions</th>
            </tr>
          </thead>

          <tbody>
          <?php while ($row = mysqli_fetch_assoc($result)): ?>
            <tr>
              <td><?= $row['id'] ?></td>
              <td><?= htmlspecialchars($row['name']) ?></td>
              <td><?= htmlspecialchars($row['sku']) ?></td>
              <td><?= htmlspecialchars($row['category_name']) ?></td>
              <td>$<?= number_format($row['price'], 2) ?></td>
              <td><?= $row['stock'] ?></td>
              <td><?= $row['visible'] ? "Yes" : "No" ?></td>

              <td class="actions-cell">
                <a class="btn-edit" href="content-edit.php?id=<?= $row['id'] ?>">Edit</a>
                <a class="btn btn-delete"
                   href="content-delete.php?id=<?= $row['id'] ?>"
                   onclick="return confirm('Delete this flower?');">
                   Delete
                </a>
              </td>
            </tr>
          <?php endwhile; ?>
          </tbody>
        </table>
      </div>

    </section>
  </div>
</main>
</body>

<?php include "footer-admin.php"; ?>
