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
          <h1 class="h4 m-0">Pages</h1>
          <a class="btn btn-outline-dark" href="index.php">Back to Dashboard</a>
        </header>

        <div class="d-flex justify-content-between align-items-center mb-3">
          <h2 class="h6 m-0">Site Pages</h2>
          <a class="btn btn-soft" href="#">Add New Page</a>
        </div>

        <div class="table-responsive">
          <table class="table table-striped table-hover align-middle">
            <thead>
              <tr>
                <th>Title</th>
                <th>Slug</th>
                <th>Status</th>
                <th>Last Updated</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              <tr><td>Home</td><td>/flowers</td><td><span class="badge bg-success">Published</span></td><td>2025-09-12</td><td><a class="btn btn-edit" href="#">Edit</a></td></tr>
              <tr><td>Gallery</td><td>/gallery</td><td><span class="badge bg-success">Published</span></td><td>2025-09-10</td><td><a class="btn btn-edit" href="#">Edit</a></td></tr>
              <tr><td>FAQ</td><td>/faq</td><td><span class="badge bg-blocked">Draft</span></td><td>2025-09-20</td><td><a class="btn btn-edit" href="#">Edit</a></td></tr>
              <tr><td>Information</td><td>/information</td><td><span class="badge bg-success">Published</span></td><td>2025-09-14</td><td><a class="btn btn-edit" href="#">Edit</a></td></tr>
            </tbody>
          </table>
        </div>
      </section>
    </div>
  </main>

  <!-- FOOTER -->
<?php include "footer-admin.php"; ?>
</body>








