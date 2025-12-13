<?php include "admin-header.php"; ?>
<?php require "../db.php"; ?>

<style>
  
.btn-soft {
  background: white;
  border: 1px solid #5fa8d3;
  color: #5fa8d3;
  font-weight: 500;
  transition: all 0.3s ease;
}
.btn-soft:hover {
  background: #5fa8d3;
  color: white;
}

.btn-flower {
  background: white;
  border: 1px solid #313131;
  color: #313131;
  font-weight: 500;
  transition: all 0.3s ease;
}
.btn-flower:hover {
  background: #fd5a88;
  color: rgb(252, 251, 252);
}

</style>

<body>
  
  <main class="container-fluid">
    <div class="row">
      
        <?php include "sidebar-admin.php"; ?>


      <section class="col-12 col-md-9 col-lg-10 p-4">
        <header class="d-flex justify-content-between align-items-center mb-3">
          <h1 class="h4 m-0">Profile</h1>
          <a class="btn btn-outline-dark" href="index.php">Back to Dashboard</a>
        </header>

        <form class="row g-3">
          <div class="col-md-6">
            <label for="name" class="form-label">Full Name</label>
            <input id="name" class="form-control flower-input" type="text" placeholder="Bloom Admin">
          </div>
          <div class="col-md-6">
            <label for="profileEmail" class="form-label">Email</label>
            <input id="profileEmail" class="form-control flower-input" type="email" placeholder="admin@flowershop.com">
          </div>

          <div class="col-12"><hr><h2 class="h6">Change Password</h2></div>

          <div class="col-md-4">
            <label for="current" class="form-label">Current Password</label>
            <input id="current" class="form-control flower-input" type="password">
          </div>
          <div class="col-md-4">
            <label for="new" class="form-label">New Password</label>
            <input id="new" class="form-control flower-input" type="password">
          </div>
          <div class="col-md-4">
            <label for="confirm" class="form-label">Confirm Password</label>
            <input id="confirm" class="form-control flower-input" type="password">
          </div>

          <div class="col-12 d-flex gap-2 mb-5">
            <button class="btn btn-soft" type="submit">Save</button>
            <a class="btn btn-flower" href="index.html">Cancel</a>
          </div>
        </form>
      </section>
    </div>
  </main>

  
<?php include "footer-admin.php"; ?>
</body>





