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
  </head>
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
              <button type="button" class="btn-primary">
                <i class="bi bi-plus-lg"></i> Tambah Produk
              </button>
            </div>
            <p class="section-description">
              Daftar produk yang tersedia di toko.
            </p>

            <div class="table-responsive">
              <table
                class="table table-hover table-responsive align-middle mb-0"
              >
                <thead class="table-light">
                  <tr>
                    <th>ID</th>
                    <th>Nama Produk</th>
                    <th>Kategori</th>
                    <th>Harga</th>
                    <th>Stok</th>
                    <th style="width: 120px">Aksi</th>
                  </tr>
                </thead>
                <tbody>
                  <tr>
                    <td>PR001</td>
                    <td>Buket Mawar Manis</td>
                    <td>Buket</td>
                    <td>Rp250.000</td>
                    <td>10</td>
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
                    <td>PR002</td>
                    <td>Box Bunga Ulang Tahun</td>
                    <td>Gift Box</td>
                    <td>Rp350.000</td>
                    <td>5</td>
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

    <!-- Bootstrap JS (optional, tapi bagus buat future: modal, dropdown, dll.) -->
    <script
      src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
      integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
      crossorigin="anonymous"
    ></script>

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
    </script>
  </body>
</html>
