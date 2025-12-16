document.addEventListener("DOMContentLoaded", async () => {
  const listContainer = document.getElementById("ordersListContainer");
  const cardsContainer = document.getElementById("ordersCardsContainer");
  const emptyState = document.getElementById("emptyState");
  const contentWrapper = document.getElementById("contentWrapper");
  const paginationContainer = document.getElementById("paginationContainer");

  let currentPage = 1;
  const perPage = 10;

  fetchOrders(currentPage);

  async function fetchOrders(page) {
    try {
      const response = await fetch(
        `/api/orders/list.php?page=${page}&limit=${perPage}`
      );
      const result = await response.json();

      if (!result.success) {
        console.error(result.message);
        return;
      }

      const orders = result.data.items;
      const pagination = result.data.pagination;

      if (orders.length === 0 && page === 1) {
        emptyState.style.display = "flex";
        contentWrapper.style.display = "none";
        paginationContainer.style.display = "none";
      } else {
        emptyState.style.display = "none";
        contentWrapper.style.display = "block";
        renderOrders(orders);
        renderPagination(pagination);
      }
    } catch (error) {
      console.error("Error fetching orders:", error);
    }
  }

  function renderOrders(orders) {
    listContainer.innerHTML = "";
    cardsContainer.innerHTML = "";

    orders.forEach((order) => {
      const ORDER_STATUS_MAP = {
        pending: { color: "status-pending", label: "Menunggu Konfirmasi" },
        confirmed: { color: "status-success", label: "Dikonfirmasi" },
        processing: { color: "status-success", label: "Sedang Diproses" },
        shipped: { color: "status-success", label: "Sedang Dikirim" },
        completed: { color: "status-success", label: "Selesai" },
        cancelled: { color: "status-cancel", label: "Dibatalkan" },
      };

      const PAYMENT_STATUS_MAP = {
        unpaid: { color: "status-pending", label: "Belum Bayar" },
        paid: { color: "status-success", label: "Lunas" },
        refunded: { color: "status-cancel", label: "Dikembalikan" },
      };

      const orderStatusConfig =
        ORDER_STATUS_MAP[order.status] || ORDER_STATUS_MAP["pending"];
      const paymentStatusConfig =
        PAYMENT_STATUS_MAP[order.payment_status] ||
        PAYMENT_STATUS_MAP["unpaid"];

      const dateStr = new Date(order.created_at).toLocaleDateString("id-ID", {
        day: "2-digit",
        month: "short",
        year: "numeric",
      });
      const totalStr = new Intl.NumberFormat("id-ID").format(order.grand_total);
      const detailUrl = `orders-detail.php?order_code=${order.order_code}`;

      const tr = document.createElement("tr");
      tr.innerHTML = `
                <td>#${escapeHtml(order.order_code)}</td>
                <td>${dateStr}</td>
                <td><span class="badge ${orderStatusConfig.color}">${
        orderStatusConfig.label
      }</span></td>
                <td><span class="badge ${paymentStatusConfig.color}">${
        paymentStatusConfig.label
      }</span></td>
                <td>Rp ${totalStr}</td>
                <td>
                    <a href="${detailUrl}" class="btn-link">
                        Lihat <i class="bi bi-arrow-right-short"></i>
                    </a>
                </td>
            `;
      listContainer.appendChild(tr);

      const card = document.createElement("article");
      card.className = "order-card";
      card.innerHTML = `
                <div class="order-card-row">
                    <span class="label">No. Pesanan</span>
                    <span class="value">#${escapeHtml(order.order_code)}</span>
                </div>
                <div class="order-card-row">
                    <span class="label">Tanggal</span>
                    <span class="value">${dateStr}</span>
                </div>
                <div class="order-card-row">
                    <span class="label">Status Pesanan</span>
                    <span class="value"><span class="badge ${
                      orderStatusConfig.color
                    }">${orderStatusConfig.label}</span></span>
                </div>
                <div class="order-card-row">
                    <span class="label">Status Pembayaran</span>
                    <span class="value"><span class="badge ${
                      paymentStatusConfig.color
                    }">${paymentStatusConfig.label}</span></span>
                </div>
                <div class="order-card-row">
                    <span class="label">Total</span>
                    <span class="value">Rp ${totalStr}</span>
                </div>
                <div class="order-card-actions">
                    <a href="${detailUrl}" class="btn btn-secondary" style="padding: 0.5rem 1rem; font-size: 0.9rem;">Lihat Detail</a>
                </div>
            `;
      cardsContainer.appendChild(card);
    });
  }

  function renderPagination(pagination) {
    paginationContainer.style.display = "flex";
    paginationContainer.innerHTML = "";

    const { current_page, total_pages } = pagination;

    if (total_pages <= 1) {
      paginationContainer.style.display = "none";
      return;
    }

    const prevBtn = document.createElement("button");
    prevBtn.className = "btn btn-secondary";
    prevBtn.innerHTML = '<i class="bi bi-chevron-left"></i> Previous';
    prevBtn.disabled = current_page === 1;
    prevBtn.onclick = () => goToPage(current_page - 1);
    paginationContainer.appendChild(prevBtn);

    const pageNumbers = document.createElement("div");
    pageNumbers.className = "pagination-numbers";

    const startPage = Math.max(1, current_page - 2);
    const endPage = Math.min(total_pages, current_page + 2);

    if (startPage > 1) {
      addPageButton(pageNumbers, 1, current_page);
      if (startPage > 2) {
        const ellipsis = document.createElement("span");
        ellipsis.textContent = "...";
        ellipsis.style.padding = "0 0.5rem";
        pageNumbers.appendChild(ellipsis);
      }
    }

    for (let i = startPage; i <= endPage; i++) {
      addPageButton(pageNumbers, i, current_page);
    }

    if (endPage < total_pages) {
      if (endPage < total_pages - 1) {
        const ellipsis = document.createElement("span");
        ellipsis.textContent = "...";
        ellipsis.style.padding = "0 0.5rem";
        pageNumbers.appendChild(ellipsis);
      }
      addPageButton(pageNumbers, total_pages, current_page);
    }

    paginationContainer.appendChild(pageNumbers);

    const nextBtn = document.createElement("button");
    nextBtn.className = "btn btn-secondary";
    nextBtn.innerHTML = 'Next <i class="bi bi-chevron-right"></i>';
    nextBtn.disabled = current_page === total_pages;
    nextBtn.onclick = () => goToPage(current_page + 1);
    paginationContainer.appendChild(nextBtn);
  }

  function addPageButton(container, pageNum, currentPage) {
    const btn = document.createElement("button");
    btn.className =
      pageNum === currentPage ? "btn btn-primary" : "btn btn-secondary";
    btn.textContent = pageNum;
    btn.style.margin = "0 0.25rem";
    btn.onclick = () => goToPage(pageNum);
    container.appendChild(btn);
  }

  function goToPage(page) {
    currentPage = page;
    window.scrollTo({ top: 0, behavior: "smooth" });
    fetchOrders(currentPage);
  }

  function escapeHtml(text) {
    if (!text) return text;
    return text
      .replace(/&/g, "&amp;")
      .replace(/</g, "&lt;")
      .replace(/>/g, "&gt;")
      .replace(/"/g, "&quot;")
      .replace(/'/g, "&#039;");
  }
});
