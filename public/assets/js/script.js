/* ================================
   TOGGLE PASSWORD (SHOW / HIDE)
================================ */
function togglePassword(inputId, el) {
  const input = document.getElementById(inputId);
  const icon = el.querySelector("i");

  if (!input || !icon) return;

  const isHidden = input.type === "password";

  input.type = isHidden ? "text" : "password";

  // Ganti icon
  icon.classList.toggle("bi-eye", isHidden);
  icon.classList.toggle("bi-eye-slash", !isHidden);
}

/* ================================
   HAMBURGER MENU DROPDOWN
================================ */
document.addEventListener("DOMContentLoaded", function () {
  const hamburger = document.getElementById("hamburgerBtn");
  const mobileMenu = document.getElementById("mobileMenu");

  if (!hamburger || !mobileMenu) return;

  hamburger.addEventListener("click", function (e) {
    e.stopPropagation(); // biar klik di tombol nggak ikut dianggap klik "di luar"
    mobileMenu.classList.toggle("open");
    hamburger.classList.toggle("open");

    const icon = hamburger.querySelector("i");
    if (hamburger.classList.contains("open")) {
      icon.classList.remove("bi-list");
      icon.classList.add("bi-x");
    } else {
      icon.classList.remove("bi-x");
      icon.classList.add("bi-list");
    }
  });

  // Tutup kalau klik di luar dropdown
  document.addEventListener("click", function (e) {
    if (!mobileMenu.contains(e.target) && !hamburger.contains(e.target)) {
      mobileMenu.classList.remove("open");
      hamburger.classList.remove("open");

      const icon = hamburger.querySelector("i");
      if (icon) {
        icon.classList.remove("bi-x");
        icon.classList.add("bi-list");
      }
    }
  });
});

// =========================
// CART BADGE + LOCALSTORAGE
// =========================

// ambil jumlah dari localStorage
function getCartCount() {
  return parseInt(localStorage.getItem("cartCount") || "0", 10);
}

// simpan jumlah baru ke localStorage
function setCartCount(count) {
  const maxItems = 5; // batas maksimal sementara
  const safeCount = Math.max(0, Math.min(count, maxItems));

  localStorage.setItem("cartCount", String(safeCount));
  updateCartBadge(safeCount);
}

// update tampilan badge di header
function updateCartBadge(count) {
  const badge = document.getElementById("cartCount");
  if (!badge) return;

  if (count <= 0) {
    badge.textContent = "0";
    badge.classList.add("hidden"); // sembunyikan kalau 0
  } else {
    badge.textContent = count;
    badge.classList.remove("hidden");
  }
}

// inisialisasi saat halaman load
document.addEventListener("DOMContentLoaded", function () {
  // tampilkan jumlah cart dari localStorage di semua halaman
  updateCartBadge(getCartCount());

  // tombol add to cart (hanya ada di halaman detail produk)
  const addBtn = document.getElementById("addToCartBtn");
  if (addBtn) {
    addBtn.addEventListener("click", function () {
      const qtyInput = document.getElementById("qtyInput");
      let qty = parseInt(qtyInput ? qtyInput.value : "1", 10);

      if (isNaN(qty) || qty < 1) qty = 1;

      // jumlah baru
      let newCount = getCartCount() + qty;

      // batasi maksimal 5
      if (newCount > 5) newCount = 5;

      setCartCount(newCount);
    });
  }
});

function updateCartCountDisplay() {
  const count = parseInt(localStorage.getItem("cartCount") || "0");
  const badge = document.getElementById("cartCount");

  if (badge) {
    if (count > 0) {
      badge.textContent = count;
      badge.classList.remove("hidden");
    } else {
      badge.classList.add("hidden");
    }
  }
}
updateCartCountDisplay();

// ==============================
// CART PAGE LOGIC (BASED ON cartCount)
// ==============================
document.addEventListener("DOMContentLoaded", function () {
  const itemsContainer = document.getElementById("cartItemsContainer");
  const itemCountText = document.getElementById("cartItemCount");
  const subtotalEl = document.getElementById("cartSubtotal");
  const shippingEl = document.getElementById("cartShipping");
  const totalEl = document.getElementById("cartTotal");

  // kalau bukan di cart.html, keluar
  if (!itemsContainer) return;

  const UNIT_PRICE = 450000;
  const SHIPPING_FEE = 25000;
  const MAX_ITEMS = 5;

  function formatRupiah(num) {
    return "Rp " + num.toLocaleString("id-ID");
  }

  // bangun ulang semua card berdasarkan cartCount
  function buildCartCards() {
    let count = getCartCount();

    // clamp 0..MAX_ITEMS
    if (count < 0) count = 0;
    if (count > MAX_ITEMS) count = MAX_ITEMS;
    setCartCount(count); // sync ke localStorage + badge

    // update teks "X item(s) in your cart"
    if (itemCountText) {
      itemCountText.textContent = count;
    }

    // kosongkan container dulu
    itemsContainer.innerHTML = "";

    // kalau kosong → tampilkan pesan saja
    if (count === 0) {
      const empty = document.createElement("p");
      empty.className = "cart-empty";
      empty.textContent =
        "Keranjangmu masih kosong. Yuk pilih buket dulu di katalog 🌸";
      itemsContainer.appendChild(empty);

      if (subtotalEl) subtotalEl.textContent = formatRupiah(0);
      if (shippingEl) shippingEl.textContent = formatRupiah(0);
      if (totalEl) totalEl.textContent = formatRupiah(0);
      return;
    }

    // kalau ada item → buat card sejumlah count
    for (let i = 0; i < count; i++) {
      const card = document.createElement("article");
      card.className = "cart-item-card";
      card.dataset.unitPrice = String(UNIT_PRICE); // simpan harga per buket

      card.innerHTML = `
        <div class="cart-item-image">
          <img src="assets/images/gardenmix.jpg" alt="Garden Rose Bouquet">
        </div>

        <div class="cart-item-info">
          <p class="cart-item-type">BOUQUET</p>
          <h2 class="cart-item-name">Garden Rose Bouquet</h2>
        </div>

        <div class="cart-item-qty-price">
          <div class="cart-item-qty">
            <button type="button" class="cart-qty-minus">−</button>
            <input type="text" class="cart-qty-value" value="1" readonly>
            <button type="button" class="cart-qty-plus">+</button>
          </div>

          <div class="cart-item-price">
            <span class="cart-item-price-main">
              ${formatRupiah(UNIT_PRICE)}
            </span>
            <span class="cart-item-price-note">
              ${formatRupiah(UNIT_PRICE)} / bouquet
            </span>
          </div>

          <button type="button" class="cart-item-remove">
            <i class="bi bi-trash3"></i>
          </button>
        </div>
      `;

      itemsContainer.appendChild(card);
    }

    attachCardEvents();
    updateSummary();
  }

  // hitung subtotal & total berdasarkan qty di tiap card
  function updateSummary() {
    let subtotal = 0;
    const cards = itemsContainer.querySelectorAll(".cart-item-card");

    cards.forEach((card) => {
      const unitPrice = parseInt(card.dataset.unitPrice || "0", 10);
      const qtyInput = card.querySelector(".cart-qty-value");
      const qty = qtyInput ? parseInt(qtyInput.value || "1", 10) : 1;
      subtotal += unitPrice * qty;
    });

    const hasItems = cards.length > 0;
    const shipping = hasItems ? SHIPPING_FEE : 0;
    const total = subtotal + shipping;

    if (subtotalEl) subtotalEl.textContent = formatRupiah(subtotal);
    if (shippingEl) shippingEl.textContent = formatRupiah(shipping);
    if (totalEl) totalEl.textContent = formatRupiah(total);
  }

  // pasang event +/− dan remove untuk semua card yang ada
  function attachCardEvents() {
    const cards = itemsContainer.querySelectorAll(".cart-item-card");

    cards.forEach((card) => {
      const minusBtn = card.querySelector(".cart-qty-minus");
      const plusBtn = card.querySelector(".cart-qty-plus");
      const qtyInput = card.querySelector(".cart-qty-value");
      const removeBtn = card.querySelector(".cart-item-remove");

      if (!qtyInput) return;

      let qty = parseInt(qtyInput.value || "1", 10);

      // minus (minimal 1)
      if (minusBtn) {
        minusBtn.addEventListener("click", function () {
          if (qty > 1) {
            qty--;
            qtyInput.value = qty;
            updateSummary();
          }
        });
      }

      // plus
      if (plusBtn) {
        plusBtn.addEventListener("click", function () {
          qty++;
          qtyInput.value = qty;
          updateSummary();
        });
      }

      // remove card
      if (removeBtn) {
        removeBtn.addEventListener("click", function () {
          card.remove();

          // kurangi global count, jangan sampai minus
          const current = getCartCount();
          const next = current > 0 ? current - 1 : 0;
          setCartCount(next);

          // rebuild tampilan sesuai count baru
          buildCartCards();
        });
      }
    });
  }

  // pertama kali halaman cart dibuka
  buildCartCards();
});
