<?php include "admin-header.php"; ?>
<?php require "../db.php"; ?>

<main class="container-fluid">
  <div class="row">

    <?php include "sidebar-admin.php"; ?>

    <section class="col-md-9 col-lg-10 p-4">
      <h1 class="h4">Dashboard</h1>

      <section class="row g-3 mb-4">
        <div class="col-6 col-lg-3"><div class="card h-100"><div class="card-body">
          <div class="small text-muted">Total Flowers</div><div class="h4">128</div>
        </div></div></div>

        <div class="col-6 col-lg-3"><div class="card h-100"><div class="card-body">
          <div class="small text-muted">New Orders</div><div class="h4">23</div>
        </div></div></div>

        <div class="col-6 col-lg-3"><div class="card h-100"><div class="card-body">
          <div class="small text-muted">Pending Shipments</div><div class="h4">5</div>
        </div></div></div>

        <div class="col-6 col-lg-3"><div class="card h-100"><div class="card-body">
          <div class="small text-muted">Active Customers</div><div class="h4">14</div>
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
