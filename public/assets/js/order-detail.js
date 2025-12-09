// order-detail.js
document.addEventListener("DOMContentLoaded", async () => {
  const STATUS_MAP = {
    unpaid: { color: "status-pending", label: "Menunggu Pembayaran" },
    pending: { color: "status-pending", label: "Menunggu Konfirmasi" },
    confirmed: { color: "status-success", label: "Dikonfirmasi" },
    processing: { color: "status-success", label: "Sedang Diproses" },
    shipped: { color: "status-success", label: "Sedang Dikirim" },
    completed: { color: "status-success", label: "Selesai" },
    cancelled: { color: "status-cancel", label: "Dibatalkan" },
    refunded: { color: "status-cancel", label: "Dana Dikembalikan" },
  };

  const urlParams = new URLSearchParams(window.location.search);
  const orderCode = urlParams.get("order_code") || urlParams.get("id");

  if (!orderCode) {
    alert("Order code missing");
    window.location.href = "orders-history.php";
    return;
  }

  try {
    const response = await fetch(
      `/api/orders/detail.php?order_code=${orderCode}`
    );

    if (!response.ok) {
      const errorText = await response.text();
      console.error("API Error Response:", errorText);
      window.location.href = "orders-history.php";
      return;
    }

    const result = await response.json();

    if (!result.success) {
      alert(result.message || "Failed to load order");
      window.location.href = "orders-history.php";
      return;
    }

    const data = result.data;
    renderOrderDetail(data);
  } catch (error) {
    console.error("Error fetching order:", error);
  }

  function renderOrderDetail(order) {
    // 1. Text Fields
    setText("orderCodeDisplay", `#${order.order_code}`);
    setText("recipientName", order.recipient_name);
    setText("recipientPhone", order.recipient_phone);
    setText("shippingAddress", order.shipping_address);

    // Format Date Time
    const dateObj = new Date(order.delivery_date);
    setText(
      "deliveryDate",
      dateObj.toLocaleDateString("id-ID", {
        day: "2-digit",
        month: "short",
        year: "numeric",
      })
    );
    setText("deliveryTime", order.delivery_time);

    // Status - Match database enum values exactly
    // Status: pending, confirmed, processing, shipped, completed, cancelled
    // Payment: unpaid, paid, refunded

    let statusKey = "pending"; // Default

    if (order.status === "cancelled") {
      statusKey = "cancelled";
    } else if (order.payment_status === "refunded") {
      statusKey = "refunded";
    } else if (order.payment_status === "unpaid") {
      statusKey = "unpaid";
    } else {
      // Paid & Active (not cancelled/refunded)
      // Use the main status
      statusKey = order.status;
    }

    const statusConfig = STATUS_MAP[statusKey] || STATUS_MAP["pending"];

    const badgeEl = document.getElementById("statusBadge");
    if (badgeEl) {
      badgeEl.className = `badge ${statusConfig.color}`;
      badgeEl.innerText = statusConfig.label;
      badgeEl.style.fontSize = "1rem";
      badgeEl.style.padding = "0.5rem 1rem";
    }

    // Card Message
    const msgContainer = document.getElementById("cardMessageContainer");
    if (order.card_message) {
      setText("cardMessage", order.card_message);
      msgContainer.style.display = "block";
    } else {
      msgContainer.style.display = "none";
    }

    // 2. Map
    if (order.latitude && order.longitude) {
      initMap(parseFloat(order.latitude), parseFloat(order.longitude));
    } else {
      document.getElementById("mapContainer").style.display = "none";
    }

    // 3. Items
    const itemsList = document.getElementById("itemsList");
    itemsList.innerHTML = "";

    if (order.items && order.items.length > 0) {
      order.items.forEach((item) => {
        const row = document.createElement("div");
        row.className = "item-row";

        const imgUrl = item.image_url || "assets/images/placeholder.jpg";
        const price = new Intl.NumberFormat("id-ID").format(item.price);

        row.innerHTML = `
                    <img src="${imgUrl}" class="item-thumb" alt="Product">
                    <div class="item-info">
                        <h4 class="item-name">${item.product_name}</h4>
                        <p class="item-meta">${item.quantity} x Rp ${price}</p>
                    </div>
                `;
        itemsList.appendChild(row);
      });
    }

    // 4. Totals
    setPrice("subtotalDisplay", order.subtotal);
    setPrice("shippingDisplay", order.shipping_cost);
    setPrice("grandTotalDisplay", order.grand_total);

    // 5. Payment Button
    const payBtnContainer = document.getElementById("paymentActionContainer");
    // Show button only if: unpaid AND not cancelled
    if (order.payment_status === "unpaid" && order.status !== "cancelled") {
      payBtnContainer.style.display = "block";
      const payBtn = document.getElementById("payBtn");
      payBtn.href = `payment_success.php?order_code=${order.order_code}`;
    } else {
      payBtnContainer.style.display = "none";
    }
  }

  function setText(id, text) {
    const el = document.getElementById(id);
    if (el) el.innerText = text || "-";
  }

  function setPrice(id, amount) {
    const el = document.getElementById(id);
    if (el)
      el.innerText = "Rp " + new Intl.NumberFormat("id-ID").format(amount);
  }

  function initMap(lat, lng) {
    const mapEl = document.getElementById("map");
    if (!mapEl) return;

    const map = L.map("map", {
      center: [lat, lng],
      zoom: 15,
      scrollWheelZoom: false,
      dragging: false,
      zoomControl: false,
    });
    L.tileLayer("https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png", {
      attribution: "&copy; OpenStreetMap contributors",
    }).addTo(map);
    L.marker([lat, lng]).addTo(map);
  }
});
