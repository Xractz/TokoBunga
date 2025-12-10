// Helper for Currency
function formatCurrency(amount) {
  return new Intl.NumberFormat("id-ID", {
    style: "currency",
    currency: "IDR",
    minimumFractionDigits: 0,
  }).format(amount);
}

// Global state
let currentProductPage = 1;
let currentCategoryPage = 1;
const limit = 10;

$(document).ready(function () {
  // --- SIDEBAR NAVIGATION ---
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

  // --- PRODUCT MANAGEMENT ---

  // Init Select2 for Product Modal
  $("#productCategory").select2({
    dropdownParent: $("#addProductModal"),
    placeholder: "Pilih Kategori",
    allowClear: true,
  });

  // Image Preview
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

  // Fetch Categories for Dropdown
  loadCategoryDropdown();

  function loadCategoryDropdown() {
    $.ajax({
      url: "/api/categories/get.php?status=1", // Only active
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

  // Load Products
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
        ? `<img src="/assets/images/${p.image}" class="rounded me-2" width="40" height="40" style="object-fit:cover">`
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

  // Product Form Submit
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
              .attr("src", "/assets/images/" + p.image)
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

  // --- CATEGORY MANAGEMENT ---

  // Load Categories
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

  // Category Form Submit
  $("#addCategoryForm").on("submit", function (e) {
    e.preventDefault();
    const formData = new FormData(this);

    // Fix: Explicitly handle checkbox state for is_active
    // Standard FormData only sends value if checked, missing if unchecked.
    // For update, API uses fallback if missing, so we MUST send 0 if unchecked.
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
          loadCategoryDropdown(); // Refresh dropdown in product modal
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
    $("#categoryStatus").prop("checked", false); // Default inactive
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

  // Generic Pagination Render
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

    // Prev
    container.append(
      createItem(
        "Previous",
        meta.current_page - 1,
        meta.current_page === 1,
        false
      )
    );

    // Numbers
    for (let i = 1; i <= meta.total_pages; i++) {
      container.append(createItem(i, i, false, i === meta.current_page));
    }

    // Next
    container.append(
      createItem(
        "Next",
        meta.current_page + 1,
        meta.current_page === meta.total_pages,
        false
      )
    );
  }

  // Initial Loads
  loadProducts(1);
  loadCategories(1);

  // Transaction Details - Keep existing logic
  const detailBtns = document.querySelectorAll(".btn-detail-transaksi");
  const detailBox = document.getElementById("detail-transaksi-box");
  const detailIdSpan = document.getElementById("detail-id");

  // We might need delegation if transactions are dynamic later
  $(document).on("click", ".btn-detail-transaksi", function () {
    const id = $(this).data("id");
    $("#detail-id").text(id);
    $("#detail-transaksi-box").show();
  });
});
