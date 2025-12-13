<?php include "admin-header.php"; ?>
<?php require "../db.php"; ?>

<?php
// Get the user ID from URL
$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

// Fetch user data
$user = mysqli_fetch_assoc(
    mysqli_query($conn, "SELECT id, username, email FROM users WHERE id=$id")
);

// als user niet gevonden, redirect back
if (!$user) {
    header("Location: customers.php");
    exit;
}

// fecth orders
$orders_result = mysqli_query($conn, "
    SELECT id, created_at, total, status
    FROM orders
    WHERE user_id=$id
    ORDER BY id DESC
");
?>

<body>

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
                <a class="btn btn-outline-dark btn-sm" href="customers.php">Back to List</a>
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
                            <td><?= htmlspecialchars($order['created_at']) ?></td>
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
                  <ul id="customer-fav-list"></ul>

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

  <!-- API stuff for favorites -->
  <script>
    const favList = document.getElementById("customer-fav-list");
    const favs = JSON.parse(localStorage.getItem("flowerFavorites")) || [];

    if (favs.length === 0) {
      favList.innerHTML = "<li>No favorites saved.</li>";
    } else {
      favs.forEach(f => {
        const li = document.createElement("li");
        li.textContent = `${f.name} — ${f.color || ""} (${f.meaning || ""})`;
        favList.appendChild(li);
      });
    }
  </script>

</body>
