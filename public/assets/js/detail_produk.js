let currentProductId = null;
window.changeQty = function (delta) {
  const input = document.getElementById("qtyInput");
  let val = parseInt(input.value || "1", 10);
  val += delta;
  if (val < 1) val = 1;
  input.value = val;
};

document.addEventListener("DOMContentLoaded", async function () {
  const params = new URLSearchParams(window.location.search);
  const slug = params.get("slug");

  const elName = document.getElementById("detail-name");
  const elDesc = document.getElementById("detail-description");
  const elCat = document.getElementById("detail-category");
  const elStock = document.getElementById("detail-stock");
  const elPrice = document.getElementById("detail-price");
  const elImgMain = document.querySelector(".product-detail-image-main img");
  const elBreadcrumb = document.querySelector(".breadcrumb span:last-child");
  const btnAddToCart = document.getElementById("addToCartBtn");

  if (!slug) {
    alert("Produk tidak ditemukan (no slug).");
    window.location.href = "katalog.php";
    return;
  }

  try {
    const response = await fetch(`/api/products/get.php?slug=${slug}`);
    const result = await response.json();

    if (result.success && result.data) {
      const p = result.data;
      currentProductId = p.id;

      if (elName) elName.textContent = p.name;
      if (elDesc) elDesc.textContent = p.description;
      if (elCat) elCat.textContent = p.label || "Fresh Flower";
      if (elStock) elStock.textContent = p.stock > 0 ? "Tersedia" : "Habis";
      if (elPrice) elPrice.textContent = formatRupiah(p.price);

      if (elImgMain) {
        elImgMain.src = p.image
          ? `assets/images/${p.image}`
          : "assets/images/gardenmix.jpg";
        elImgMain.alt = p.name;
        elImgMain.onerror = function () {
          this.src = "assets/images/gardenmix.jpg";
        };
      }

      if (elBreadcrumb) elBreadcrumb.textContent = p.name;

      document.title = `${p.name} – Bloomify`;

      // Mencari rekomendasi berdasarkan kategori
      if (p.category_id) {
        loadRecommendations(p.category_id, p.id);
      } else {
        // Jika tidak ada kategori, mungkin clear the recommendation section
        document.querySelector(".detail-recommend").style.display = "none";
      }
    } else {
      alert("Produk tidak ditemukan.");
      window.location.href = "katalog.php";
    }
  } catch (error) {
    console.error("Fetch error:", error);
    alert("Gagal memuat detail produk.");
  }

  async function loadRecommendations(categoryId, currentId) {
    try {
      const response = await fetch(
        `/api/products/get.php?category=${categoryId}`
      );
      const result = await response.json();

      if (result.success && Array.isArray(result.data)) {
        const recommendations = result.data
          .filter((item) => item.id !== currentId)
          .slice(0, 3); // mengambil top 3

        const container = document.querySelector(".detail-recommend-grid");
        if (!container) return;

        if (recommendations.length === 0) {
          document.querySelector(".detail-recommend").style.display = "none";
          return;
        }

        container.innerHTML = "";

        recommendations.forEach((item) => {
          const card = document.createElement("a");
          card.href = `detail_produk.php?slug=${item.slug}`;
          card.className = "detail-recommend-card";

          const imgPath = item.image
            ? `assets/images/${item.image}`
            : "assets/images/gardenmix.jpg";

          card.innerHTML = `
            <img src="${imgPath}" alt="${
            item.name
          }" onerror="this.src='assets/images/gardenmix.jpg'"/>
            <div class="detail-recommend-body">
              <h4>${item.name}</h4>
              <p>${item.label || "Fresh Flower"}</p>
              <span>${formatRupiah(item.price)}</span>
            </div>
          `;

          container.appendChild(card);
        });
      }
    } catch (err) {
      console.error("Error loading recommendations:", err);
    }
  }

  if (btnAddToCart) {
    btnAddToCart.addEventListener("click", function () {
      if (!currentProductId) {
        return;
      }

      const qtyInput = document.getElementById("qtyInput");
      const qty = parseInt(qtyInput ? qtyInput.value : "1", 10);

      addToCart(currentProductId, qty);
    });
  }
});
