/**
 * cart.js
 * Logic specific to the Shopping Cart Page
 */

document.addEventListener("DOMContentLoaded", function () {
  const itemsContainer = document.getElementById("cartItemsContainer");
  const subtotalEl = document.getElementById("cartSubtotal");
  const shippingEl = document.getElementById("cartShipping");
  const totalEl = document.getElementById("cartTotal");
  const itemCountEl = document.getElementById("cartItemCount");

  if (!itemsContainer) return;

  const SHIPPING_FEE = 25000;

  /**
   * Render Cart Items
   * @param {Array} items
   */
  function renderCart(items) {
    itemsContainer.innerHTML = "";

    let subtotal = 0;
    let totalCount = 0;

    if (items.length === 0) {
      itemsContainer.innerHTML = `
                <p class="cart-empty">
                    Keranjangmu masih kosong. Yuk pilih buket dulu di katalog 🌸
                </p>
            `;
      updateSummary(0, 0);
      return;
    }

    items.forEach((item) => {
      const qty = parseInt(item.quantity);
      const price = parseInt(item.price);
      const lineTotal = qty * price;

      subtotal += lineTotal;
      totalCount += 1;

      const card = document.createElement("article");
      card.className = "cart-item-card";

      card.innerHTML = `
                <div class="cart-item-image" style="cursor: pointer;">
                    <img src="assets/images/products/${
                      item.image || "gardenmix.jpg"
                    }" alt="${item.name}" 
                         onerror="this.src='assets/images/products/gardenmix.jpg'">
                </div>

                <div class="cart-item-info">
                    <p class="cart-item-type">BOUQUET</p>
                    <h2 class="cart-item-name" style="cursor: pointer;">${
                      item.name
                    }</h2>

                    <div class="cart-item-qty">
                        <button type="button" class="cart-qty-minus" data-id="${
                          item.id
                        }" data-qty="${qty}">−</button>
                        <input type="text" class="cart-qty-value" value="${qty}" readonly>
                        <button type="button" class="cart-qty-plus" data-id="${
                          item.id
                        }" data-qty="${qty}">+</button>
                    </div>
                </div>

                <div class="cart-item-qty-price">
                    <div class="cart-item-price">
                        <span class="cart-item-price-main">
                            ${formatRupiah(lineTotal)}
                        </span>
                        <span class="cart-item-price-note">
                            ${formatRupiah(price)} / bouquet
                        </span>
                    </div>
                </div>
            `;

      card.addEventListener("click", (e) => {
        if (
          e.target.closest(".cart-item-image") ||
          e.target.closest(".cart-item-name")
        ) {
          window.location.href = `detail_produk.php?slug=${item.slug}`;
        }
      });

      itemsContainer.appendChild(card);
    });

    if (itemCountEl) itemCountEl.textContent = totalCount;

    updateSummary(subtotal, SHIPPING_FEE);

    setupCartEvents();
  }

  function updateSummary(subtotal, shipping) {
    if (subtotal === 0) shipping = 0;

    if (subtotalEl) subtotalEl.textContent = formatRupiah(subtotal);
    if (shippingEl) shippingEl.textContent = formatRupiah(shipping);
    if (totalEl) totalEl.textContent = formatRupiah(subtotal + shipping);
  }

  /**
   * Fetch Cart Data
   */
  async function loadCart() {
    try {
      const response = await fetch(
        `${API_CART_LIST}?t=${new Date().getTime()}`
      );
      const result = await response.json();

      if (result.success) {
        renderCart(result.data || []);
      } else {
        console.warn(result.message);
        if (result.message.includes("login")) {
          itemsContainer.innerHTML = `<p class="cart-empty">Silakan <a href="auth/login.php">login</a> untuk melihat keranjang.</p>`;
        }
      }
    } catch (error) {
      console.error("Error loading cart:", error);
    }
  }

  function setupCartEvents() {
    document.querySelectorAll(".cart-qty-plus").forEach((btn) => {
      btn.addEventListener("click", () => {
        const pid = btn.dataset.id;
        const newQty = parseInt(btn.dataset.qty) + 1;
        handleUpdate(pid, newQty);
      });
    });

    document.querySelectorAll(".cart-qty-minus").forEach((btn) => {
      btn.addEventListener("click", () => {
        const pid = btn.dataset.id;
        const currentQty = parseInt(btn.dataset.qty);
        if (currentQty > 1) {
          handleUpdate(pid, currentQty - 1);
        } else {
          handleDelete(pid);
        }
      });
    });
  }

  async function handleUpdate(cartId, quantity) {
    if (quantity < 1) return;
    await updateCartItem(cartId, quantity);
    loadCart();
  }

  async function handleDelete(cartId) {
    const success = await deleteCartItem(cartId);
    if (success) loadCart();
  }

  loadCart();
});
