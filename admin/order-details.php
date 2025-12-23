<?php include "admin-header.php"; ?>
<?php require "../db.php"; ?>

<?php
$orderId = isset($_GET["id"]) ? (int) $_GET["id"] : 0;
$order = null;
$items = [];
$subtotal = 0.0;

if ($orderId > 0) {
  $orderSql = "SELECT o.id, o.total, o.status, o.user_id, u.username, u.email
               FROM orders o
               LEFT JOIN users u ON u.id = o.user_id
               WHERE o.id = ?
               LIMIT 1";
  $orderStmt = mysqli_prepare($conn, $orderSql);
  mysqli_stmt_bind_param($orderStmt, "i", $orderId);
  mysqli_stmt_execute($orderStmt);
  $orderResult = mysqli_stmt_get_result($orderStmt);
  $order = mysqli_fetch_assoc($orderResult);
  mysqli_stmt_close($orderStmt);

  if ($order) {
    $itemsSql = "SELECT oi.quantity, oi.price_at_order, f.name, f.image
                 FROM order_items oi
                 JOIN flowers f ON f.id = oi.flower_id
                 WHERE oi.order_id = ?";
    $itemsStmt = mysqli_prepare($conn, $itemsSql);
    mysqli_stmt_bind_param($itemsStmt, "i", $orderId);
    mysqli_stmt_execute($itemsStmt);
    $itemsResult = mysqli_stmt_get_result($itemsStmt);
    while ($row = mysqli_fetch_assoc($itemsResult)) {
      $lineTotal = (float) $row["price_at_order"] * (int) $row["quantity"];
      $subtotal += $lineTotal;
      $row["line_total"] = $lineTotal;
      $items[] = $row;
    }
    mysqli_stmt_close($itemsStmt);
  }
}

function details_status_badge($status) {
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
          <h1 class="h4 m-0">
            <?php if ($order): ?>
              Order #<?= (int) $order["id"] ?>
            <?php else: ?>
              Order not found
            <?php endif; ?>
          </h1>
          <a class="btn btn-outline-dark" href="orders.php">Back to Orders</a>
        </header>

        <div class="row g-4">
          <!-- LEFT: ORDER DETAILS -->
          <div class="col-lg-8">
            <div class="card shadow-sm border-0 rounded-4">
              <div class="card-body">
                <h2 class="h6">Order Summary</h2>
                <?php if ($order): ?>
                  <?php $badge = details_status_badge($order["status"]); ?>
                  <p class="small text-muted">Status: <span class="badge <?= $badge ?>"><?= htmlspecialchars($order["status"] ?: "Unknown") ?></span></p>
                  <div class="table-responsive">
                    <table class="table align-middle table-striped">
                      <thead>
                        <tr><th>Item</th><th>Qty</th><th>Price</th><th>Subtotal</th></tr>
                      </thead>
                      <tbody>
                        <?php if (count($items) === 0): ?>
                          <tr><td colspan="4" class="text-center text-muted">No items found.</td></tr>
                        <?php else: ?>
                          <?php foreach ($items as $item): ?>
                            <tr>
                              <td>
                                <img src="<?= htmlspecialchars($item["image"]) ?>" alt="<?= htmlspecialchars($item["name"]) ?>" width="40" height="40" class="rounded me-2" style="object-fit:cover;">
                                <?= htmlspecialchars($item["name"]) ?>
                              </td>
                              <td><?= (int) $item["quantity"] ?></td>
                              <td>€<?= number_format((float) $item["price_at_order"], 2) ?></td>
                              <td>€<?= number_format((float) $item["line_total"], 2) ?></td>
                            </tr>
                          <?php endforeach; ?>
                        <?php endif; ?>
                      </tbody>
                    </table>
                  </div>
                <?php else: ?>
                  <p class="text-muted mb-0">Select an order from the Orders page to view its details.</p>
                <?php endif; ?>
              </div>
            </div>
          </div>

          <!-- RIGHT: CUSTOMER SUMMARY -->
          <div class="col-lg-4">
            <div class="card shadow-sm border-0 rounded-4">
              <div class="card-body">
                <h3 class="h6">Customer</h3>
                <?php if ($order): ?>
                  <?php
                    $customerName = $order["username"] ?: "User #" . (int) $order["user_id"];
                    $customerEmail = $order["email"] ?: "No email on file";
                  ?>
                  <p class="mb-1"><?= htmlspecialchars($customerName) ?></p>
                  <p class="small text-muted"><?= htmlspecialchars($customerEmail) ?></p>

                  <h3 class="h6 mt-3">Totals</h3>
                  <dl class="row small">
                    <dt class="col-6">Subtotal</dt><dd class="col-6 text-end">€<?= number_format($subtotal, 2) ?></dd>
                    <dt class="col-6 fw-bold">Total</dt><dd class="col-6 text-end fw-bold">€<?= number_format((float) $order["total"], 2) ?></dd>
                  </dl>
                <?php else: ?>
                  <p class="text-muted mb-0">No customer data available.</p>
                <?php endif; ?>
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
