<?php
session_start();
$bodyClass = "gallery-page";
require "../db.php";
include "header.php";
if (!isset($_SESSION["user_id"])) {
    die("<p style='padding:20px;'>Please login first.</p>");
}
$user = (int) $_SESSION["user_id"];
$sql = "SELECT c.id AS cart_id, c.quantity, flowers.* 
        FROM cart c
        JOIN flowers ON flowers.id = c.flower_id
        WHERE c.user_id = ?
          AND flowers.visible = 1
        ORDER BY c.id DESC";
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
  margin-top: 8px !important;         
  width: 100% !important;
  box-sizing: border-box !important;
  z-index: 2 !important;
}

.flower-card .price-details {
  margin-top: 10px !important;
  padding: 8px 16px !important;
  background: #fff4f7 !important;
  border-radius: 10px !important;
  text-align: left !important;
  font-size: 0.9rem !important;
  color: #333 !important;
  box-sizing: border-box !important;
}

.flower-card .price-details .item-title {
  font-weight: 700 !important;
  font-size: 1rem !important;
}

.flower-card .price-details .line-total {
  font-weight: 600 !important;
}

.flower-card .quantity-control {
  display: flex !important;
  justify-content: center !important;
  align-items: center !important;
  gap: 8px !important;
  margin-top: 8px !important;
}

.flower-card .quantity-control input {
  width: 70px !important;
  text-align: center !important;
  border-radius: 999px !important;
}

.order-total {
  margin-top: 24px !important;
  text-align: right !important;
  font-size: 1.1rem !important;
  font-weight: 600 !important;
  color: #333 !important;
  display: flex !important;
  justify-content: flex-end !important;
  align-items: center !important;
  gap: 12px !important;
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

<h2 class="info-title">Your Shopping Basket</h2>

<div class="results-grid" >
<?php
$orderTotal = 0;
while ($f = mysqli_fetch_assoc($result)):
    $quantity = isset($f['quantity']) ? (int) $f['quantity'] : 1;
    if ($quantity < 1) {
        $quantity = 1;
    }
    $unitPrice = (float) $f['price'];
    $lineTotal = $unitPrice * $quantity;
    $orderTotal += $lineTotal;
?>
    <div class="gallery-item flower-card">
        <div class="flower-card-inner">
            <img src="<?= htmlspecialchars($f['image']) ?>" alt="<?= htmlspecialchars($f['name']) ?>">
            <p class="caption"><?= htmlspecialchars($f['name']) ?></p>
        </div>
        <div class="price-details" data-price="<?= htmlspecialchars($unitPrice) ?>">
            <div class="item-title"><?= htmlspecialchars($f['name']) ?></div>
            <div>Price: €<?= number_format($unitPrice, 2) ?></div>
            <div class="quantity-control">
                <label for="qty-<?= $f['id'] ?>">Amount</label>
                <input id="qty-<?= $f['id'] ?>" class="quantity-input" type="number" min="1" value="<?= $quantity ?>" data-id="<?= $f['id'] ?>">
            </div>
            <div>Total: €<span class="line-total" data-line-total="<?= htmlspecialchars($lineTotal) ?>"><?= number_format($lineTotal, 2) ?></span></div>
        </div>
        <div class="card-actions card-actions-outside">
          <button class="btn btn-outline-dark btn-sm remove-cart" data-id="<?= $f['id'] ?>">Remove</button>
        </div>
    </div>
<?php endwhile; ?>
</div>

<div class="order-total">
  <span>Order total: €<span id="order-total"><?= number_format($orderTotal, 2) ?></span></span>
  <button id="place-order" class="btn btn-dark btn-sm">Place Order</button>
</div>

</main>
</div>

<?php include "footer.php"; ?>

<script>
document.querySelectorAll(".remove-cart").forEach(btn => {
    btn.addEventListener("click", function () {
        const id = this.dataset.id;
        fetch("cart_toggle.php?id=" + encodeURIComponent(id), { method: 'POST' })
            .then(r => r.json())
            .then(j => {
                if (j.success) {
                    this.closest(".gallery-item").remove();
                    updateOrderTotal();
                } else if (j.login_required) {
                    location.href = "login.php";
                } else {
                    alert(j.message || "Failed");
                }
            }).catch(() => alert("Network error"));
    });
});

function updateOrderTotal() {
    let total = 0;
    document.querySelectorAll(".line-total").forEach(line => {
        const value = parseFloat(line.dataset.lineTotal || "0");
        if (!Number.isNaN(value)) {
            total += value;
        }
    });
    const totalEl = document.getElementById("order-total");
    if (totalEl) {
        totalEl.textContent = total.toFixed(2);
    }
}

document.querySelectorAll(".quantity-input").forEach(input => {
    input.addEventListener("change", function () {
        const id = this.dataset.id;
        let quantity = parseInt(this.value, 10);
        if (!quantity || quantity < 1) {
            quantity = 1;
            this.value = 1;
        }

        const formData = new URLSearchParams();
        formData.append("id", id);
        formData.append("quantity", quantity);

        fetch("cart_update.php", { method: "POST", body: formData })
            .then(r => r.json())
            .then(j => {
                if (j.success) {
                    const card = this.closest(".flower-card");
                    const priceHolder = card ? card.querySelector(".price-details") : null;
                    const unitPrice = priceHolder ? parseFloat(priceHolder.dataset.price || "0") : 0;
                    const lineTotal = unitPrice * quantity;
                    const lineEl = card ? card.querySelector(".line-total") : null;
                    if (lineEl) {
                        lineEl.dataset.lineTotal = lineTotal.toFixed(2);
                        lineEl.textContent = lineTotal.toFixed(2);
                    }
                    updateOrderTotal();
                } else if (j.login_required) {
                    location.href = "login.php";
                } else {
                    alert(j.message || "Failed");
                }
            })
            .catch(() => alert("Network error"));
    });
});

const placeOrderBtn = document.getElementById("place-order");
if (placeOrderBtn) {
    placeOrderBtn.addEventListener("click", function () {
        fetch("place_order.php", { method: "POST" })
            .then(r => r.json())
            .then(j => {
                if (j.success) {
                    alert("Order placed!");
                    location.reload();
                } else if (j.login_required) {
                    location.href = "login.php";
                } else {
                    alert(j.message || "Failed");
                }
            })
            .catch(() => alert("Network error"));
    });
}
</script>

</body>
