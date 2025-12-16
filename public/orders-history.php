<?php
require_once __DIR__ . '/../config/auth.php';
requireCustomer();
?>
<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <link rel="icon" href="assets/images/favicon.png" type="image/png">
  <title>Riwayat Pesanan - Bloomify</title>
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link
    href="https://fonts.googleapis.com/css2?family=Playfair+Display+SC:wght@400;700&family=Playfair:wght@400;500;600&display=swap"
    rel="stylesheet" />
  <link
    rel="stylesheet"
    href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" />
  <link rel="stylesheet" href="assets/css/style.css" />

  <style>
    #paginationContainer {
      font-family: inherit;
    }

    #paginationContainer button {
      cursor: pointer;
      transition: all 0.2s;
    }

    #paginationContainer button:disabled {
      opacity: 0.5;
      cursor: not-allowed;
    }

    .pagination-numbers {
      display: flex;
      align-items: center;
      gap: 0.25rem;
    }
  </style>
</head>

<body>
  <header class="header">
    <div class="container">
      <nav class="navbar">
        <div class="logo">
          <i class="bi bi-flower1"></i>
          <h1>Bloomify</h1>
        </div>

        <ul class="menu">
          <li><a href="index.php">Home</a></li>
          <li><a href="katalog.php">Katalog Bunga</a></li>
          <li><a href="tentang.php">Tentang Kami</a></li>
          <?php if (isAdmin()) echo '<a href="admin/index.php">Admin Panel</a>'; ?>
        </ul>

        <div class="auth-buttons">
          <button
            class="icon-btn"
            aria-label="Cart"
            onclick="window.location.href='cart.php'">
            <i class="bi bi-bag"></i>
            <span id="cartCount" class="cart-badge hidden">0</span>
          </button>

          <button class="hamburger-menu" id="hamburgerBtn">
            <i class="bi bi-list"></i>
          </button>
        </div>
      </nav>

      <div class="mobile-menu" id="mobileMenu">
        <?php if (isLoggedIn()): ?>
          <a href="profile.php">Profile</a>
          <?php if (isCustomer()) echo '<a href="orders-history.php">Riwayat Pesanan</a>'; ?>
          <a href="/api/auth/logout.php">Logout</a>
        <?php else: ?>
          <a href="auth/login.php">Login</a>
          <a href="auth/register.php">Register</a>
        <?php endif; ?>
      </div>
    </div>
  </header>
  <main class="page-orders">
    <div class="container">
      <div class="orders-header">
        <h2>Riwayat Pesanan</h2>
        <p>Lihat daftar pesanan yang pernah kamu buat di Bloomify.</p>
      </div>
      <div class="orders-empty" id="emptyState" style="display: none;">
        <i class="bi bi-bag-x"></i>
        <h3>Belum ada pesanan</h3>
        <p>Ayo mulai kirim buket bunga untuk orang tersayang 💐</p>
        <a href="katalog.php" class="btn btn-primary">Belanja Sekarang</a>
      </div>
      <div id="contentWrapper" style="display: none;">
        <section class="orders-table-wrapper">
          <div class="orders-table-header">
            <h3>Daftar Pesanan</h3>
          </div>

          <div class="orders-table-scroll">
            <table class="orders-table">
              <thead>
                <tr>
                  <th>No. Pesanan</th>
                  <th>Tanggal</th>
                  <th>Status Pesanan</th>
                  <th>Status Pembayaran</th>
                  <th>Total</th>
                  <th>Detail</th>
                </tr>
              </thead>
              <tbody id="ordersListContainer">
              </tbody>
            </table>
          </div>
        </section>
        <section class="orders-cards" id="ordersCardsContainer">
        </section>
        <div id="paginationContainer" style="display: none; justify-content: center; align-items: center; gap: 1rem; margin-top: 2rem; flex-wrap: wrap;">
        </div>

      </div>
    </div>
  </main>
  <footer class="footer">
    <div class="container">
      <p>&copy; 2025 Bloomify. All rights reserved.</p>
    </div>
  </footer>
  <script src="assets/js/script.js"></script>
  <script src="assets/js/orders-history.js"></script>
</body>

</html>