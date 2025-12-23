<?php
session_start();

$bodyClass = "bg-light";
include "header.php";
?>

<style>
.form-control:focus {
    border-color: #5fa8d3 !important;     
    box-shadow: 0 0 0 0.2rem rgba(95,168,211,0.5) !important; 
    outline: none !important;
}

.btn-signup {
    background: #5fa8d3;
    color: white;
    transition: 0.2s;
}

.btn-signup:hover {
    background: #3e82ad !important;
    color: white !important;
}

.card a {
    color: #5fa8d3; 
    text-decoration: none;
}

.card a:hover {
    color: #3e82ad; 
}
</style>

<main class="container py-5" style="padding-top:100px;">
  <div class="row justify-content-center" style="margin-top:65px">
    <div class="col-12 col-sm-10 col-md-8 col-lg-6">
      <div class="card shadow-sm border-0 rounded-4">
        <div class="card-body p-4">
          <h1 class="h4 mb-3 text-center text-pink" style="font-weight: 550; font-size:35px; padding-top: 20px;">🌸 Contact Us</h1>
        

          <div class="mb-4 text-center" style="padding-top: 10px;">
            <p class="mb-1">Email: <a href="mailto:contact.us@flowers.com">contact.us@flowers.com</a></p>
            <p class="mb-0">Phone: <a href="tel:+31621265814">+31621265814</a></p>
          </div>

          <form method="post" action="#">
            <div class="mb-3">
              <input type="text" name="name" placeholder="Your name" class="form-control" required>
            </div>

            <div class="mb-3">
              <input type="email" name="email" placeholder="Your email" class="form-control" required>
            </div>

            <div class="mb-3">
              <textarea name="message" placeholder="Your message" class="form-control" rows="5" required></textarea>
            </div>

            <div class="d-grid mb-3">
              <button class="btn btn-signup" type="submit">Send</button>
            </div>
          </form>

        </div>
      </div>
    </div>
  </div>
</main>

</main>

<?php include "footer.php"; ?>
