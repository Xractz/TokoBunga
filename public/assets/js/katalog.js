// ==============================
// FETCH PRODUCT CATALOG
// ==============================

document.addEventListener("DOMContentLoaded", function () {
    fetchCategories().then(() => {
        fetchProducts();
    });

    // Event Listeners for Filter Inputs
    const searchInput = document.getElementById("sidebar-search");
    const sortSelect = document.getElementById("sortSelect");
    const applyBtn = document.querySelector(".catalog-sidebar-apply");

    // Search on Enter
    if (searchInput) {
        searchInput.addEventListener("keypress", function (e) {
            if (e.key === "Enter") {
            applyFilters();
            }
        });
    }

    // Sort on Change
    if (sortSelect) {
        sortSelect.addEventListener("change", function () {
            applyFilters();
        });
    }

    // Apply Button Click
    if (applyBtn) {
        applyBtn.addEventListener("click", function () {
            applyFilters();
        });
    }
});

function applyFilters() {
    const params = new URLSearchParams();

    // 1. Search
    const search = document.getElementById("sidebar-search")?.value.trim();
    if (search) params.append("search", search);

    // 2. Sort
    const sort = document.getElementById("sortSelect")?.value;

    // Mapping value select ke param API
    if (sort === "Price: Low to High") params.append("sort", "price_low");
    else if (sort === "Price: High to Low") params.append("sort", "price_high");
    else if (sort === "Newest") params.append("sort", "newest");
    else params.append("sort", "featured");

    const checkedCats = document.querySelectorAll(".catalog-sidebar-list input[type='checkbox']:checked");
    let selectedIds = [];

    checkedCats.forEach(cb => {
        // Value checkbox sudah ID
        if (cb.value) {
            selectedIds.push(cb.value);
        }
    });

    if (selectedIds.length > 0) {
        params.append("category", selectedIds.join(","));
    }

    const priceRadio = document.querySelector("input[name='price']:checked");
    if (priceRadio) {
        const label = priceRadio.parentElement.textContent.trim();
        if (label.includes("< 300K")) {
            params.append("max_price", "300000");
        } else if (label.includes("300K – 500K")) {
            params.append("min_price", "300000");
            params.append("max_price", "500000");
        } else if (label.includes("> 500K")) {
            params.append("min_price", "500000");
        }
    }

    fetchProducts(params);
}

async function fetchProducts(params = null) {
    const grid = document.querySelector(".catalog-grid");
    const topInfo = document.querySelector(".catalog-topbar-info");

    if (!grid) return;

    grid.innerHTML = '<p class="loading-text">Memuat produk...</p>';


    let url = '/api/products/get.php';
    if (params) {
        url += '?' + params.toString();
    }

    try {
        const response = await fetch(url);
        const result = await response.json();
        console.log(url);

        if (result.success && Array.isArray(result.data)) {
            renderProducts(result.data, grid);

            if (topInfo) {
                topInfo.textContent = `Showing ${result.data.length} products`;
            }
        } else {
            grid.innerHTML = '<p class="error-text">Gagal memuat produk.</p>';
        }
    } catch (error) {
        console.error("Error fetching products:", error);
        grid.innerHTML = '<p class="error-text">Terjadi kesalahan saat mengambil data.</p>';
    }
}

async function fetchCategories() {
    try {
        const response = await fetch('/api/categories/get.php?status=1');
        const result = await response.json();

        if (result.success && Array.isArray(result.data)) {
            renderCategories(result.data);
        }
    } catch (error) {
        console.error("Error fetching categories:", error);
    }
}

function renderCategories(categories) {
    const list = document.querySelector(".catalog-sidebar-list");
    if (!list) return;

    while (list.children.length > 1) {
        list.removeChild(list.lastChild);
    }

    categories.forEach(cat => {
        const li = document.createElement("li");
        li.innerHTML = `
            <label>
                <input type="checkbox" value="${cat.id}" />
                ${cat.name}
            </label>
        `;
        list.appendChild(li);
    });
}

function renderProducts(products, container) {
    container.innerHTML = "";

    if (products.length === 0) {
        container.innerHTML = '<p class="empty-text">Belum ada produk.</p>';
        return;
    }

    products.forEach((product) => {
        const imageSrc = product.image ? `assets/images/${product.image}` : 'assets/images/gardenmix.jpg';
        const priceDisplay = formatRupiah(product.price);

        const card = document.createElement("article");
        card.className = "catalog-card";

        card.innerHTML = `
      <div class="catalog-card-image">
        <img src="${imageSrc}" alt="${product.name}" onerror="this.src='assets/images/gardenmix.jpg'">
        ${product.label ? `<span class="catalog-card-badge">${product.label}</span>` : ''}
        <button class="catalog-card-heart" aria-label="Tambah ke favorit">
          <i class="bi bi-heart"></i>
        </button>
      </div>
      <div class="catalog-card-body">
        <h3 class="catalog-card-title">${product.name}</h3>
        <p class="catalog-card-meta">${product.description || ''}</p>
        <div class="catalog-card-footer">
          <span class="catalog-card-price">${priceDisplay}</span>
          <a href="detail_produk.html?id=${product.id}" class="catalog-card-link">View</a>
        </div>
      </div>
    `;

        container.appendChild(card);
    });
}

function formatRupiah(amount) {
    return new Intl.NumberFormat("id-ID", {
        style: "currency",
        currency: "IDR",
        minimumFractionDigits: 0,
    }).format(amount);
}
