<?php include "admin-header.php"; ?>
<?php require "../db.php"; ?>

<?php
$totalFlowers = 0;
$newOrders = 0;
$pendingShipments = 0;
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
    $pendingShipments = (int) $row["total"];
}

$result = mysqli_query($conn, "SELECT COUNT(*) AS total FROM users WHERE role <> 'admin' OR role IS NULL");
if ($result) {
    $row = mysqli_fetch_assoc($result);
    $activeCustomers = (int) $row["total"];
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
          <div class="small text-muted">Total Orders</div><div class="h4"><?php echo $pendingShipments; ?></div>
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
            <tr><td>#1001</td><td>Alice Bloom</td><td>Roses</td><td>$45</td><td><span class="badge bg-success">Paid</span></td></tr>
            <tr><td>#1002</td><td>John Petal</td><td>Sunflowers</td><td>$30</td><td><span class="badge bg-secondary">Shipped</span></td></tr>
            <tr><td>#1003</td><td>Lily Grace</td><td>Tulips</td><td>$28</td><td><span class="badge bg-warning">Pending</span></td></tr>
          </tbody>
        </table>
      </div>
    </section>

  </div>
</main>

<?php include "footer-admin.php"; ?>
