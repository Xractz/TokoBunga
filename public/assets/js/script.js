const API_CART_LIST = "/api/cart/list.php";

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
  updateCartBadge();
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

function showError(message, formId = null) {
  const existingAlert = document.querySelector(".alert-message");
  if (existingAlert) {
    existingAlert.remove();
  }

  const alert = document.createElement("div");
  alert.className = "alert-message alert-error";
  alert.innerHTML = `
    <i class="bi bi-exclamation-circle"></i>
    <span>${message}</span>
    <button class="alert-close" onclick="this.parentElement.remove()">
      <i class="bi bi-x"></i>
    </button>
  `;

  const form = formId ? document.getElementById(formId) : document.querySelector("form");
  if (form) {
    form.parentElement.insertBefore(alert, form);
    setTimeout(() => {
      if (alert.parentElement) {
        alert.remove();
      }
    }, 5000);
  }
}

function showSuccess(message, formId = null) {
  const existingAlert = document.querySelector(".alert-message");
  if (existingAlert) {
    existingAlert.remove();
  }

  const alert = document.createElement("div");
  alert.className = "alert-message alert-success";
  alert.innerHTML = `
    <i class="bi bi-check-circle"></i>
    <span>${message}</span>
  `;

  const form = formId ? document.getElementById(formId) : document.querySelector("form");
  if (form) {
    form.parentElement.insertBefore(alert, form);
  }
}

function clearErrors() {
  const existingAlert = document.querySelector(".alert-message");
  if (existingAlert) {
    existingAlert.remove();
  }
}
