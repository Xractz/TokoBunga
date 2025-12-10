document.addEventListener("DOMContentLoaded", function () {
  loadBestSeller();
});

async function loadBestSeller() {
  const container = document.querySelector(".products-grid");
  if (!container) return;

  container.innerHTML =
    '<p style="text-align:center; col-span:4; width:100%;">Loading...</p>';

  try {
    const response = await fetch("api/products/terlaris.php");
    const result = await response.json();

    if (result.success && result.data.length > 0) {
      renderProducts(result.data, container);
    } else {
      container.innerHTML = "<p>Belum ada produk untuk ditampilkan.</p>";
    }
  } catch (error) {
    console.error("Error loading best seller:", error);
    container.innerHTML = "<p>Gagal memuat produk.</p>";
  }
}

function renderProducts(products, container) {
  container.innerHTML = "";

  products.forEach((prod) => {
    const priceFormatted = new Intl.NumberFormat("id-ID").format(prod.price);

    const card = document.createElement("article");
    card.className = "product-card";
    const imgSrc = prod.image
      ? `assets/images/products/${prod.image}`
      : "assets/images/gardenmix.jpg";

    card.innerHTML = `
        <div class="product-image">
            <span class="product-badge">Best Seller</span>
            <img src="${imgSrc}" alt="${prod.name}" onerror="this.src='assets/images/products/gardenmix.jpg'" />
        </div>
        <h3 class="product-title">${prod.name}</h3>
        <p class="price">Rp ${priceFormatted}</p>
        <a href="detail_produk.php?slug=${prod.slug}" class="stretched-link"></a>
    `;

    container.appendChild(card);
  });
}
