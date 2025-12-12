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

    if (
      result.success &&
      result.data &&
      result.data.products &&
      result.data.products.length > 0
    ) {
      const p = result.data.products[0];
      currentProductId = p.id;

      if (elName) elName.textContent = p.name;
      if (elDesc) elDesc.textContent = p.description;
      if (elCat) elCat.textContent = p.category_name || "Fresh Flower"; // Updated field
      if (elStock) elStock.textContent = p.stock > 0 ? "Tersedia" : "Habis";

      if (elPrice) {
        elPrice.textContent = new Intl.NumberFormat("id-ID", {
          style: "currency",
          currency: "IDR",
          minimumFractionDigits: 0,
        }).format(p.price);
      }

      if (elImgMain) {
        elImgMain.src = p.image
          ? `assets/images/products/${p.image}`
          : "assets/images/products/gardenmix.jpg";
        elImgMain.alt = p.name;
        elImgMain.onerror = function () {
          this.src = "assets/images/products/gardenmix.jpg";
        };
      }

      if (elBreadcrumb) elBreadcrumb.textContent = p.name;

      document.title = `${p.name} – Bloomify`;

      // Mencari rekomendasi berdasarkan kategori
      if (p.category_id) {
        loadRecommendations(p.category_id, p.id);
      } else {
        // Jika tidak ada kategori, sembunyikan section
        const recSection = document.querySelector(".detail-recommend");
        if (recSection) recSection.style.display = "none";
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
      // Limit 4 to increase chance of getting 3 distinct items (excluding self)
      const response = await fetch(
        `/api/products/get.php?category=${categoryId}&limit=4`
      );
      const result = await response.json();

      let products = [];
      if (
        result.success &&
        result.data &&
        Array.isArray(result.data.products)
      ) {
        products = result.data.products;
      }

      const recommendations = products
        .filter((item) => item.id != currentId) // Loose equality just in case
        .slice(0, 3);

      const container = document.querySelector(".detail-recommend-grid");
      const section = document.querySelector(".detail-recommend");

      if (!container || !section) return;

      if (recommendations.length === 0) {
        section.style.display = "none";
        return;
      }

      // Ensure section is visible if we have data
      section.style.display = "block";
      container.innerHTML = "";

      recommendations.forEach((item) => {
        const card = document.createElement("a");
        card.href = `detail_produk.php?slug=${item.slug}`;
        card.className = "detail-recommend-card";

        const imgPath = item.image
          ? `assets/images/products/${item.image}`
          : "assets/images/products/gardenmix.jpg";

        const price = new Intl.NumberFormat("id-ID", {
          style: "currency",
          currency: "IDR",
          minimumFractionDigits: 0,
        }).format(item.price);

        card.innerHTML = `
            <img src="${imgPath}" alt="${
          item.name
        }" onerror="this.src='assets/images/products/gardenmix.jpg'"/>
            <div class="detail-recommend-body">
              <h4>${item.name}</h4>
              <p>${item.category_name || "Fresh Flower"}</p>
              <span>${price}</span>
            </div>
          `;

        container.appendChild(card);
      });
    } catch (err) {
      console.error("Error loading recommendations:", err);
      // Simplify error handling: hide section
      const section = document.querySelector(".detail-recommend");
      if (section) section.style.display = "none";
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
