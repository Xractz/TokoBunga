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
    } else {
      alert("Produk tidak ditemukan.");
      window.location.href = "katalog.php";
    }
  } catch (error) {
    console.error("Fetch error:", error);
    alert("Gagal memuat detail produk.");
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
