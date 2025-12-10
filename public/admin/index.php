<?php
require_once __DIR__ . '/../../api/middleware/is_admin.php';
require_once __DIR__ . '/../../api/helpers/flash.php';

// Get admin data
$adminName = $_SESSION['name'] ?? 'Admin';

// Get flash messages
$error = flash('error');
$success = flash('success');
?>
<!DOCTYPE html>
<html lang="id">
  <head>
    <meta charset="UTF-8" />
    <title>Dashboard Admin - Bloomify</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />

    <!-- Google Fonts -->
    <link
      href="https://fonts.googleapis.com/css2?family=Playfair+Display+SC:wght@400;700&family=Playfair:wght@300;400;500;600;700&display=swap"
      rel="stylesheet"
    />

    <!-- Bootstrap Icons -->
    <link
      rel="stylesheet"
      href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css"
    />

    <!-- Bootstrap CSS -->
    <link
      href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
      rel="stylesheet"
      integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH"
      crossorigin="anonymous"
    />

    <!-- CSS utama project kamu -->
    <link rel="stylesheet" href="../assets/css/style.css" />

    <!-- Select2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <style>
        /* Custom Select2 Style for Bootstrap 5 */
        .select2-container .select2-selection--single {
            height: 38px;
            border: 1px solid #dee2e6;
            padding: 5px;
        }
        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 36px;
        }
    </style>
  <body>
    <!-- NAVBAR -->
    <header class="header">
      <div class="container">
        <nav class="navbar">
          <!-- Logo -->
          <div class="logo">
            <i class="bi bi-flower1"></i>
            <h1>Bloomify</h1>
          </div>

          <!-- Info admin di kanan -->
          <div class="auth-buttons">
            <span style="font-size: 0.9rem; color: var(--text-muted)">
              <i class="bi bi-person-gear"></i> <?php echo htmlspecialchars($adminName); ?>
            </span>
            <a href="/api/auth/logout.php" class="icon-btn" title="Logout">
              <i class="bi bi-box-arrow-right"></i>
            </a>
          </div>
        </nav>
      </div>
    </header>

    <!-- MAIN DASHBOARD -->
    <main class="container">
      <div class="admin-layout">
        <!-- KONTEN KIRI -->
        <section class="admin-content">
          <h1 class="mb-1">Dashboard Admin</h1>
          <p class="mb-3" style="color: var(--text-muted); font-size: 0.95rem">
            Kelola produk, kategori, transaksi, dan pelanggan untuk toko bunga
            Bloomify.
          </p>

          <?php if ($error): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
              <i class="bi bi-exclamation-circle"></i>
              <?php echo htmlspecialchars($error); ?>
              <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
          <?php endif; ?>

          <?php if ($success): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
              <i class="bi bi-check-circle"></i>
              <?php echo htmlspecialchars($success); ?>
              <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
          <?php endif; ?>

          <!-- ============== SECTION: PRODUK ============== -->
          <div class="admin-section" id="section-produk">
            <div class="section-header-row">
              <h2 class="mb-0">Produk</h2>
              <button type="button" class="btn-primary" data-bs-toggle="modal" data-bs-target="#addProductModal" onclick="prepareAdd()">
                <i class="bi bi-plus-lg"></i> Tambah Produk
              </button>
            </div>
            <p class="section-description">
              Daftar produk yang tersedia di toko.
            </p>

            <div class="table-responsive">
              <table class="table table-hover table-responsive align-middle mb-0">
                <thead class="table-light">
                  <tr>
                    <th>No</th>
                    <th>Nama Produk</th>
                    <th>Kategori</th>
                    <th>Harga</th>
                    <th>Stok</th>
                    <th style="width: 120px">Aksi</th>
                  </tr>
                </thead>
                <tbody id="products-table-body">
                  <!-- Javascript will populate this -->
                </tbody>
              </table>
            </div>

            <!-- Pagination Container -->
            <nav class="d-flex justify-content-end mt-3">
                <ul class="pagination" id="pagination-controls">
                    <!-- Javascript will populate this -->
                </ul>
            </nav>
          </div>

          <!-- ============== SECTION: KATEGORI ============== -->
          <div
            class="admin-section"
            id="section-kategori"
            style="display: none"
          >
            <div class="section-header-row">
              <h2 class="mb-0">Kategori</h2>
              <button type="button" class="btn-primary">
                <i class="bi bi-plus-lg"></i> Tambah Kategori
              </button>
            </div>
            <p class="section-description">Kelola kategori produk bunga.</p>

            <div class="table-responsive">
              <table
                class="table table-hover table-responsive align-middle mb-0"
              >
                <thead class="table-light">
                  <tr>
                    <th>ID</th>
                    <th>Nama Kategori</th>
                    <th>Deskripsi</th>
                    <th style="width: 120px">Aksi</th>
                  </tr>
                </thead>
                <tbody>
                  <tr>
                    <td>CAT01</td>
                    <td>Buket</td>
                    <td>Rangkaian bunga dalam bentuk buket.</td>
                    <td>
                      <button
                        class="btn btn-sm btn-outline-secondary me-1"
                        type="button"
                      >
                        <i class="bi bi-pencil-square"></i>
                      </button>
                      <button
                        class="btn btn-sm btn-outline-danger"
                        type="button"
                      >
                        <i class="bi bi-trash3"></i>
                      </button>
                    </td>
                  </tr>
                  <tr>
                    <td>CAT02</td>
                    <td>Box Bunga</td>
                    <td>Bunga dalam box elegan untuk hadiah.</td>
                    <td>
                      <button
                        class="btn btn-sm btn-outline-secondary me-1"
                        type="button"
                      >
                        <i class="bi bi-pencil-square"></i>
                      </button>
                      <button
                        class="btn btn-sm btn-outline-danger"
                        type="button"
                      >
                        <i class="bi bi-trash3"></i>
                      </button>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>

          <!-- ============== SECTION: LOG TRANSAKSI ============== -->
          <div
            class="admin-section"
            id="section-transaksi"
            style="display: none"
          >
            <div class="section-header-row">
              <h2 class="mb-0">Log Transaksi</h2>
            </div>
            <p class="section-description">
              Riwayat pesanan pelanggan. Klik tombol detail untuk melihat
              rincian pesanan.
            </p>

            <div class="table-responsive">
              <table
                class="table table-hover table-responsive align-middle mb-0"
              >
                <thead class="table-light">
                  <tr>
                    <th>ID Transaksi</th>
                    <th>Tanggal</th>
                    <th>Pelanggan</th>
                    <th>Total</th>
                    <th>Status</th>
                    <th style="width: 100px">Aksi</th>
                  </tr>
                </thead>
                <tbody>
                  <tr>
                    <td>TRX001</td>
                    <td>03-12-2025</td>
                    <td>Ani</td>
                    <td>Rp350.000</td>
                    <td>
                      <span class="badge text-bg-success">Selesai</span>
                    </td>
                    <td>
                      <button
                        class="btn btn-sm btn-outline-primary btn-detail-transaksi"
                        type="button"
                        data-id="TRX001"
                      >
                        <i class="bi bi-eye"></i>
                      </button>
                    </td>
                  </tr>
                  <tr>
                    <td>TRX002</td>
                    <td>02-12-2025</td>
                    <td>Budi</td>
                    <td>Rp250.000</td>
                    <td>
                      <span class="badge text-bg-warning">Diproses</span>
                    </td>
                    <td>
                      <button
                        class="btn btn-sm btn-outline-primary btn-detail-transaksi"
                        type="button"
                        data-id="TRX002"
                      >
                        <i class="bi bi-eye"></i>
                      </button>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>

            <!-- Detail transaksi sederhana -->
            <div
              id="detail-transaksi-box"
              class="detail-box"
              style="display: none"
            >
              <h3>Detail Transaksi</h3>
              <p><strong>ID:</strong> <span id="detail-id">TRX001</span></p>
              <p><strong>Pelanggan:</strong> Ani</p>
              <p><strong>Item:</strong> Buket Mawar Manis (1x)</p>
              <p><strong>Total:</strong> Rp350.000</p>
            </div>
          </div>

          <!-- ============== SECTION: CUSTOMER ============== -->
          <div
            class="admin-section"
            id="section-customer"
            style="display: none"
          >
            <div class="section-header-row">
              <h2 class="mb-0">Customer / Pelanggan</h2>
            </div>
            <p class="section-description">
              Data pelanggan yang sudah melakukan pendaftaran atau transaksi.
            </p>

            <div class="table-responsive">
              <table
                class="table table-hover table-responsive align-middle mb-0"
              >
                <thead class="table-light">
                  <tr>
                    <th>ID</th>
                    <th>Nama</th>
                    <th>Email</th>
                    <th>No. HP</th>
                    <th>Total Transaksi</th>
                  </tr>
                </thead>
                <tbody>
                  <tr>
                    <td>CUST01</td>
                    <td>Ani</td>
                    <td>ani@example.com</td>
                    <td>0812-3456-7890</td>
                    <td>3</td>
                  </tr>
                  <tr>
                    <td>CUST02</td>
                    <td>Budi</td>
                    <td>budi@example.com</td>
                    <td>0813-9876-5432</td>
                    <td>1</td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </section>

        <!-- SIDEBAR KANAN -->
        <aside class="admin-sidebar">
          <h3 class="sidebar-title">Menu Admin</h3>
          <button
            class="sidebar-link active"
            data-target="section-produk"
            type="button"
          >
            <i class="bi bi-flower1"></i> Produk
          </button>
          <button
            class="sidebar-link"
            data-target="section-kategori"
            type="button"
          >
            <i class="bi bi-tags"></i> Kategori
          </button>
          <button
            class="sidebar-link"
            data-target="section-transaksi"
            type="button"
          >
            <i class="bi bi-receipt"></i> Log Transaksi
          </button>
          <button
            class="sidebar-link"
            data-target="section-customer"
            type="button"
          >
            <i class="bi bi-people"></i> Customer
          </button>
        </aside>
      </div>
    </main>

    <!-- MODAL ADD PRODUCT -->
    <div class="modal fade" id="addProductModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitle">Tambah Produk Baru</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="addProductForm">
                        <input type="hidden" id="productId" name="id">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="productName" class="form-label">Nama Produk</label>
                                <input type="text" class="form-control" id="productName" name="name" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="productCategory" class="form-label">Kategori</label>
                                <select class="form-select" id="productCategory" name="category_id" style="width: 100%;" required>
                                    <option value="">Pilih Kategori</option>
                                    <!-- Options populated by JS -->
                                </select>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="productPrice" class="form-label">Harga (Rp)</label>
                                <input type="number" class="form-control" id="productPrice" name="price" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="productStock" class="form-label">Stok</label>
                                <input type="number" class="form-control" id="productStock" name="stock" required>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="productImage" class="form-label">Foto Produk</label>
                            <input type="file" class="form-control" id="productImage" name="image" accept="image/*">
                            <div class="mt-2">
                                <img id="imagePreview" src="" alt="Preview" class="img-fluid d-none rounded border" style="max-width: 200px; max-height: 200px; object-fit: cover;">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="productDesc" class="form-label">Deskripsi</label>
                            <textarea class="form-control" id="productDesc" name="description" rows="3"></textarea>
                        </div>

                        <div class="modal-footer px-0 pb-0">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-primary">Simpan Produk</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script
      src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
      integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
      crossorigin="anonymous"
    ></script>

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
    <!-- Select2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script>
      // GANTI KONTEN BERDASARKAN MENU SIDEBAR
      const links = document.querySelectorAll(".sidebar-link");
      const sections = document.querySelectorAll(".admin-section");

      links.forEach((btn) => {
        btn.addEventListener("click", () => {
          // aktifkan tombol
          links.forEach((b) => b.classList.remove("active"));
          btn.classList.add("active");

          // tampilkan section yang dipilih
          const targetId = btn.getAttribute("data-target");
          sections.forEach((sec) => {
            sec.style.display = sec.id === targetId ? "block" : "none";
          });
        });
      });

      // DETAIL TRANSAKSI SEDERHANA
      const detailBtns = document.querySelectorAll(".btn-detail-transaksi");
      const detailBox = document.getElementById("detail-transaksi-box");
      const detailIdSpan = document.getElementById("detail-id");

      detailBtns.forEach((btn) => {
        btn.addEventListener("click", () => {
          const id = btn.getAttribute("data-id");
          detailIdSpan.textContent = id;
          detailBox.style.display = "block";
        });
      });

        // --- NEW LOGIC: ADD PRODUCT ---
        $(document).ready(function() {
            // Initialize Select2
            $('#productCategory').select2({
                dropdownParent: $('#addProductModal'), // Fix Select2 in Bootstrap Modal
                placeholder: "Pilih Kategori",
                allowClear: true
            });

            // Image Preview
            $('#productImage').change(function() {
                const file = this.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        $('#imagePreview').attr('src', e.target.result).removeClass('d-none');
                    }
                    reader.readAsDataURL(file);
                } else {
                    $('#imagePreview').addClass('d-none');
                }
            });

            // Fetch Categories on Load
            loadCategories();

            function loadCategories() {
                $.ajax({
                    url: '/api/categories/get.php?status=all',
                    method: 'GET',
                    success: function(response) {
                        if(response.success && response.data) {
                            const select = $('#productCategory');
                            select.empty();
                            select.append('<option value="">Pilih Kategori</option>');
                            response.data.forEach(cat => {
                                select.append(`<option value="${cat.id}">${cat.name}</option>`);
                            });
                        }
                    },
                    error: function(err) {
                        console.error('Gagal memuat kategori', err);
                    }
                });
            }

            // Handle Form Submit
            $('#addProductForm').on('submit', function(e) {
                e.preventDefault();
                
                const submitBtn = $(this).find('button[type="submit"]');
                const originalText = submitBtn.text();
                submitBtn.prop('disabled', true).text('Menyimpan...');

                const formData = new FormData(this);
                const id = $('#productId').val();
                
                let url = '/api/products/create.php';
                if(id) {
                    url = '/api/products/update.php';
                    formData.append('id', id); // Ensure ID is in FormData
                }

                $.ajax({
                    url: url,
                    method: 'POST', // Both use POST
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        if(response.success) {
                            alert('Data berhasil disimpan!');
                            $('#addProductModal').modal('hide');
                            loadProducts(currentPage); // Reload current page
                        } else {
                            alert('Gagal: ' + response.message);
                        }
                    },
                    error: function(xhr) {
                        alert('Terjadi kesalahan: ' + (xhr.responseJSON?.message || xhr.statusText));
                    },
                    complete: function() {
                        submitBtn.prop('disabled', false).text(originalText);
                    }
                });
            });

            // --- PRODUCT LIST & PAGINATION ---
            const limit = 10;
            let currentPage = 1;

            function loadProducts(page) {
                currentPage = page;
                $.ajax({
                    url: '/api/products/get.php',
                    method: 'GET',
                    data: { page: page, limit: limit, sort: 'newest' }, // Sort by newest by default
                    success: function(response) {
                        if(response.success && response.data) {
                            renderTable(response.data.products, (page - 1) * limit);
                            renderPagination(response.data.pagination);
                        }
                    }
                });
            }

            function renderTable(products, offset) {
                const tbody = $('#products-table-body');
                tbody.empty();
                if(!products || products.length === 0) {
                    tbody.append('<tr><td colspan="6" class="text-center">Tidak ada produk.</td></tr>');
                    return;
                }
                
                products.forEach((p, index) => {
                    const no = offset + index + 1;
                    const price = new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(p.price);
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
                            <td>${p.category_name || '-'}</td>
                            <td>${price}</td>
                            <td>${p.stock}</td>
                            <td>
                                <button class="btn btn-sm btn-outline-secondary me-1" onclick="editProduct(${p.id})">
                                    <i class="bi bi-pencil-square"></i>
                                </button>
                                <button class="btn btn-sm btn-outline-danger" onclick="deleteProduct(${p.id})">
                                    <i class="bi bi-trash3"></i>
                                </button>
                            </td>
                        </tr>
                    `;
                    tbody.append(row);
                });
            }

            function renderPagination(meta) {
                const container = $('#pagination-controls');
                container.empty();
                
                if(meta.total_pages <= 1) return;

                // Helper to create item
                function createItem(label, page, disabled = false, active = false) {
                    return `
                        <li class="page-item ${disabled ? 'disabled' : ''} ${active ? 'active' : ''}">
                            <button class="page-link" onclick="loadProducts(${page})">${label}</button>
                        </li>
                    `;
                }

                // Prev
                container.append(createItem('Previous', meta.current_page - 1, meta.current_page === 1));

                // Numbers
                for(let i=1; i<=meta.total_pages; i++) {
                    container.append(createItem(i, i, false, i === meta.current_page));
                }

                // Next
                container.append(createItem('Next', meta.current_page + 1, meta.current_page === meta.total_pages));
            }
            
            // --- ACTIONS ---
            window.prepareAdd = function() {
                $('#modalTitle').text('Tambah Produk Baru');
                $('#addProductForm')[0].reset();
                $('#productId').val('');
                $('#productCategory').val(null).trigger('change');
                $('#imagePreview').addClass('d-none');
                $('#addProductForm button[type="submit"]').prop('disabled', false).text('Simpan Produk');
            };

            window.editProduct = function(id) {
                // Fetch product details
                $.ajax({
                    url: '/api/products/get.php',
                    method: 'GET',
                    data: { id: id },
                    success: function(response) {
                        if(response.success && response.data) {
                            const p = response.data;
                            
                            $('#modalTitle').text('Edit Produk');
                            $('#productId').val(p.id);
                            $('#productName').val(p.name);
                            $('#productCategory').val(p.category_id).trigger('change');
                            $('#productPrice').val(p.price);
                            $('#productStock').val(p.stock);
                            $('#productDesc').val(p.description);
                            $('#productImage').val(''); // Clear file input (user must re-select to change)

                            if(p.image) {
                                $('#imagePreview').attr('src', '/assets/images/' + p.image).removeClass('d-none');
                            } else {
                                $('#imagePreview').addClass('d-none');
                            }
                            
                            $('#addProductForm button[type="submit"]').prop('disabled', false).text('Simpan Produk');
                            $('#addProductModal').modal('show');
                        } else {
                            alert('Gagal mengambil data produk.');
                        }
                    },
                    error: function() {
                        alert('Terjadi kesalahan koneksi.');
                    }
                });
            };

            window.deleteProduct = function(id) {
                if(confirm('Apakah Anda yakin ingin menghapus produk ini?')) {
                    $.ajax({
                        url: '/api/products/delete.php',
                        method: 'POST',
                        data: { id: id },
                        success: function(response) {
                            if(response.success) {
                                alert('Produk berhasil dihapus.');
                                loadProducts(currentPage);
                            } else {
                                alert('Gagal: ' + response.message);
                            }
                        },
                        error: function(xhr) {
                            alert('Gagal: ' + (xhr.responseJSON?.message || xhr.statusText));
                        }
                    });
                }
            };

            // Expose to window for onclick handlers (already done for edit/delete above via window assignment)
            window.loadProducts = loadProducts;
            
            // Initial Load
            loadProducts(1);
        });
    </script>
  </body>
</html>
