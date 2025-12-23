<?php
session_start();
require "../db.php";

$bodyClass = "bg-light";
include "header.php";

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit;
}

$userId = (int) $_SESSION["user_id"];
$orderId = isset($_GET["id"]) ? (int) $_GET["id"] : 0;
$order = null;
$items = [];
$subtotal = 0.0;

if ($orderId > 0) {
    $orderSql = "SELECT id, total, status FROM orders WHERE id = ? AND user_id = ? LIMIT 1";
    $orderStmt = mysqli_prepare($conn, $orderSql);
    mysqli_stmt_bind_param($orderStmt, "ii", $orderId, $userId);
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

function order_status_badge($status) {
    $normalized = strtolower(trim((string) $status));
    switch ($normalized) {
        case 'completed':
            return 'bg-success';
        case 'pending':
            return 'bg-warning';
        default:
            return 'bg-secondary';
    }
}
?>

<body class="bg-light">
<div id="main">
  <main class="container py-5" style="padding-top:100px;">
    <div class="d-flex justify-content-between align-items-center mb-4">
      <h1 class="h4 mb-0">
        <?php if ($order): ?>
          Order #<?= (int) $order["id"] ?>
        <?php else: ?>
          Order not found
        <?php endif; ?>
      </h1>
      <a class="btn btn-outline-dark btn-sm" href="orders.php">Back to Orders</a>
    </div>

    <div class="row g-4">
      <div class="col-lg-8">
        <div class="card shadow-sm border-0 rounded-4">
          <div class="card-body">
            <h2 class="h6">Order Summary</h2>
            <?php if ($order): ?>
              <?php $badge = order_status_badge($order["status"]); ?>
              <p class="small text-muted">Status: <span class="badge <?= $badge ?>"><?= htmlspecialchars($order["status"]) ?></span></p>
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

      <div class="col-lg-4">
        <div class="card shadow-sm border-0 rounded-4">
          <div class="card-body">
            <h3 class="h6">Totals</h3>
            <?php if ($order): ?>
              <dl class="row small">
                <dt class="col-6">Subtotal</dt><dd class="col-6 text-end">€<?= number_format($subtotal, 2) ?></dd>
                <dt class="col-6 fw-bold">Total</dt><dd class="col-6 text-end fw-bold">€<?= number_format((float) $order["total"], 2) ?></dd>
              </dl>
            <?php else: ?>
              <p class="text-muted mb-0">No totals available.</p>
            <?php endif; ?>
          </div>
        </div>
      </div>
    </div>
  </main>
</div>

<?php include "footer.php"; ?>
