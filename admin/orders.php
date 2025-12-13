<?php include "admin-header.php"; ?>
<?php require "../db.php"; ?>

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
                <th>Date</th>
                <th>Status</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td><a href="order-details.php">#2001</a></td>
                <td>Customer 1</td>
                <td>$265.26</td>
                <td>2025-09-12</td>
                <td><span class="badge bg-secondary">Shipped</span></td>
                <td><a class="btn btn-edit" href="order-details.php">View</a></td>
              </tr>
              <tr>
                <td><a href="order-detail.html">#2002</a></td>
                <td>Customer 2</td>
                <td>$116.81</td>
                <td>2025-09-18</td>
                <td><span class="badge bg-refunded">Refunded</span></td>
                <td><a class="btn btn-edit" href="order-details.php">View</a></td>
              </tr>
              <tr>
                <td><a href="order-details.php">#2003</a></td>
                <td>Customer 3</td>
                <td>$138.01</td>
                <td>2025-09-12</td>
                <td><span class="badge bg-success">Paid</span></td>
                <td><a class="btn btn-edit" href="order-details.php">View</a></td>
              </tr>
              <tr>
                <td><a href="order-details.php">#2004</a></td>
                <td>Customer 4</td>
                <td>$285.07</td>
                <td>2025-09-21</td>
                <td><span class="badge bg-warning">Pending</span></td>
                <td><a class="btn btn-edit" href="order-details.php">View</a></td>
              </tr>
            </tbody>
          </table>
        </div>
      </section>
    </div>
  </main>

  <?php include "footer-admin.php"; ?>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

