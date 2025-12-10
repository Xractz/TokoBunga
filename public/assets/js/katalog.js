let currentPage = 1;

document.addEventListener("DOMContentLoaded", function () {
  fetchCategories().then(() => {
    loadCatalog(1);
  });

  const searchInput = document.getElementById("sidebar-search");
  const sortSelect = document.getElementById("sortSelect");
  const applyBtn = document.querySelector(".catalog-sidebar-apply");

  if (searchInput) {
    searchInput.addEventListener("keypress", function (e) {
      if (e.key === "Enter") {
        loadCatalog(1);
      }
    });
  }

  if (sortSelect) {
    sortSelect.addEventListener("change", function () {
      loadCatalog(1);
    });
  }

  if (applyBtn) {
    applyBtn.addEventListener("click", function () {
      loadCatalog(1);
    });
  }
});

function getFilterParams() {
  const params = new URLSearchParams();

  // 1. Search
  const search = document.getElementById("sidebar-search")?.value.trim();
  if (search) params.append("search", search);

  // 2. Sort
  const sort = document.getElementById("sortSelect")?.value;
  if (sort === "Price: Low to High") params.append("sort", "price_low");
  else if (sort === "Price: High to Low") params.append("sort", "price_high");
  else if (sort === "Newest") params.append("sort", "newest");
  else params.append("sort", "featured");

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

async function loadCatalog(page) {
  currentPage = page;
  const grid = document.querySelector(".catalog-grid");
  const topInfo = document.querySelector(".catalog-topbar-info");

  if (!grid) return;

  grid.innerHTML = '<p class="loading-text">Memuat produk...</p>';

  const params = getFilterParams();
  params.append("page", page);
  params.append("limit", 10);

  try {
    const response = await fetch("/api/products/get.php?" + params.toString());
    const result = await response.json();

    if (result.success && result.data) {
      const products = result.data.products || [];
      const pagination = result.data.pagination;

      renderProducts(products, grid);
      renderPagination(pagination);

      if (topInfo && pagination) {
        topInfo.textContent = `Showing ${products.length} of ${pagination.total_items} products`;
      }
    } else {
      grid.innerHTML = '<p class="error-text">Gagal memuat produk.</p>';
    }
  } catch (error) {
    console.error("Error fetching products:", error);
    grid.innerHTML =
      '<p class="error-text">Terjadi kesalahan saat mengambil data.</p>';
  }
}

function renderPagination(meta) {
  const container = document.getElementById("catalog-pagination");
  if (!container) return;
  container.innerHTML = "";

  if (!meta || meta.total_pages <= 1) return;

  // Prev
  const prevLi = document.createElement("li");
  prevLi.className = `page-item ${meta.current_page === 1 ? "disabled" : ""}`;
  prevLi.innerHTML = `<button class="page-link">Previous</button>`;
  if (meta.current_page > 1) {
    prevLi.querySelector("button").onclick = () =>
      loadCatalog(meta.current_page - 1);
  }
  container.appendChild(prevLi);

  // Numbers
  for (let i = 1; i <= meta.total_pages; i++) {
    const li = document.createElement("li");
    li.className = `page-item ${i === meta.current_page ? "active" : ""}`;
    li.innerHTML = `<button class="page-link">${i}</button>`;
    li.querySelector("button").onclick = () => loadCatalog(i);
    container.appendChild(li);
  }

  // Next
  const nextLi = document.createElement("li");
  nextLi.className = `page-item ${
    meta.current_page === meta.total_pages ? "disabled" : ""
  }`;
  nextLi.innerHTML = `<button class="page-link">Next</button>`;
  if (meta.current_page < meta.total_pages) {
    nextLi.querySelector("button").onclick = () =>
      loadCatalog(meta.current_page + 1);
  }
  container.appendChild(nextLi);
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

function renderProducts(products, container) {
  container.innerHTML = "";

  if (products.length === 0) {
    container.innerHTML = '<p class="empty-text">Belum ada produk.</p>';
    return;
  }

  products.forEach((product) => {
    console.log(product);
    const imageSrc = product.image
      ? `assets/images/products/${product.image}`
      : "assets/images/gardenmix.jpg";
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
