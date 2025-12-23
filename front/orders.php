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
$orders = [];

$sql = "SELECT id, total, status
        FROM orders
        WHERE user_id = ? AND status IN ('completed', 'pending')
        ORDER BY id DESC";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $userId);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
while ($row = mysqli_fetch_assoc($result)) {
    $orders[] = $row;
}
mysqli_stmt_close($stmt);

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

<style>
  .badge.bg-success,
  .text-bg-success {
    background-color: #b9e2b0 !important;
    color: #1a1919 !important;
  }

  .badge.bg-warning,
  .text-bg-warning {
    background-color: #fff1a2 !important;
    color: #1a1919 !important;
  }
</style>

<main class="container py-5" style="padding-top:100px;">
  <div class="row justify-content-center" style="margin-top:65px">
    <div class="col-12 col-lg-10">
      <div class="card shadow-sm border-0 rounded-4">
        <div class="card-body p-4">
          <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
              <h1 class="h4 mb-1">My Orders</h1>
              <p class="text-muted small mb-0">Track your recent orders and view their details.</p>
            </div>
            <a class="btn btn-outline-dark btn-sm" href="profile.php">Back to Profile</a>

          </div>

          <div class="table-responsive">
            <table class="table table-striped table-hover align-middle mb-0">
              <thead>
                <tr>
                  <th>Order #</th>
                  <th>Total</th>
                  <th>Status</th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody>
                <?php if (count($orders) === 0): ?>
                  <tr>
                    <td colspan="4" class="text-center text-muted">No orders found.</td>
                  </tr>
                <?php else: ?>
                  <?php foreach ($orders as $order): ?>
                    <?php $badge = order_status_badge($order["status"]); ?>
                    <tr>
                      <td>#<?= (int) $order["id"] ?></td>
                      <td>€<?= number_format((float) $order["total"], 2) ?></td>
                      <td><span class="badge <?= $badge ?>"><?= htmlspecialchars($order["status"]) ?></span></td>
                      <td>
                        <a class="btn btn-outline-dark btn-sm" href="order-details.php?id=<?= (int) $order["id"] ?>">View</a>
                      </td>
                    </tr>
                  <?php endforeach; ?>
                <?php endif; ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
</main>

<?php include "footer.php"; ?>
