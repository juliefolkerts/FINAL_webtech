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
</style>
<body>
  

  <main class="container-fluid">
    <div class="row">
      <?php include "sidebar-admin.php"; ?>

      <!-- MAIN CONTENT -->
      <section class="col-12 col-md-9 col-lg-10 p-4">
        <header class="d-flex justify-content-between align-items-center mb-3">
          <h1 class="h4 m-0">Customers</h1>
          <a class="btn btn-outline-dark" href="index.php">Back to Dashboard</a>
        </header>

        <!-- CUSTOMERS TABLE -->
        <div class="table-responsive">
          <table class="table table-striped table-hover align-middle">
            <thead>
              <tr>
                <th>ID</th>
                <th>Username</th>
                <th>Email</th>
                <th style="width:120px;">Actions</th>
              </tr>
            </thead>
            <tbody>
<?php
$sql = "SELECT id, username, email FROM users WHERE role <> 'admin' ORDER BY id ASC";
$result = mysqli_query($conn, $sql);

if ($result === false) {
    
    $result = mysqli_query($conn, "SELECT id, username, email FROM users ORDER BY id ASC");
}

if ($result && mysqli_num_rows($result) > 0):
    while ($user = mysqli_fetch_assoc($result)):
?>
              <tr>
                <td><?= htmlspecialchars($user['id']) ?></td>
                <td><?= htmlspecialchars($user['username']) ?></td>
                <td><a href="customer.php?id=<?= urlencode($user['id']) ?>"><?= htmlspecialchars($user['email']) ?></a></td>
                <td><a class="btn btn-edit" href="customer.php?id=<?= urlencode($user['id']) ?>">View</a></td>
              </tr>
<?php
    endwhile;
else:
?>
              <tr>
                <td colspan="4" class="text-center">No customers found.</td>
              </tr>
<?php
endif;
?>
            </tbody>
          </table>
        </div>
      </section>
    </div>
  </main>

  <?php include "footer-admin.php"; ?>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
