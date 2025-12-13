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
          <h1 class="h4 m-0">Settings</h1>
          <a class="btn btn-outline-dark" href="index.php">Back to Dashboard</a>
        </header>

        <form class="row g-3">
          <div class="col-md-6">
            <label for="siteName" class="form-label">Store Name</label>
            <input id="siteName" class="form-control flower-input" type="text" placeholder="Flowers by Bloom">
          </div>
          <div class="col-md-6">
            <label for="email" class="form-label">Contact Email</label>
            <input id="email" class="form-control flower-input" type="email" placeholder="hello@flowershop.com">
          </div>

          <div class="col-md-4">
            <label for="currency" class="form-label">Currency</label>
            <select id="currency" class="form-select flower-select">
              <option>USD</option><option>EUR</option><option>GBP</option>
            </select>
          </div>
          <div class="col-md-4">
            <label for="timezone" class="form-label">Timezone</label>
            <select id="timezone" class="form-select flower-select">
              <option>Europe/London</option><option>UTC</option>
            </select>
          </div>
          <div class="col-md-4">
            <label for="brandColor" class="form-label">Accent Color</label>
            <input id="brandColor" class="form-control flower-input" type="text" placeholder="#ffe4ec">
          </div>

          <div class="col-12">
            <label class="form-label">Branding Image</label>
            <div class="border rounded p-3 d-flex justify-content-between align-items-center">
              <span class="text-muted">No file chosen</span>
              <button class="btn btn-outline-dark" type="button">Choose file</button>
            </div>
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




