/**
 * checkout.js
 * Logic for Checkout Page
 */

const API_ORDER_CREATE = "/api/orders/create.php";

let map;
let marker;
let cartItems = [];
let cartSubtotal = 0;
const SHIPPING_FEE = 25000; // Fixed shipping for now

document.addEventListener("DOMContentLoaded", function () {
  checkCartAccess();
  initMap();
  setupForm();
});

// 1. Check Cart Access & Load Summary
async function checkCartAccess() {
  try {
    const response = await fetch(`${API_CART_LIST}?t=${new Date().getTime()}`);
    const result = await response.json();

    if (!result.success || !result.data || result.data.length === 0) {
      alert("Keranjang Anda kosong! Silakan belanja terlebih dahulu.");
      window.location.href = "katalog.php";
      return;
    }

    cartItems = result.data;
    renderOrderSummary(cartItems);
  } catch (error) {
    console.error("Error checking cart:", error);
    // On error, stay or redirect? safety: redirect
    window.location.href = "katalog.php";
  }
}

function renderOrderSummary(items) {
  const container = document.getElementById("checkoutSummaryItems");
  const subtotalEl = document.getElementById("checkoutSubtotal");
  const shippingEl = document.getElementById("checkoutShipping");
  const totalEl = document.getElementById("checkoutTotal");
  const itemCountEl = document.getElementById("checkoutItemCount");
  // Note: checkoutItemCount might act as total items count

  if (!container) return;

  container.innerHTML = "";
  cartSubtotal = 0;

  items.forEach((item) => {
    const lineTotal = parseInt(item.price) * parseInt(item.quantity);
    cartSubtotal += lineTotal;

    const div = document.createElement("div");
    div.className = "checkout-summary-item";
    div.innerHTML = `
        <div class="checkout-summary-thumb">
            <img src="assets/images/${item.image || "gardenmix.jpg"}" alt="${
      item.name
    }" onerror="this.src='assets/images/products/gardenmix.jpg'">
        </div>
        <div class="checkout-summary-info">
            <p class="checkout-summary-name">${item.name}</p>
            <p class="checkout-summary-meta">${item.quantity} x ${formatRupiah(
      item.price
    )}</p>
        </div>
        <div class="checkout-summary-price">
            ${formatRupiah(lineTotal)}
        </div>
    `;
    container.appendChild(div);
  });

  // Calculate totals
  const grandTotal = cartSubtotal + SHIPPING_FEE;

  // Update DOM
  if (subtotalEl) subtotalEl.textContent = formatRupiah(cartSubtotal);
  if (shippingEl) shippingEl.textContent = formatRupiah(SHIPPING_FEE);
  if (totalEl) totalEl.textContent = formatRupiah(grandTotal);
  if (itemCountEl) itemCountEl.textContent = items.length; // Count unique items or total qty

  // Update Hidden Inputs
  document.getElementById("inputSubtotal").value = cartSubtotal;
  document.getElementById("inputShipping").value = SHIPPING_FEE;
  document.getElementById("inputGrandTotal").value = grandTotal;
}

// 2. Map Initialization (Leaflet)
function initMap() {
  // Default: Yogyakarta
  const defaultLat = -7.788563203049172;
  const defaultLng = 110.36921160082893;

  map = L.map("map").setView([defaultLat, defaultLng], 13);

  L.tileLayer("https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png", {
    maxZoom: 19,
    attribution: "© OpenStreetMap",
  }).addTo(map);

  // Add click listener
  map.on("click", function (e) {
    updateMarker(e.latlng.lat, e.latlng.lng);
  });

  // Try to get user location
  if (navigator.geolocation) {
    navigator.geolocation.getCurrentPosition(
      (position) => {
        const lat = position.coords.latitude;
        const lng = position.coords.longitude;
        map.setView([lat, lng], 15);
        updateMarker(lat, lng);
      },
      (error) => {
        console.warn("Geolocation access denied or failed:", error.message);
        updateMarker(defaultLat, defaultLng);
      },
      {
        enableHighAccuracy: true,
        timeout: 5000,
        maximumAge: 0,
      }
    );
  } else {
    updateMarker(defaultLat, defaultLng);
  }
}

function updateMarker(lat, lng) {
  if (marker) {
    marker.setLatLng([lat, lng]);
  } else {
    marker = L.marker([lat, lng]).addTo(map);
  }

  document.getElementById("inputLat").value = lat;
  document.getElementById("inputLng").value = lng;
}

// 3. Address Auto-Update Map
function setupAddressAutoUpdate() {
  const inputs = ["city", "province", "postal"];
  const debouncedSearch = debounce(searchLocation, 1000);

  inputs.forEach((id) => {
    const el = document.getElementById(id);
    if (el) {
      el.addEventListener("input", debouncedSearch);
    }
  });
}

function debounce(func, wait) {
  let timeout;
  return function () {
    const context = this;
    const args = arguments;
    clearTimeout(timeout);
    timeout = setTimeout(() => func.apply(context, args), wait);
  };
}

async function searchLocation() {
  const city = document.getElementById("city").value.trim();
  const province = document.getElementById("province").value.trim();
  const postal = document.getElementById("postal").value.trim();

  // Need at least city or province to search effectively
  if (!city && !province) return;

  const query = `${city}, ${province}, ${postal}, Indonesia`.replace(
    /^, |, $/g,
    ""
  );

  try {
    const response = await fetch(
      `https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(
        query
      )}`
    );
    const data = await response.json();

    if (data && data.length > 0) {
      const lat = parseFloat(data[0].lat);
      const lng = parseFloat(data[0].lon);

      map.setView([lat, lng], 15);
      updateMarker(lat, lng);
      console.log(`Map updated to: ${query}`);
    }
  } catch (error) {
    console.warn("Geocoding failed:", error);
  }
}

// 4. Form Submission
function setupForm() {
  const btn = document.getElementById("btnPlaceOrder");
  if (!btn) return;

  // Initialize address listeners
  setupAddressAutoUpdate();

  // Initialize Date/Time defaults and validation
  setupDateTime();

  btn.addEventListener("click", submitOrder);
}

function setupDateTime() {
  const dateInput = document.getElementById("deliveryDate");
  const timeInput = document.getElementById("deliveryTime");

  if (!dateInput || !timeInput) return;

  const now = new Date();

  // Format Date: YYYY-MM-DD
  const year = now.getFullYear();
  const month = String(now.getMonth() + 1).padStart(2, "0");
  const day = String(now.getDate()).padStart(2, "0");
  const todayStr = `${year}-${month}-${day}`;

  // Set min date to today
  dateInput.min = todayStr;

  // Set default value to today if empty
  if (!dateInput.value) {
    dateInput.value = todayStr;
  }

  // Format Time: HH:MM
  const hours = String(now.getHours()).padStart(2, "0");
  const minutes = String(now.getMinutes()).padStart(2, "0");
  const timeStr = `${hours}:${minutes}`;

  // Set default time to now if empty
  if (!timeInput.value) {
    timeInput.value = timeStr;
  }

  // Live Validation
  function validateDateTime() {
    const selectedDate = dateInput.value;
    const selectedTime = timeInput.value;

    if (!selectedDate || !selectedTime) return;

    const selectedDateTime = new Date(`${selectedDate}T${selectedTime}`);
    const currentDateTime = new Date();

    dateInput.setCustomValidity("");
    timeInput.setCustomValidity("");

    if (selectedDate === todayStr) {
      if (selectedDateTime < currentDateTime) {
        timeInput.setCustomValidity(
          "Waktu pengiriman tidak boleh kurang dari waktu sekarang."
        );
        timeInput.reportValidity();
      }
    } else if (selectedDate < todayStr) {
      dateInput.setCustomValidity("Tanggal tidak boleh kurang dari hari ini.");
      dateInput.reportValidity();
    }
  }

  dateInput.addEventListener("input", validateDateTime);
  timeInput.addEventListener("input", validateDateTime);
}

async function submitOrder() {
  const items = cartItems;
  if (items.length === 0) {
    alert("Keranjang tidak boleh kosong.");
    return;
  }

  const recipientName = document.getElementById("fullName").value.trim();

  const recipientPhone = document.getElementById("phone").value.trim();
  const address = document.getElementById("address").value.trim();

  const city = document.getElementById("city").value.trim();
  const province = document.getElementById("province").value.trim();
  const postal = document.getElementById("postal").value.trim();
  const fullAddress = `${address}, ${city}, ${province}, ${postal}`;

  const deliveryDate = document.getElementById("deliveryDate").value;
  const deliveryTime = document.getElementById("deliveryTime").value;
  const cardMessage = document.getElementById("notes").value.trim();

  const paymentMethod = "qris";

  const lat = document.getElementById("inputLat").value;
  const lng = document.getElementById("inputLng").value;

  const subtotal = document.getElementById("inputSubtotal").value;
  const shipping = document.getElementById("inputShipping").value;
  const grandTotal = document.getElementById("inputGrandTotal").value;

  // Validation
  if (
    !recipientName ||
    !recipientPhone ||
    !address ||
    !deliveryDate ||
    !deliveryTime
  ) {
    alert("Harap isi semua field yang diperlukan (dengan tanda *).");
    return;
  }

  if (!lat || !lng) {
    alert("Harap pin lokasi Anda di peta.");
    return;
  }

  const formData = new FormData();
  formData.append("payment_method", paymentMethod);
  formData.append("recipient_name", recipientName);
  formData.append("recipient_phone", recipientPhone);
  formData.append("shipping_address", fullAddress);
  formData.append("delivery_date", deliveryDate);
  formData.append("delivery_time", deliveryTime);
  formData.append("card_message", cardMessage);
  formData.append("subtotal", subtotal);
  formData.append("shipping_cost", shipping);
  formData.append("grand_total", grandTotal);
  formData.append("latitude", lat);
  formData.append("longitude", lng);

  try {
    const btn = document.getElementById("btnPlaceOrder");
    btn.disabled = true;
    btn.textContent = "Processing...";

    const response = await fetch(API_ORDER_CREATE, {
      method: "POST",
      body: formData,
    });

    const result = await response.json();

    if (result.success) {
      const orderCode = result.data.order_code;
      const amount = parseInt(result.data.grand_total);

      try {
        await fetch("/api/cart/clear.php", { method: "POST" });
        if (typeof updateCartBadge === "function") {
          updateCartBadge();
        }
      } catch (err) {
        console.warn("Failed to clear cart silently", err);
      }

      const redirectUrl = `${APP_URL}/payment_success.php?order_code=${orderCode}`;
      const pakasirUrl = `${PAKASIR_API_URL}/pay/${PAKASIR_SLUG}/${amount}?order_id=${orderCode}&qris_only=1&redirect=${encodeURIComponent(
        redirectUrl
      )}`;

      window.location.href = pakasirUrl;
    } else {
      alert("Failed to place order: " + (result.message || "Unknown error"));
      btn.disabled = false;
      btn.textContent = "Place Order (QRIS)";
    }
  } catch (error) {
    console.error("Order submit error:", error);
    alert("Connection error. Please try again.");
    document.getElementById("btnPlaceOrder").disabled = false;
  }
}
