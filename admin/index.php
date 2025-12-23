<?php include "admin-header.php"; ?>
<?php require "../db.php"; ?>

<?php
$totalFlowers = 0;
$newOrders = 0;
$totalOrders = 0;
$activeCustomers = 0;

$result = mysqli_query($conn, "SELECT COALESCE(SUM(stock), 0) AS total FROM flowers");
if ($result) {
    $row = mysqli_fetch_assoc($result);
    $totalFlowers = (int) $row["total"];
}

$result = mysqli_query($conn, "SELECT COUNT(*) AS total FROM flowers");
if ($result) {
    $row = mysqli_fetch_assoc($result);
    $newOrders = (int) $row["total"];
}

$result = mysqli_query($conn, "SELECT COUNT(*) AS total FROM orders");
if ($result) {
    $row = mysqli_fetch_assoc($result);
    $totalOrders = (int) $row["total"];
}

$result = mysqli_query($conn, "SELECT COUNT(*) AS total FROM users WHERE role <> 'admin' OR role IS NULL");
if ($result) {
    $row = mysqli_fetch_assoc($result);
    $activeCustomers = (int) $row["total"];
}
?>

<?php
$recentOrders = [];
$recentSql = "SELECT o.id, o.total, o.status, o.user_id, u.username, u.email,
                     GROUP_CONCAT(DISTINCT f.name ORDER BY f.name SEPARATOR ', ') AS flowers
              FROM orders o
              LEFT JOIN users u ON u.id = o.user_id
              LEFT JOIN order_items oi ON oi.order_id = o.id
              LEFT JOIN flowers f ON f.id = oi.flower_id
              GROUP BY o.id
              ORDER BY o.id DESC
              LIMIT 3";
$recentResult = mysqli_query($conn, $recentSql);
if ($recentResult) {
    while ($row = mysqli_fetch_assoc($recentResult)) {
        $recentOrders[] = $row;
    }
}

function dashboard_status_badge($status) {
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

<main class="container-fluid">
  <div class="row">

    <?php include "sidebar-admin.php"; ?>

    <section class="col-md-9 col-lg-10 p-4">
      <h1 class="h4">Dashboard</h1>

      <section class="row g-3 mb-4">
        <div class="col-6 col-lg-3"><div class="card h-100"><div class="card-body">
          <div class="small text-muted">Total Inventory</div><div class="h4"><?php echo $totalFlowers; ?></div>
        </div></div></div>

        <div class="col-6 col-lg-3"><div class="card h-100"><div class="card-body">
          <div class="small text-muted">Flower types</div><div class="h4"><?php echo $newOrders; ?></div>
        </div></div></div>

        <div class="col-6 col-lg-3"><div class="card h-100"><div class="card-body">
          <div class="small text-muted">Total Orders</div><div class="h4"><?php echo $totalOrders; ?></div>
        </div></div></div>

        <div class="col-6 col-lg-3"><div class="card h-100"><div class="card-body">
          <div class="small text-muted">Active Customers</div><div class="h4"><?php echo $activeCustomers; ?></div>
        </div></div></div>
      </section>

      <h2 class="h6">Recent Orders</h2>
      <div class="table-responsive">
        <table class="table table-striped table-hover align-middle">
          <thead><tr><th>Order #</th><th>Customer</th><th>Flower</th><th>Total</th><th>Status</th></tr></thead>
          <tbody>
            <?php if (count($recentOrders) === 0): ?>
              <tr><td colspan="5" class="text-center text-muted">No orders found.</td></tr>
            <?php else: ?>
              <?php foreach ($recentOrders as $order): ?>
                <?php
                  $customer = $order["username"] ?: $order["email"];
                  if (!$customer) {
                    $customer = "User #" . (int) $order["user_id"];
                  }
                  $flowerList = $order["flowers"] ?: "No items";
                  $badge = dashboard_status_badge($order["status"]);
                ?>
                <tr>
                  <td>#<?= (int) $order["id"] ?></td>
                  <td><?= htmlspecialchars($customer) ?></td>
                  <td><?= htmlspecialchars($flowerList) ?></td>
                  <td>€<?= number_format((float) $order["total"], 2) ?></td>
                  <td><span class="badge <?= $badge ?>"><?= htmlspecialchars($order["status"] ?: "Unknown") ?></span></td>
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
