let currentPage = 1;
let isLoading = false;
let hasMore = true;
const LIMIT = 8;

document.addEventListener("DOMContentLoaded", function () {
  fetchCategories().then(() => {
    loadCatalog(1, false);
  });

  const searchInput = document.getElementById("sidebar-search");
  const sortSelect = document.getElementById("sortSelect");
  const applyBtn = document.querySelector(".catalog-sidebar-apply");

  if (searchInput) {
    searchInput.addEventListener("keypress", function (e) {
      if (e.key === "Enter") {
        resetAndLoad();
      }
    });
  }

  if (sortSelect) {
    sortSelect.addEventListener("change", function () {
      resetAndLoad();
    });
  }

  if (applyBtn) {
    applyBtn.addEventListener("click", function () {
      resetAndLoad();
    });
  }

  // Infinite Scroll Event
  window.addEventListener("scroll", handleScroll);
});

function handleScroll() {
  if (isLoading || !hasMore) return;

  const scrollPosition = window.innerHeight + window.scrollY;
  const bottomPosition = document.documentElement.offsetHeight - 100; // Buffer 100px before bottom

  if (scrollPosition >= bottomPosition) {
    loadCatalog(currentPage + 1, true);
  }
}

function resetAndLoad() {
  currentPage = 1;
  hasMore = true;
  loadCatalog(1, false);
}

function getFilterParams() {
  const params = new URLSearchParams();

  // 1. Search
  const search = document.getElementById("sidebar-search")?.value.trim();
  if (search) params.append("search", search);

  // 2. Sort
  const sort = document.getElementById("sortSelect")?.value;
  params.append(
    "sort",
    sort === "Price: Low to High"
      ? "price_low"
      : sort === "Price: High to Low"
      ? "price_high"
      : sort === "Newest"
      ? "newest"
      : "featured"
  );

  // 3. Categories
  const checkedCats = document.querySelectorAll(
    ".catalog-sidebar-list input[type='checkbox']:checked"
  );
  const selectedIds = Array.from(checkedCats).map((cb) => cb.value);
  if (selectedIds.length > 0) {
    params.append("category", selectedIds.join(","));
  }

  // 4. Price
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

  return params;
}

async function loadCatalog(page, append = false) {
  if (isLoading) return;
  isLoading = true;
  currentPage = page;

  const grid = document.querySelector(".catalog-grid");
  const topInfo = document.querySelector(".catalog-topbar-info");

  if (!grid) return;

  // Show loading indicator if appending, or initial load placeholder
  if (!append) {
    grid.innerHTML = '<p class="loading-text">Memuat produk...</p>';
  } else {
    // Optional: Add a small loading spinner at bottom if desired
    // For now we rely on the user just waiting a moment or seeing the scrollbar stop
    // Or we can append a loader element
    const loader = document.createElement("div");
    loader.id = "infinite-loader";
    loader.className = "col-span-full text-center py-4";
    loader.innerHTML = '<p class="loading-text">Memuat lebih banyak...</p>';
    grid.appendChild(loader);
  }

  const params = getFilterParams();
  params.append("page", page);
  params.append("limit", LIMIT);

  try {
    const response = await fetch("/api/products/get.php?" + params.toString());
    const result = await response.json();

    // Remove temp loader if exists
    const tempLoader = document.getElementById("infinite-loader");
    if (tempLoader) tempLoader.remove();

    if (result.success && result.data) {
      const products = result.data.products || [];
      const pagination = result.data.pagination;

      // Update state
      hasMore = pagination.current_page < pagination.total_pages;

      if (!append) {
        // Replace content
        renderProducts(products, grid, false);
      } else {
        // Append content
        renderProducts(products, grid, true);
      }

      if (topInfo && pagination) {
        // Calculate total showing
        const showing = Math.min(
          pagination.total_items,
          pagination.current_page * pagination.limit
        );
        topInfo.textContent = `Showing ${showing} of ${pagination.total_items} products`;
      }
    } else {
      if (!append) {
        grid.innerHTML = '<p class="error-text">Gagal memuat produk.</p>';
      }
    }
  } catch (error) {
    console.error("Error fetching products:", error);
    if (!append) {
      grid.innerHTML =
        '<p class="error-text">Terjadi kesalahan saat mengambil data.</p>';
    }
  } finally {
    isLoading = false;
  }
}

async function fetchCategories() {
  try {
    const response = await fetch("/api/categories/get.php?status=1");
    const result = await response.json();

    if (
      result.success &&
      result.data &&
      Array.isArray(result.data.categories)
    ) {
      renderCategories(result.data.categories);
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

  categories.forEach((cat) => {
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

function renderProducts(products, container, append) {
  if (!append) {
    container.innerHTML = "";
  }

  if (!append && products.length === 0) {
    container.innerHTML = '<p class="empty-text">Belum ada produk.</p>';
    return;
  }

  products.forEach((product) => {
    const imageSrc = product.image
      ? `assets/images/products/${product.image}`
      : "assets/images/products/gardenmix.jpg";
    const priceDisplay = formatRupiah(product.price);

    const card = document.createElement("article");
    card.className = "catalog-card";

    card.innerHTML = `
      <div class="catalog-card-image">
        <img src="${imageSrc}" alt="${
      product.name
    }" onerror="this.src='assets/images/products/gardenmix.jpg'">
        ${
          product.label
            ? `<span class="catalog-card-badge">${product.label}</span>`
            : ""
        }
      </div>
      <div class="catalog-card-body">
        <h3 class="catalog-card-title">${product.name}</h3>
        <p class="catalog-card-meta">${product.description || ""}</p>
        <div class="catalog-card-footer">
          <span class="catalog-card-price">${priceDisplay}</span>
          <a href="detail_produk.php?slug=${
            product.slug
          }" class="catalog-card-link">View</a>
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
