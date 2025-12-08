const API_CART_ADD = "/api/cart/add.php";
const API_CART_UPDATE = "/api/cart/update.php";
const API_CART_DELETE = "/api/cart/delete.php";
const API_CART_LIST = "/api/cart/list.php";

function formatRupiah(num) {
  return new Intl.NumberFormat("id-ID", {
    style: "currency",
    currency: "IDR",
    minimumFractionDigits: 0,
  }).format(num);
}

async function updateCartBadge() {
  try {
    const response = await fetch(API_CART_LIST);
    if (!response.ok) return;

    const result = await response.json();
    const badge = document.getElementById("cartCount");

    if (result.success && Array.isArray(result.data)) {
      // Count unique items
      const totalQty = result.data.length;

      if (badge) {
        if (totalQty > 0) {
          badge.textContent = totalQty;
          badge.classList.remove("hidden");
        } else {
          badge.textContent = "0";
          badge.classList.add("hidden");
        }
      }
    }
  } catch (error) {
    console.warn("Failed to update cart badge:", error);
  }
}

async function addToCart(productId, quantity = 1) {
  const formData = new FormData();
  formData.append("product_id", productId);
  formData.append("quantity", quantity);

  try {
    const response = await fetch(API_CART_ADD, {
      method: "POST",
      body: formData,
    });

    const result = await response.json();

    if (result.success) {
      alert("Berhasil masuk keranjang! 🌸");
      updateCartBadge();
    } else {
      if (response.status === 401) {
        if (confirm("Silakan login untuk berbelanja. Ke halaman login?")) {
          window.location.href = "/auth/login.php";
        }
      } else {
        alert(result.message || "Gagal menambahkan ke keranjang.");
      }
    }
  } catch (error) {
    console.error("Cart error:", error);
    alert("Terjadi kesalahan koneksi.");
  }
}

/**
 * Update Cart Item Quantity (Set specific value)
 * @param {number} cartItemId
 * @param {number} quantity
 */
async function updateCartItem(cartItemId, quantity) {
  const formData = new FormData();
  formData.append("id", cartItemId);
  formData.append("quantity", quantity);

  try {
    const response = await fetch(API_CART_UPDATE, {
      method: "POST",
      body: formData,
    });

    const result = await response.json();

    if (result.success) {
      // Valid update, no need to toast usually (silent)
      updateCartBadge();
    } else {
      alert(result.message || "Gagal update keranjang.");
    }
    return result.success;
  } catch (error) {
    console.error("Update error:", error);
    return false;
  }
}

/**
 * Delete Cart Item
 * @param {number} cartItemId
 */
async function deleteCartItem(cartItemId) {
  if (!confirm("Hapus item ini dari keranjang?")) return false;

  const formData = new FormData();
  formData.append("id", cartItemId);

  try {
    const response = await fetch(API_CART_DELETE, {
      method: "POST",
      body: formData,
    });

    const result = await response.json();

    if (result.success) {
      updateCartBadge();
      return true;
    } else {
      alert(result.message || "Gagal menghapus item.");
      return false;
    }
  } catch (error) {
    console.error("Delete error:", error);
    return false;
  }
}

document.addEventListener("DOMContentLoaded", () => {
  updateCartBadge();
});
