<?php include "admin-header.php"; ?>
<?php require "../db.php"; ?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Order Detail — 🌸 Flowers Admin</title>
  <link rel="icon" type="image/png" href="images/amazon-flower.png">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="style-admin.css" rel="stylesheet">
</head>

<body>
  

  <main class="container-fluid">
    <div class="row">
      <!-- SIDEBAR -->
      <?php include "sidebar-admin.php"; ?>
      </aside>

      <!-- MAIN CONTENT -->
      <section class="col-12 col-md-9 col-lg-10 p-4">
        <header class="d-flex justify-content-between align-items-center mb-3">
          <h1 class="h4 m-0">Order #2001</h1>
          <a class="btn btn-outline-dark" href="orders.php">Back to Orders</a>
        </header>

        <div class="row g-4">
          <!-- LEFT: ORDER DETAILS -->
          <div class="col-lg-8">
            <div class="card shadow-sm border-0 rounded-4">
              <div class="card-body">
                <h2 class="h6">Order Summary</h2>
                <p class="small text-muted">Date: 2025-09-21 • Status: <span class="badge bg-success">Paid</span></p>
                <div class="table-responsive">
                  <table class="table align-middle table-striped">
                    <thead>
                      <tr><th>Item</th><th>Qty</th><th>Price</th><th>Subtotal</th></tr>
                    </thead>
                    <tbody>
                      <tr><td><img src="images/RoseLarge.webp" alt="Rose" width="40" height="40" class="rounded me-2" style="object-fit:cover;">Rose</td><td>2</td><td>$12.00</td><td>$24.00</td></tr>
                      <tr><td><img src="images/sunflower.jpg" alt="Sunflower" width="40" height="40" class="rounded me-2" style="object-fit:cover;">Sunflower</td><td>1</td><td>$14.00</td><td>$14.00</td></tr>
                      <tr><td><img src="images/daisy.jpg" alt="Daisy" width="40" height="40" class="rounded me-2" style="object-fit:cover;">Daisy</td><td>3</td><td>$8.00</td><td>$24.00</td></tr>
                    </tbody>
                  </table>
                </div>
              </div>
            </div>
          </div>

          <!-- RIGHT: CUSTOMER SUMMARY -->
          <div class="col-lg-4">
            <div class="card shadow-sm border-0 rounded-4">
              <div class="card-body">
                <h3 class="h6">Customer</h3>
                <p class="mb-1">Customer 1</p>
                <p class="small text-muted">user1@example.com</p>

                <h3 class="h6 mt-3">Totals</h3>
                <dl class="row small">
                  <dt class="col-6">Subtotal</dt><dd class="col-6 text-end">$62.00</dd>
                  <dt class="col-6">Tax</dt><dd class="col-6 text-end">$6.20</dd>
                  <dt class="col-6">Shipping</dt><dd class="col-6 text-end">$3.00</dd>
                  <dt class="col-6 fw-bold">Total</dt><dd class="col-6 text-end fw-bold">$71.20</dd>
                </dl>
              </div>
            </div>
          </div>
        </div>
      </section>
    </div>
  </main>

  <!-- FOOTER identical to flowers.html -->
  <?php include "footer-admin.php"; ?>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

