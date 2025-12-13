const API_KEY = "sk-qprm691d7358c0958135610"; //verwijder 0 om key te usen

const favouritesContainer = document.getElementById("favourites");
const modal = document.getElementById("modal");
const modalContent = document.getElementById("modal-content");

let favourites = JSON.parse(localStorage.getItem("favourites")) || [];

// save to LocalStorage
function saveFavourites() {
    localStorage.setItem("favourites", JSON.stringify(favourites));
}

// remove fave
function removeFavourite(id) {
    favourites = favourites.filter(f => f !== id);
    saveFavourites();
    loadFavourites();
}

// Open info modal
function openModal(plant) {
    modalContent.innerHTML = `
        <h3>${plant.common_name || "Unknown plant"}</h3>
        <img src="${plant.default_image?.medium_url || ""}" style="width:100%;border-radius:4px;">
        <p><b>Scientific name:</b> ${plant.scientific_name?.join(", ")}</p>
        <p><b>Watering:</b> ${plant.watering}</p>
        <p><b>Sunlight:</b> ${plant.sunlight?.join(", ")}</p>
        <button onclick="modal.style.display='none'" class="fav-btn">Close</button>
    `;
    modal.style.display = "flex";
}

// load faves
async function loadFavourites() {
    favouritesContainer.innerHTML = "";

    if (favourites.length === 0) {
        favouritesContainer.innerHTML = "<p>No favourites yet.</p>";
        return;
    }

    for (const id of favourites) {
        try {
            const res = await fetch(`https://perenual.com/api/species/details/${id}?key=${API_KEY}`);

            if (!res.ok) throw new Error("API failed");

            const plant = await res.json();

            const img = plant.default_image?.thumbnail
                      || plant.default_image?.medium_url
                      || plant.default_image?.original_url
                      || "images/RoseLarge.webp";

            const card = document.createElement("div");
            card.className = "result-card";
            card.innerHTML = `
                <img src="${img}" alt="${plant.common_name}">
                <h4>${plant.common_name || "Unknown plant"}</h4>
                <div class="fav-btn-row">
                    <button class="fav-btn btn-remove">Remove</button>
                    <button class="fav-btn btn-more">More Info</button>
                </div>
            `;

            card.querySelector(".btn-remove").addEventListener("click", () => removeFavourite(id));
            card.querySelector(".btn-more").addEventListener("click", () => openModal(plant));

            favouritesContainer.appendChild(card);

        } catch (err) {
            console.warn("Falling back to mock details for ID:", id);

            
            const mockPlant = mockDetails[id];
            if (!mockPlant) continue;

            const mockImg = "images/RoseLarge.webp";

            const card = document.createElement("div");
            card.className = "result-card";
            card.innerHTML = `
                <img src="${mockPlant.image}" alt="${mockPlant.common_name}">
                <h4>${mockPlant.common_name}</h4>
                <div class="fav-btn-row">
                    <button class="fav-btn btn-remove">Remove</button>
                    <button class="fav-btn btn-more">More Info</button>
                </div>
            `;

            card.querySelector(".btn-remove").addEventListener("click", () => removeFavourite(id));
            card.querySelector(".btn-more").addEventListener("click", () => openModal(mockPlant));

            favouritesContainer.appendChild(card);
        }
    }
}

// mock data
const mockDetails = {
    1: {
        id: 1,
        common_name: "Mock Orchid",
        image: "https://via.placeholder.com/200",
        scientific_name: ["Orchidaceae"],
        watering: "Moderate",
        sunlight: ["Bright indirect light"]
    },
    2: {
        id: 2,
        common_name: "Mock Rose",
        image: "https://via.placeholder.com/200",
        scientific_name: ["Rosa"],
        watering: "Average",
        sunlight: ["Full sun"]
    }
};


modal.addEventListener("click", (e) => {
    if (e.target === modal) modal.style.display = "none";
});

loadFavourites();

