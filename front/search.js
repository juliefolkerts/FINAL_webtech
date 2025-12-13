const API_KEY = "sk-qprm691d7358c0958135610"; //remove 0 to use key

// DOM elements
const searchBtn = document.getElementById("searchBtn");
const searchInput = document.getElementById("searchInput");
const resultsContainer = document.getElementById("results");
const favouritesContainer = document.getElementById("favourites");
const errorMsg = document.getElementById("error");

// ensuring existence of DOM
if (!resultsContainer) console.error("No #results element found in the page.");
if (!searchBtn) console.error("No #searchBtn element found in the page.");
if (!searchInput) console.error("No #searchInput element found in the page.");

// loading faves from storage, normalizing to strings
let favourites = (JSON.parse(localStorage.getItem("favourites")) || []).map(String);

// saving faves to storage
function saveFavourites() {
    localStorage.setItem("favourites", JSON.stringify(favourites));
}



//comparing strings to avoid mismatches between numeric and string ids
function isFavourite(id) {
    return favourites.map(String).includes(String(id));
}


//adding/removing like/fave
function toggleFavourite(id, buttonElement) {
    const idStr = String(id);

    if (isFavourite(idStr)) {
        favourites = favourites.filter(f => String(f) !== idStr);
        buttonElement.classList.remove("liked");
        buttonElement.innerHTML = "🤍";
    } else {
        favourites.push(idStr);
        buttonElement.classList.add("liked");
        buttonElement.innerHTML = "❤️";
    }
    saveFavourites();
}


// ------------------------------
// Modal setup (defensive: create if missing)
// ------------------------------

//info popup
let modal = document.getElementById("modal");
let modalContent = document.getElementById("modal-content");

if (!modal) {
    modal = document.createElement("div");
    modal.id = "modal";
    modal.style.display = "none"; 
    document.body.appendChild(modal);

    modalContent = document.createElement("div");
    modalContent.id = "modal-content";
    modal.appendChild(modalContent);
}

// close modal when clicking outside
modal.addEventListener("click", (e) => {
    if (e.target === modal) modal.style.display = "none";
});


//opening info modal
function openModal(plant) {
    modalContent.innerHTML = `
        <h3>${plant.common_name || "Unknown plant"}</h3>
        <img src="${plant.default_image?.medium_url || "images/RoseLarge.web"}" alt="${plant.common_name}">
        <p><b>Scientific name:</b> ${plant.scientific_name?.join(", ")}</p>
        <p><b>Watering:</b> ${plant.watering}</p>
        <p><b>Sunlight:</b> ${plant.sunlight?.join(", ")}</p>
        <button class="fav-btn" onclick="modal.style.display='none'">Close</button>
    `;
    modal.style.display = "flex"; 
}


// rendering results
function renderResults(list) {
    if (!resultsContainer) return;
    resultsContainer.innerHTML = "";

    list.forEach(item => {
        // ensuring id, else creating safe id
        const id = (typeof item.id !== "undefined") ? item.id : ("mock-" + Math.random().toString(36).slice(2,9));

        const card = document.createElement("div");
        card.className = "result-card";

        const isFav = isFavourite(id);
        const heart = isFav ? "❤️" : "🤍";

        const imgSrc = (item.default_image && (item.default_image.thumbnail || item.default_image.medium_url || item.default_image.original_url))
                       || item.image
                       || 'images/RoseLarge.webp';

        // info + like btn
        card.innerHTML = `
            <img src="${imgSrc}" alt="${item.common_name || "Plant"}">
            <h3>${item.common_name || "Unknown Plant"}</h3>
            <div class="fav-btn-row" style="display:flex;justify-content:center;gap:8px;margin-top:8px;">
                <button class="fav-btn btn-like" data-id="${id}">${heart}</button>
                <button class="fav-btn btn-more" data-id="${id}">More Info</button>
            </div>
        `;

        // like btn handler
        const likeBtn = card.querySelector(".btn-like");
        if (likeBtn) {
            likeBtn.addEventListener("click", () => toggleFavourite(id, likeBtn));
        }


        // info btn handler, opens modal
        const moreBtn = card.querySelector(".btn-more");
        if (moreBtn) {
            moreBtn.addEventListener("click", () => {
                openModal(item);
            });
        }

        resultsContainer.appendChild(card);
    });
}

//
async function searchPlants(query) {
    if (!resultsContainer || !searchBtn) return;
    errorMsg && (errorMsg.textContent = "");
    resultsContainer.innerHTML = '<div class="status">Loading...</div>';

    try {
        const response = await fetch(
            `https://perenual.com/api/species-list?key=${API_KEY}&q=${encodeURIComponent(query)}`
        );

        if (!response.ok) {
            throw new Error("API request failed (HTTP " + response.status + ")");
        }

        const data = await response.json();

        if (!data || !data.data || data.error) {
            throw new Error("API returned an error or no data");
        }

        lastSearchData = data.data;
        renderResults(lastSearchData);

    } catch (error) {
        console.error("Search failed, falling back to mock data: ", error);

        //mock data 
        const mockData = [
            { id: 1, common_name: "Orchid", scientific_name: ["Orchidaceae"], watering: "Moderate", sunlight: ["Bright indirect"], default_image: { thumbnail: "https://via.placeholder.com/200" } },
            { id: 2, common_name: "Rose", scientific_name: ["Rosa"], watering: "Average", sunlight: ["Full sun"], default_image: { thumbnail: "https://via.placeholder.com/200" } }
        ];

        lastSearchData = mockData;
        resultsContainer.innerHTML = "";
        renderResults(mockData);
        if (errorMsg) errorMsg.textContent = "API limit reached. Showing mock data:";
    }
}

let lastSearchData = []; // stores latest results for re-rendering

// event listnere
if (searchBtn) {
    searchBtn.addEventListener("click", () => {
        const query = searchInput.value.trim();
        if (query.length > 0) {
            searchPlants(query);
        }
    });
}

// Enter key to search
if (searchInput) {
    searchInput.addEventListener("keydown", (e) => {
        if (e.key === "Enter") {
            searchBtn.click();
        }
    });
}
