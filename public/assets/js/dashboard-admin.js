function formatCurrency(amount) {
  const num = Number(amount);
  if (isNaN(num)) return "Rp0";
  return new Intl.NumberFormat("id-ID", {
    style: "currency",
    currency: "IDR",
    minimumFractionDigits: 0,
  }).format(num);
}

let currentProductPage = 1;
let currentCategoryPage = 1;
const limit = 10;

$(document).ready(function () {
  const links = document.querySelectorAll(".sidebar-link");
  const sections = document.querySelectorAll(".admin-section");

  links.forEach((btn) => {
    btn.addEventListener("click", () => {
      links.forEach((b) => b.classList.remove("active"));
      btn.classList.add("active");

      const targetId = btn.getAttribute("data-target");
      sections.forEach((sec) => {
        sec.style.display = sec.id === targetId ? "block" : "none";
      });
    });
  });

  $("#productCategory").select2({
    dropdownParent: $("#addProductModal"),
    placeholder: "Pilih Kategori",
    allowClear: true,
  });

  $("#productImage").change(function () {
    const file = this.files[0];
    if (file) {
      const reader = new FileReader();
      reader.onload = function (e) {
        $("#imagePreview").attr("src", e.target.result).removeClass("d-none");
      };
      reader.readAsDataURL(file);
    } else {
      $("#imagePreview").addClass("d-none");
    }
  });

  loadCategoryDropdown();

  function loadCategoryDropdown() {
    $.ajax({
      url: "/api/categories/get.php?status=1",
      method: "GET",
      success: function (response) {
        if (response.success && response.data && response.data.categories) {
          const select = $("#productCategory");
          select.empty();
          select.append('<option value="">Pilih Kategori</option>');
          response.data.categories.forEach((cat) => {
            select.append(`<option value="${cat.id}">${cat.name}</option>`);
          });
        }
      },
    });
  }

  window.loadProducts = function (page) {
    currentProductPage = page;
    $.ajax({
      url: "/api/products/get.php",
      method: "GET",
      data: { page: page, limit: limit, sort: "newest" },
      success: function (response) {
        if (response.success && response.data) {
          renderProductTable(response.data.products, (page - 1) * limit);
          renderPagination(
            response.data.pagination,
            "pagination-controls",
            "loadProducts"
          );
        }
      },
    });
  };

  function renderProductTable(products, offset) {
    const tbody = $("#products-table-body");
    tbody.empty();
    if (!products || products.length === 0) {
      tbody.append(
        '<tr><td colspan="6" class="text-center">Tidak ada produk.</td></tr>'
      );
      return;
    }

    products.forEach((p, index) => {
      const no = offset + index + 1;
      const price = formatCurrency(p.price);
      const imageHtml = p.image
        ? `<img src="/assets/images/products/${p.image}" class="rounded me-2" width="40" height="40" style="object-fit:cover">`
        : `<div class="rounded me-2 d-flex align-items-center justify-content-center bg-light" style="width:40px;height:40px"><i class="bi bi-image text-muted"></i></div>`;

      const row = `
                <tr>
                    <td>${no}</td>
                    <td>
                        <div class="d-flex align-items-center">
                            ${imageHtml}
                            <strong>${p.name}</strong>
                        </div>
                    </td>
                    <td>${p.category_name || "-"}</td>
                    <td>${price}</td>
                    <td>${p.stock}</td>
                    <td>
                        <button class="btn btn-sm btn-outline-secondary me-1" onclick="editProduct(${
                          p.id
                        })">
                            <i class="bi bi-pencil-square"></i>
                        </button>
                        <button class="btn btn-sm btn-outline-danger" onclick="deleteProduct(${
                          p.id
                        })">
                            <i class="bi bi-trash3"></i>
                        </button>
                    </td>
                </tr>
            `;
      tbody.append(row);
    });
  }

  $("#addProductForm").on("submit", function (e) {
    e.preventDefault();
    const submitBtn = $(this).find('button[type="submit"]');
    const originalText = submitBtn.text();
    submitBtn.prop("disabled", true).text("Menyimpan...");

    const formData = new FormData(this);
    const id = $("#productId").val();

    let url = "/api/products/create.php";
    if (id) {
      url = "/api/products/update.php";
      formData.append("id", id);
    }

    $.ajax({
      url: url,
      method: "POST",
      data: formData,
      processData: false,
      contentType: false,
      success: function (response) {
        if (response.success) {
          alert("Data berhasil disimpan!");
          $("#addProductModal").modal("hide");
          loadProducts(currentProductPage);
        } else {
          alert("Gagal: " + response.message);
        }
      },
      error: function (xhr) {
        alert(
          "Terjadi kesalahan: " + (xhr.responseJSON?.message || xhr.statusText)
        );
      },
      complete: function () {
        submitBtn.prop("disabled", false).text(originalText);
      },
    });
  });

  window.prepareAdd = function () {
    $("#modalTitle").text("Tambah Produk Baru");
    $("#addProductForm")[0].reset();
    $("#productId").val("");
    $("#productCategory").val(null).trigger("change");
    $("#imagePreview").addClass("d-none");
  };

  window.editProduct = function (id) {
    $.ajax({
      url: "/api/products/get.php",
      data: { id: id },
      success: function (response) {
        if (response.success && response.data) {
          const p = response.data;
          $("#modalTitle").text("Edit Produk");
          $("#productId").val(p.id);
          $("#productName").val(p.name);
          $("#productCategory").val(p.category_id).trigger("change");
          $("#productPrice").val(p.price);
          $("#productStock").val(p.stock);
          $("#productDesc").val(p.description);
          $("#productImage").val("");
          if (p.image) {
            $("#imagePreview")
              .attr("src", "/assets/images/products/" + p.image)
              .removeClass("d-none");
          } else {
            $("#imagePreview").addClass("d-none");
          }
          $("#addProductModal").modal("show");
        }
      },
    });
  };

  window.deleteProduct = function (id) {
    if (confirm("Hapus produk ini?")) {
      $.ajax({
        url: "/api/products/delete.php",
        method: "POST",
        data: { id: id },
        success: function (response) {
          if (response.success) {
            loadProducts(currentProductPage);
          } else {
            alert("Gagal: " + response.message);
          }
        },
      });
    }
  };

  window.loadCategories = function (page) {
    currentCategoryPage = page;
    $.ajax({
      url: "/api/categories/get.php",
      data: { page: page, limit: limit, status: "all" },
      success: function (response) {
        if (response.success && response.data) {
          renderCategoryTable(response.data.categories, (page - 1) * limit);
          renderPagination(
            response.data.pagination,
            "category-pagination-controls",
            "loadCategories"
          );
        }
      },
    });
  };

  function renderCategoryTable(categories, offset) {
    const tbody = $("#categories-table-body");
    tbody.empty();
    if (!categories || categories.length === 0) {
      tbody.append(
        '<tr><td colspan="4" class="text-center">Tidak ada kategori.</td></tr>'
      );
      return;
    }

    categories.forEach((cat, index) => {
      const no = offset + index + 1;
      const statusBadge =
        cat.is_active == 1
          ? '<span class="badge bg-success">Active</span>'
          : '<span class="badge bg-secondary">Inactive</span>';

      const row = `
                <tr>
                    <td>${no}</td>
                    <td>${cat.name}</td>
                    <td>${statusBadge}</td>
                    <td>
                        <button class="btn btn-sm btn-outline-secondary me-1" onclick="editCategory(${cat.id})">
                            <i class="bi bi-pencil-square"></i>
                        </button>
                        <button class="btn btn-sm btn-outline-danger" onclick="deleteCategory(${cat.id})">
                            <i class="bi bi-trash3"></i>
                        </button>
                    </td>
                </tr>
            `;
      tbody.append(row);
    });
  }

  $("#addCategoryForm").on("submit", function (e) {
    e.preventDefault();
    const formData = new FormData(this);

    formData.set("is_active", $("#categoryStatus").is(":checked") ? 1 : 0);

    const id = $("#categoryId").val();
    let url = "/api/categories/create.php";
    if (id) {
      url = "/api/categories/update.php";
      formData.append("id", id);
    }

    $.ajax({
      url: url,
      method: "POST",
      data: formData,
      processData: false,
      contentType: false,
      success: function (response) {
        if (response.success) {
          alert("Kategori berhasil disimpan!");
          $("#addCategoryModal").modal("hide");
          loadCategories(currentCategoryPage);
          loadCategoryDropdown();
        } else {
          alert("Gagal: " + response.message);
        }
      },
      error: function (xhr) {
        alert(
          "Terjadi kesalahan: " + (xhr.responseJSON?.message || xhr.statusText)
        );
      },
    });
  });

  window.prepareAddCategory = function () {
    $("#categoryModalTitle").text("Tambah Kategori");
    $("#addCategoryForm")[0].reset();
    $("#categoryId").val("");
    $("#categoryDesc").val("");
    $("#categoryStatus").prop("checked", false);
  };

  window.editCategory = function (id) {
    $.ajax({
      url: "/api/categories/get.php",
      data: { id: id },
      success: function (response) {
        if (response.success && response.data) {
          const c = response.data;
          $("#categoryModalTitle").text("Edit Kategori");
          $("#categoryId").val(c.id);
          $("#categoryName").val(c.name);
          $("#categoryDesc").val(c.description || "");
          $("#categoryStatus").prop("checked", c.is_active == 1);

          $("#addCategoryModal").modal("show");
        }
      },
    });
  };

  window.deleteCategory = function (id) {
    if (confirm("Hapus kategori ini?")) {
      $.ajax({
        url: "/api/categories/delete.php",
        method: "POST",
        data: { id: id },
        success: function (response) {
          if (response.success) {
            loadCategories(currentCategoryPage);
            loadCategoryDropdown();
          } else {
            alert("Gagal: " + response.message);
          }
        },
      });
    }
  };

  function renderPagination(meta, containerId, funcName) {
    const container = $("#" + containerId);
    container.empty();

    if (!meta || meta.total_pages <= 1) return;

    function createItem(label, page, disabled, active) {
      return `
                <li class="page-item ${
                  disabled ? "disabled" : ""
                } ${active ? "active" : ""}">
                    <button class="page-link" onclick="${funcName}(${page})">${label}</button>
                </li>
            `;
    }

    container.append(
      createItem(
        "Previous",
        meta.current_page - 1,
        meta.current_page === 1,
        false
      )
    );

    for (let i = 1; i <= meta.total_pages; i++) {
      container.append(createItem(i, i, false, i === meta.current_page));
    }

    container.append(
      createItem(
        "Next",
        meta.current_page + 1,
        meta.current_page === meta.total_pages,
        false
      )
    );
  }

  loadProducts(1);
  loadCategories(1);

  window.loadStats = function (period) {
    $(".btn-group .btn").removeClass("active");
    $("#btn-" + period).addClass("active");

    $.ajax({
      url: "/api/admin/stats.php",
      method: "GET",
      data: { period: period },
      success: function (response) {
        if (response.success && response.data) {
          $("#stat-transactions").text(response.data.total_transactions);
          $("#stat-revenue").text(formatCurrency(response.data.total_revenue));
        }
      },
      error: function (xhr) {
        console.error("Failed to load stats:", xhr);
      },
    });
  };

  loadStats("week");

  let currentTransactionPage = 1;

  window.loadTransactions = function (page) {
    currentTransactionPage = page;
    $.ajax({
      url: "/api/orders/list.php",
      data: { page: page, limit: 10 },
      success: function (response) {
        if (response.success && response.data) {
          renderTransactionTable(response.data.orders, (page - 1) * 10);
          renderPagination(
            response.data.pagination,
            "transaction-pagination-controls",
            "loadTransactions"
          );
        }
      },
    });
  };

  function renderTransactionTable(orders, offset) {
    const tbody = $("#transactions-table-body");
    tbody.empty();
    if (!orders || orders.length === 0) {
      tbody.append(
        '<tr><td colspan="6" class="text-center">Tidak ada transaksi.</td></tr>'
      );
      return;
    }

    orders.forEach((order, index) => {
      let badgeClass = "bg-secondary";
      if (order.status === "completed") badgeClass = "bg-success";
      else if (order.status === "cancelled") badgeClass = "bg-danger";
      else if (order.status === "pending") badgeClass = "bg-warning text-dark";
      else badgeClass = "bg-info text-dark";

      const row = `
                <tr>
                    <td>${order.order_code}</td>
                    <td>${order.created_at}</td>
                    <td>${order.customer_name || order.recipient_name}</td>
                    <td>${formatCurrency(order.grand_total)}</td>
                    <td><span class="badge ${badgeClass}">${
        order.status
      }</span></td>
                    <td>
                         <button class="btn btn-sm btn-outline-primary me-1" onclick="viewTransactionDetail('${
                           order.order_code
                         }')" title="Lihat Detail">
                            <i class="bi bi-eye"></i>
                        </button>
                        <button class="btn btn-sm btn-outline-warning" onclick="editTransactionStatus(${
                          order.id
                        }, '${order.status}')" title="Update Status">
                            <i class="bi bi-pencil"></i>
                        </button>
                    </td>
                </tr>
            `;
      tbody.append(row);
    });
  }

  window.viewTransactionDetail = function (orderCode) {
    $.ajax({
      url: "/api/orders/detail.php",
      data: { order_code: orderCode },
      success: function (response) {
        if (response.success && response.data) {
          const o = response.data;
          console.log(o);
          $("#detailOrderCode").text(o.order_code);
          $("#detailRecipientName").text(o.recipient_name);
          $("#detailRecipientPhone").text(o.recipient_phone);
          $("#detailAddress").text(o.shipping_address);
          $("#detailDeliveryTime").text(
            (o.delivery_date || "-") + " " + (o.delivery_time || "")
          );
          $("#detailStatus").text(o.status);
          $("#detailPaymentStatus").text(
            o.payment_status + " (" + (o.payment_method || "-") + ")"
          );
          $("#detailCardMessage").text(o.card_message || "-");
          $("#detailGrandTotal").text(formatCurrency(o.grand_total));

          const tbody = $("#detailItemsTable");
          tbody.empty();
          if (o.items && o.items.length > 0) {
            o.items.forEach((item) => {
              tbody.append(`
                                <tr>
                                    <td>${
                                      item.product_name ||
                                      "Produk Tidak Ditemukan (Dihapus)"
                                    }</td>
                                    <td>${formatCurrency(item.unit_price)}</td>
                                    <td>${item.quantity}</td>
                                    <td>${formatCurrency(item.subtotal)}</td>
                                </tr>
                            `);
            });
          } else {
            tbody.append(
              '<tr><td colspan="4" class="text-center text-muted">Tidak ada item dalam pesanan ini.</td></tr>'
            );
          }

          $("#transactionDetailModal").modal("show");
        } else {
          alert(
            "Gagal memuat detail: " + (response.message || "Unknown error")
          );
        }
      },
      error: function (xhr, status, error) {
        console.error("AJAX Error:", xhr.responseText);
        alert(
          "Terjadi kesalahan koneksi: " + (xhr.responseJSON?.message || error)
        );
      },
    });
  };

  window.editTransactionStatus = function (id, status) {
    $("#statusOrderId").val(id);
    $("#statusSelect").val(status);
    $("#updateStatusModal").modal("show");
  };

  $("#updateStatusForm").on("submit", function (e) {
    e.preventDefault();
    const formData = new FormData(this);
    $.ajax({
      url: "/api/orders/update-status.php",
      method: "POST",
      data: formData,
      processData: false,
      contentType: false,
      success: function (response) {
        if (response.success) {
          alert("Status berhasil diperbarui.");
          $("#updateStatusModal").modal("hide");
          loadTransactions(currentTransactionPage);
        } else {
          alert("Gagal: " + response.message);
        }
      },
    });
  });

  loadTransactions(1);

  let currentCustomerPage = 1;

  window.loadCustomers = function (page) {
    currentCustomerPage = page;
    $.ajax({
      url: "/api/users/list.php",
      data: { page: page, limit: 10 },
      success: function (response) {
        if (response.success && response.data && response.data.users) {
          $("#totalCustomers").text(
            "Total: " + (response.data.pagination.total_count || 0)
          );
          renderCustomerTable(response.data.users, (page - 1) * 10);
          renderPagination(
            response.data.pagination,
            "customer-pagination-controls",
            "loadCustomers"
          );
        }
      },
    });
  };

  function renderCustomerTable(users, offset) {
    const tbody = $("#customers-table-body");
    tbody.empty();
    if (!users || users.length === 0) {
      tbody.append(
        '<tr><td colspan="5" class="text-center">Tidak ada pelanggan.</td></tr>'
      );
      return;
    }

    users.forEach((u, index) => {
      const no = offset + index + 1;
      const tr = `
              <tr>
                  <td>${u.id}</td>
                  <td>
                      <div class="d-flex align-items-center">
                          <img src="${
                            u.profile_photo
                              ? "/assets/images/profiles/" + u.profile_photo
                              : "https://ui-avatars.com/api/?name=" + u.name
                          }" class="rounded-circle me-2" width="30" height="30">
                          ${u.name}
                      </div>
                  </td>
                  <td>${u.email}</td>
                  <td>${u.phone || "-"}</td>
                  <td>${u.total_transactions}</td>
              </tr>
          `;
      tbody.append(tr);
    });
  }

  loadCustomers(1);
});
