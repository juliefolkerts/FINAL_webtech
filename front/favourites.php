<?php
session_start();
$bodyClass = "gallery-page";
require "../db.php";
include "header.php";
if (!isset($_SESSION["user_id"])) {
    die("<p style='padding:20px;'>Please login first.</p>");
}
$user = (int) $_SESSION["user_id"];
$sql = "SELECT f.id AS fav_id, flowers.*, c.id AS cart_id
        FROM favourites f
        JOIN flowers ON flowers.id = f.flower_id
        LEFT JOIN cart c ON c.flower_id = flowers.id AND c.user_id = f.user_id
        WHERE f.user_id = ?
        ORDER BY f.id DESC";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $user);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
?>

<body class="gallery-page">

<style>
.gallery-item.flower-card {
  display: flex !important;
  flex-direction: column !important;
  align-items: stretch !important;
  width: 100% !important;
  max-width: 340px !important;
  box-sizing: border-box !important;
  border-radius: 10px !important;
  overflow: visible !important;
  margin: 0 !important;
}

.flower-card .flower-card-inner {
  position: relative !important;
  overflow: hidden !important;
  border-radius: 10px !important;
  width: 100% !important;
  box-sizing: border-box !important;
  background: transparent !important;
}

.flower-card .flower-card-inner img {
  width: 100% !important;
  height: 200px !important;
  object-fit: cover !important;
  display: block !important;
  border-radius: 10px !important;
}

.flower-card .caption {
  position: absolute !important;
  bottom: 0 !important;
  left: 0 !important;
  width: 100% !important;
  padding: 6px 8px !important;
  margin: 0 !important;
  box-sizing: border-box !important;
  text-align: center !important;
  background: rgba(255,228,236,0.95) !important;
  color: #333 !important;
  font-size: 0.95rem !important;
  transform: translateY(100%) !important;
  opacity: 0 !important;
  transition: transform 0.28s ease, opacity 0.28s ease !important;
  z-index: 3 !important;
}

.flower-card .flower-card-inner:hover .caption,
.flower-card .flower-card-inner:focus-within .caption {
  transform: translateY(0%) !important;
  opacity: 1 !important;
}

.flower-card .card-actions.card-actions-outside {
  display: flex !important;
  justify-content: center !important;
  align-items: center !important;
  margin-top: 12px !important;
  width: 100% !important;
  box-sizing: border-box !important;
  z-index: 2 !important;
}

.flower-card .card-actions.card-actions-outside .btn,
.flower-card .card-actions.card-actions-outside .fav-toggle {
  position: relative !important;
  z-index: 6 !important;
}

.results-grid .flower-card {
  flex: 0 1 calc(25% - 20px) !important;
  max-width: calc(25% - 20px) !important;
}

@media (max-width: 1024px) {
  .results-grid .flower-card {
    flex-basis: calc(33.333% - 20px) !important;
    max-width: calc(33.333% - 20px) !important;
  }
}

@media (max-width: 768px) {
  .results-grid .flower-card {
    flex-basis: calc(50% - 20px) !important;
    max-width: calc(50% - 20px) !important;
  }
}

@media (max-width: 480px) {
  .results-grid .flower-card {
    flex-basis: 100% !important;
    max-width: 100% !important;
  }
}

@media (max-width: 420px) {
  .flower-card .flower-card-inner img { height: 160px !important; }
}
</style>

<div id="main">
<main class="gallery-page">

<h2 class="info-title">Your Favourite Flowers</h2>

<div class="results-grid" >
<?php while ($f = mysqli_fetch_assoc($result)): ?>
    <div class="gallery-item flower-card">
        <div class="flower-card-inner">
            <img src="<?= htmlspecialchars($f['image']) ?>" alt="<?= htmlspecialchars($f['name']) ?>">
            <p class="caption"><?= htmlspecialchars($f['name']) ?></p>
        </div>
        <div class="card-actions card-actions-outside">
          <button class="fav-toggle btn" data-id="<?= $f['id'] ?>" data-fav="1">🤍</button>
          <?php if (!$f['cart_id']): ?>
          <button class="btn btn-outline-dark btn-sm add-cart" data-id="<?= $f['id'] ?>">Add to Cart</button>
          <?php endif; ?>
        </div>
    </div>
<?php endwhile; ?>
</div>

</main>
</div>

<?php include "footer.php"; ?>

<script>
document.querySelectorAll(".fav-toggle").forEach(btn => {
    btn.addEventListener("click", function () {
        const id = this.dataset.id;
        fetch("favourite_toggle.php?id=" + encodeURIComponent(id), { method: 'POST' })
            .then(r => r.json())
            .then(j => {
                if (j.success) {
                    this.dataset.fav = j.is_favourite ? '1' : '0';
                    this.innerHTML = j.is_favourite ? '🤍' : '❤️';
                    if (!j.is_favourite) {
                        this.closest(".gallery-item").remove();
                    }
                } else if (j.login_required) {
                    location.href = "login.php";
                } else {
                    alert(j.message || "Failed");
                }
            }).catch(() => alert("Network error"));
    });
});

document.querySelectorAll(".add-cart").forEach(btn => {
    btn.addEventListener("click", function () {
        const id = this.dataset.id;
        fetch("cart_toggle.php?id=" + encodeURIComponent(id), { method: 'POST' })
            .then(r => r.json())
            .then(j => {
                if (j.success) {
                    if (j.in_cart) {
                        this.remove();
                    } else {
                        this.textContent = "Add to Cart";
                    }
                } else if (j.login_required) {
                    location.href = "login.php";
                } else {
                    alert(j.message || "Failed");
                }
            }).catch(() => alert("Network error"));
    });
});
</script>

</body>
