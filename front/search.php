<?php 
session_start();
$bodyClass = "gallery-page";
require "../db.php";
include "header.php"; 
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

@media (max-width: 420px) {
  .flower-card .flower-card-inner img { height: 160px !important; }
}
</style>

<div id="main">
<main class="gallery-page" >

    <h2 class="info-title">Plant & Flower Search</h2>

    <div class="search-bar">
        <input type="text" id="searchInput" class="search-input" placeholder="Search for flowers...">
        <button id="searchBtn" class="search-btn">Search</button>
    </div>

    <p id="error" style="color:red;"></p>

    <div id="results" class="results-grid"></div>

</main>
</div>

<?php include "footer.php"; ?>

<script>
function heartHtml(isFav) {
    
    return isFav ? '🤍' : '❤️';
}

document.getElementById("searchBtn").onclick = function () {
    let q = document.getElementById("searchInput").value.trim();
    if (!q) return;

    fetch("search_action.php?q=" + encodeURIComponent(q))
        .then(res => res.json())
        .then(data => {
            let container = document.getElementById("results");
            container.innerHTML = "";

            if (data.length === 0) {
                container.innerHTML = "<p>No results found.</p>";
                return;
            }

            data.forEach(item => {
                let card = document.createElement("div");
                card.className = "gallery-item flower-card";

                const alt = item.name ? item.name.replace(/"/g,'') : '';

                const favButtonHtml = `<button class="fav-toggle btn" data-id="${item.id}" data-fav="${item.is_favourite}">${item.is_favourite ? '🤍' : '❤️'}</button>`;

                card.innerHTML = `
                    <div class="flower-card-inner">
                      <img src="${item.image}" alt="${alt}">
                      <p class="caption">${item.name}</p>
                    </div>
                    <div class="card-actions card-actions-outside">
                      ${favButtonHtml}
                    </div>
                `;

                container.appendChild(card);
            });

            document.querySelectorAll(".fav-toggle").forEach(btn => {
                btn.addEventListener("click", function () {
                    const id = this.dataset.id;
                    toggleFavourite(id, this);
                });
            });

        })
        .catch(err => {
            console.error(err);
            document.getElementById("results").innerHTML = "<p>Error searching. Try again.</p>";
        });
};

function toggleFavourite(id, btnEl) {
    fetch("favourite_toggle.php?id=" + encodeURIComponent(id), { method: 'POST' })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                btnEl.dataset.fav = data.is_favourite ? '1' : '0';
                btnEl.innerHTML = data.is_favourite ? '🤍' : '❤️';
            } else {
                alert(data.message || "Action failed");
                if (data.login_required) {
                    location.href = "login.php";
                }
            }
        })
        .catch(err => {
            console.error(err);
            alert("Network error");
        });
}
</script>

</body>
