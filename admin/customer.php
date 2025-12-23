<?php include "admin-header.php"; ?>
<?php require "../db.php"; ?>


<?php
// Get the user ID from URL
$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

// Fetch user data
$stmt = mysqli_prepare($conn, "SELECT id, username, email FROM users WHERE id = ?");
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$user = mysqli_fetch_assoc($result);
mysqli_stmt_close($stmt);

// als user niet gevonden, redirect back
if (!$user) {
    header("Location: customers.php");
    exit;
}

// fecth orders
$stmt = mysqli_prepare($conn, "
    SELECT id, created_at, total, status
    FROM orders
    WHERE user_id = ?
    ORDER BY id DESC
");
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$orders_result = mysqli_stmt_get_result($stmt);
mysqli_stmt_close($stmt);

// fetch favourites
$stmt = mysqli_prepare($conn, "
    SELECT flowers.name, flowers.image
    FROM favourites
    JOIN flowers ON flowers.id = favourites.flower_id
    WHERE favourites.user_id = ?
    ORDER BY favourites.id DESC
");
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$favourites_result = mysqli_stmt_get_result($stmt);
mysqli_stmt_close($stmt);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_customer'])) {
    $delete_id = (int) $_POST['delete_customer'];
    if ($delete_id > 0) {
        $stmt = mysqli_prepare($conn, "DELETE FROM favourites WHERE user_id = ?");
        mysqli_stmt_bind_param($stmt, "i", $delete_id);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);

        $stmt = mysqli_prepare($conn, "DELETE FROM users WHERE id = ?");
        mysqli_stmt_bind_param($stmt, "i", $delete_id);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
    }
    header("Location: customers.php");
    exit;
}
?>

<body>
  <style>
    .customer-fav-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(120px, 1fr));
      gap: 12px;
    }

    .customer-fav-grid .gallery-item {
      border-radius: 8px;
    }

    .customer-fav-grid .gallery-item img {
      width: 100%;
      height: 120px;
      object-fit: cover;
      border-radius: 8px;
      display: block;
    }

    .customer-fav-grid .gallery-item .caption {
      position: static;
      opacity: 1;
      background: none;
      font-size: 0.75rem;
      padding: 6px 0 0;
      text-align: center;
      transform: none;
    }
    .btn-dcustomer {
      background: white;
      border: 1px solid #313131;
      color: #313131;
      font-weight: 500;
      font-size: 12px;
      transition: all 0.3s ease;
      }
      .btn-dcustomer:hover {
        background: #fd5a88;
        color: rgb(252, 251, 252);
      }
  </style>

  <main class="container-fluid">
    <div class="row">
      <!-- SIDEBAR -->
      <?php include "sidebar-admin.php"; ?>

      <!-- MAIN CONTENT -->
      <section class="col-12 col-md-9 col-lg-10 p-4">
        <header class="d-flex justify-content-between align-items-center mb-3">
          <h1 class="h4 m-0">Customer Profile</h1>
          <a class="btn btn-outline-dark" href="customers.php">Back to Customers</a>
        </header>

        <div class="row g-4">
          <!-- LEFT CARD -->
          <div class="col-lg-4">
            <div class="card shadow-sm border-0 rounded-4">
              <div class="card-body">
                <h2 class="h6">Customer</h2>
                <p class="mb-1"><?= htmlspecialchars($user['username']) ?></p>
                <p class="small text-muted"><?= htmlspecialchars($user['email']) ?></p>
                <form id="delete-customer-form" method="POST">
                  <input type="hidden" name="delete_customer" value="<?= (int) $user['id'] ?>">
                  <button class="btn btn-dcustomer btn-sm" type="submit">Delete Customer</button>
                </form>
              </div>
            </div>
          </div>

          <!-- RIGHT CARD -->
          <div class="col-lg-8">
            <div class="card shadow-sm border-0 rounded-4">
              <div class="card-body">
                <h3 class="h6">Past Orders</h3>
                <div class="table-responsive">
                  <table class="table table-sm align-middle">
                    <thead>
                      <tr>
                        <th>Order #</th>
                        <th>Date</th>
                        <th>Total</th>
                        <th>Status</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php if ($orders_result && mysqli_num_rows($orders_result) > 0): ?>
                        <?php while ($order = mysqli_fetch_assoc($orders_result)): ?>
                          <tr>
                            <td><a href="order-detail.php?id=<?= $order['id'] ?>">#<?= $order['id'] ?></a></td>
                            <?= htmlspecialchars($order['created_at']) ?>
                            <td>$<?= number_format($order['total'], 2) ?></td>
                            <td>
                              <?php
                                $statusClass = $order['status'] === 'Paid' ? 'bg-success' :
                                               ($order['status'] === 'Pending' ? 'bg-warning' : 'bg-secondary');
                              ?>
                              <span class="badge <?= $statusClass ?>"><?= htmlspecialchars($order['status']) ?></span>
                            </td>
                          </tr>
                        <?php endwhile; ?>
                      <?php else: ?>
                        <tr>
                          <td colspan="4" class="text-center">No orders found.</td>
                        </tr>
                      <?php endif; ?>
                    </tbody>
                  </table>

                  <!-- Saved Favorites -->
                  <h3 class="h6">Saved Favorites</h3>
                  <div id="customer-fav-list" class="customer-fav-grid">
                    <?php if ($favourites_result && mysqli_num_rows($favourites_result) > 0): ?>
                      <?php while ($fav = mysqli_fetch_assoc($favourites_result)): ?>
                        <div class="gallery-item">
                          <img src="<?= htmlspecialchars($fav['image']) ?>" alt="<?= htmlspecialchars($fav['name']) ?>">
                          <p class="caption"><?= htmlspecialchars($fav['name']) ?></p>
                        </div>
                      <?php endwhile; ?>
                    <?php else: ?>
                      <p>No favorites saved.</p>
                    <?php endif; ?>
                  </div>

                </div>
              </div>
            </div>
          </div>
        </div>
      </section>
    </div>
  </main>

  <!-- FOOTER -->
  <?php include "footer-admin.php"; ?>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    const deleteForm = document.getElementById("delete-customer-form");
    if (deleteForm) {
      deleteForm.addEventListener("submit", function (event) {
        if (!confirm("Are you sure you want to delete this user?")) {
          event.preventDefault();
        }
      });
    }
  </script>

</body>
