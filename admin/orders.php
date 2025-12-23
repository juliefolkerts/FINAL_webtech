<?php include "admin-header.php"; ?>
<?php require "../db.php"; ?>

<?php
$orders = [];
$sql = "SELECT o.id, o.total, o.status, o.user_id, u.username, u.email
        FROM orders o
        LEFT JOIN users u ON u.id = o.user_id
        ORDER BY o.id DESC";
$result = mysqli_query($conn, $sql);
if ($result) {
  while ($row = mysqli_fetch_assoc($result)) {
    $orders[] = $row;
  }
}

function order_status_badge($status) {
  $normalized = strtolower(trim((string) $status));
  switch ($normalized) {
    case 'paid':
      return 'bg-success';
    case 'pending':
      return 'bg-warning';
    case 'shipped':
      return 'bg-secondary';
    case 'refunded':
      return 'bg-refunded';
    default:
      return 'bg-secondary';
  }
}
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
</style>

<body>
  

  <main class="container-fluid">
    <div class="row">
      <!-- SIDEBAR -->
      <?php include "sidebar-admin.php"; ?>

      <!-- MAIN CONTENT -->
      <section class="col-12 col-md-9 col-lg-10 p-4">
        <header class="d-flex justify-content-between align-items-center mb-3">
          <h1 class="h4 m-0">Orders</h1>
          <a class="btn btn-outline-dark" href="index.php">Back to Dashboard</a>
        </header>

        <!-- ORDERS TABLE -->
        <div class="table-responsive">
          <table class="table table-striped table-hover align-middle">
            <thead>
              <tr>
                <th>Order #</th>
                <th>Customer</th>
                <th>Total</th>
                <th>Status</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              <?php if (count($orders) === 0): ?>
                <tr>
                  <td colspan="5" class="text-center text-muted">No orders found.</td>
                </tr>
              <?php else: ?>
                <?php foreach ($orders as $order): ?>
                  <?php
                    $customer = $order["username"] ?: $order["email"];
                    if (!$customer) {
                      $customer = "User #" . (int) $order["user_id"];
                    }
                    $badge = order_status_badge($order["status"]);
                  ?>
                  <tr>
                    <td><a href="order-details.php?id=<?= (int) $order["id"] ?>">#<?= (int) $order["id"] ?></a></td>
                    <td><?= htmlspecialchars($customer) ?></td>
                    <td>€<?= number_format((float) $order["total"], 2) ?></td>
                    <td><span class="badge <?= $badge ?>"><?= htmlspecialchars($order["status"] ?: "Unknown") ?></span></td>
                    <td><a class="btn btn-edit" href="order-details.php?id=<?= (int) $order["id"] ?>">View</a></td>
                  </tr>
                <?php endforeach; ?>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </section>
    </div>
  </main>

  <?php include "footer-admin.php"; ?>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
