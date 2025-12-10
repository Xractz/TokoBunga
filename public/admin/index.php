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
          <div class="admin-section" id="section-kategori" style="display: none">
            <div class="section-header-row">
              <h2 class="mb-0">Kategori</h2>
              <button type="button" class="btn-primary" data-bs-toggle="modal" data-bs-target="#addCategoryModal" onclick="prepareAddCategory()">
                <i class="bi bi-plus-lg"></i> Tambah Kategori
              </button>
            </div>
            <p class="section-description">Kelola kategori produk bunga.</p>

            <div class="table-responsive">
              <table class="table table-hover table-responsive align-middle mb-0">
                <thead class="table-light">
                  <tr>
                    <th>No</th>
                    <th>Nama Kategori</th>
                    <th>Status</th>
                    <th style="width: 120px">Aksi</th>
                  </tr>
                </thead>
                <tbody id="categories-table-body">
                  <!-- JS Populated -->
                </tbody>
              </table>
            </div>

            <!-- Pagination Container -->
            <nav class="d-flex justify-content-end mt-3">
                <ul class="pagination" id="category-pagination-controls">
                    <!-- Javascript will populate this -->
                </ul>
            </nav>
          </div>

          <!-- ============== SECTION: LOG TRANSAKSI ============== -->
          <div class="admin-section" id="section-transaksi" style="display: none">
            <div class="row mb-4">
                <div class="col-12">
                     <div class="card border-0 shadow-sm">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h5 class="card-title mb-0">Rangkuman Transaksi</h5>
                                <div class="btn-group" role="group" aria-label="Filter Periode">
                                    <button type="button" class="btn btn-outline-primary active" onclick="loadStats('week')" id="btn-week">Minggu Ini</button>
                                    <button type="button" class="btn btn-outline-primary" onclick="loadStats('month')" id="btn-month">Bulan Ini</button>
                                    <button type="button" class="btn btn-outline-primary" onclick="loadStats('year')" id="btn-year">Tahun Ini</button>
                                </div>
                            </div>
                            <div class="row text-center">
                                <div class="col-md-6 mb-3 mb-md-0 border-end">
                                    <h6 class="text-muted">Total Pendapatan</h6>
                                    <h3 class="fw-bold text-success" id="stat-revenue">Rp0</h3>
                                </div>
                                <div class="col-md-6">
                                    <h6 class="text-muted">Total Transaksi</h6>
                                    <h3 class="fw-bold" id="stat-transactions">0</h3>
                                </div>
                            </div>
                        </div>
                     </div>
                </div>
            </div>

            <div class="section-header-row">
              <h2 class="mb-0">Log Transaksi</h2>
            </div>
            <p class="section-description">
              Riwayat pesanan pelanggan. Klik tombol detail untuk melihat rincian pesanan.
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
                    <th style="width: 120px">Aksi</th>
                  </tr>
                </thead>
                <tbody id="transactions-table-body">
                  <!-- JS Populated -->
                </tbody>
              </table>
            </div>

            <!-- Pagination -->
            <nav class="d-flex justify-content-end mt-3">
                <ul class="pagination" id="transaction-pagination-controls"></ul>
            </nav>
          </div>

          <!-- ============== SECTION: CUSTOMER ============== -->
          <div class="admin-section" id="section-customer" style="display: none">
            <div class="section-header-row">
              <h2 class="mb-0">Customer / Pelanggan <span id="totalCustomers" class="badge bg-primary ms-2" style="font-size: 0.8rem">Total: 0</span></h2>
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
                <tbody id="customers-table-body">
                  <!-- JS Populated -->
                </tbody>
              </table>
            </div>
            
            <!-- Pagination -->
            <nav class="d-flex justify-content-end mt-3">
                <ul class="pagination" id="customer-pagination-controls"></ul>
            </nav>
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

    <!-- MODAL ADD CATEGORY -->
    <div class="modal fade" id="addCategoryModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="categoryModalTitle">Tambah Kategori</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="addCategoryForm">
                        <input type="hidden" id="categoryId" name="id">
                        <div class="mb-3">
                            <label for="categoryName" class="form-label">Nama Kategori</label>
                            <input type="text" class="form-control" id="categoryName" name="name" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="categoryDesc" class="form-label">Deskripsi</label>
                            <textarea class="form-control" id="categoryDesc" name="description" rows="3"></textarea>
                        </div>
                        
                        <div class="mb-3 form-check form-switch">
                            <input class="form-check-input" type="checkbox" role="switch" id="categoryStatus" name="is_active" value="1" checked>
                            <label class="form-check-label" for="categoryStatus">Aktif</label>
                        </div>

                        <div class="modal-footer px-0 pb-0">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-primary">Simpan Kategori</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- MODAL DETAIL TRANSAKSI -->
    <div class="modal fade" id="transactionDetailModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Detail Transaksi <span id="detailOrderCode"></span></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6>Informasi Penerima</h6>
                            <p class="mb-1"><strong>Nama:</strong> <span id="detailRecipientName"></span></p>
                            <p class="mb-1"><strong>Telepon:</strong> <span id="detailRecipientPhone"></span></p>
                            <p class="mb-1"><strong>Alamat:</strong> <span id="detailAddress"></span></p>
                            <p class="mb-1"><strong>Waktu Kirim:</strong> <span id="detailDeliveryTime"></span></p>
                        </div>
                        <div class="col-md-6">
                             <h6>Informasi Pesanan</h6>
                             <p class="mb-1"><strong>Status:</strong> <span id="detailStatus"></span></p>
                             <p class="mb-1"><strong>Pembayaran:</strong> <span id="detailPaymentStatus"></span></p>
                             <p class="mb-1"><strong>Pesan Kartu:</strong> <span id="detailCardMessage"></span></p>
                        </div>
                    </div>
                    <hr>
                    <h6>Item Pesanan</h6>
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead><tr><th>Produk</th><th>Harga</th><th>Qty</th><th>Total</th></tr></thead>
                            <tbody id="detailItemsTable"></tbody>
                        </table>
                    </div>
                     <div class="d-flex justify-content-end">
                        <strong>Grand Total: <span id="detailGrandTotal"></span></strong>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- MODAL UPDATE STATUS -->
    <div class="modal fade" id="updateStatusModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Update Status Transaksi</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="updateStatusForm">
                        <input type="hidden" id="statusOrderId" name="id">
                        <div class="mb-3">
                            <label for="statusSelect" class="form-label">Status Baru</label>
                            <select class="form-select" id="statusSelect" name="status" required>
                                <option value="pending">Pending</option>
                                <option value="confirmed">Confirmed</option>
                                <option value="processing">Processing</option>
                                <option value="shipped">Shipped</option>
                                <option value="completed">Completed</option>
                                <option value="cancelled">Cancelled</option>
                            </select>
                        </div>
                        <div class="modal-footer px-0 pb-0">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-primary">Simpan Status</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>

    <!-- Bootstrap JS -->
    <script
      src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
      integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
      crossorigin="anonymous"
    ></script>
    <!-- Select2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <!-- Custom Admin JS -->
    <script src="../assets/js/dashboard-admin.js"></script>
  </body>
</html>
