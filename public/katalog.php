<?php require_once '../config/auth.php'; ?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link
    href="https://fonts.googleapis.com/css2?family=Playfair+Display+SC:ital,wght@0,400;0,700;0,900;1,400;1,700;1,900&family=Playfair:ital,opsz,wght@0,5..1200,300..900;1,5..1200,300..900&display=swap"
    rel="stylesheet" />
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" />
  <link rel="stylesheet" href="assets/css/style.css" />
  <link rel="icon" href="assets/images/favicon.png" type="image/png">
  <title>Katalog Bunga – Bloomify</title>
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
          <li><a href="katalog.php" class="active">Katalog Bunga</a></li>
          <li><a href="tentang.php">Tentang Kami</a></li>
          <?php if (isAdmin()) echo '<a href="admin/index.php">Admin Panel</a>'; ?>
        </ul>
        <div class="auth-buttons">
          <button class="icon-btn" aria-label="Cart" onclick="window.location.href='cart.php'">
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
  <main class="catalog-main">
    <div class="container">
      <section class="catalog-hero">
        <p class="catalog-hero-eyebrow">Our floral collection</p>
        <h1>Pilih Bunga Terbaik untuk Setiap Momen</h1>
        <p class="catalog-hero-text">
          Jelajahi koleksi buket dan rangkaian Bloomify yang dirancang khusus
          untuk ulang tahun, anniversary, wisuda, hingga ucapan terima kasih
          yang hangat.
        </p>
      </section>
      <section class="catalog-layout">
        <aside>
          <div class="catalog-sidebar-card">
            <h2 class="catalog-sidebar-title">Filters</h2>

            <div class="catalog-sidebar-section">
              <label class="catalog-sidebar-label" for="sidebar-search">
                Search
              </label>
              <div class="catalog-sidebar-search">
                <input type="text" id="sidebar-search" placeholder="Search flowers..." />
              </div>
            </div>

            <div class="catalog-sidebar-section">
              <span class="catalog-sidebar-label">Categories</span>
              <ul class="catalog-sidebar-list">
                <li>
                  <label>
                    <input type="checkbox" checked value="" />
                    All flowers
                  </label>
                </li>
              </ul>
            </div>

            <div class="catalog-sidebar-section">
              <span class="catalog-sidebar-label">Price range</span>
              <ul class="catalog-sidebar-list">
                <li>
                  <label>
                    <input type="radio" name="price" checked />
                    Any
                  </label>
                </li>
                <li>
                  <label>
                    <input type="radio" name="price" />
                    &lt; 300K
                  </label>
                </li>
                <li>
                  <label>
                    <input type="radio" name="price" />
                    300K – 500K
                  </label>
                </li>
                <li>
                  <label>
                    <input type="radio" name="price" />
                    &gt; 500K
                  </label>
                </li>
              </ul>
            </div>

            <button class="catalog-sidebar-apply">Apply filters</button>
          </div>
        </aside>
        <section class="catalog-content">
          <div class="catalog-topbar">
            <span class="catalog-topbar-info">Showing 8 products</span>
            <div class="catalog-topbar-sort">
              <label for="sortSelect">Sort by</label>
              <select id="sortSelect">
                <option>Featured</option>
                <option>Price: Low to High</option>
                <option>Price: High to Low</option>
                <option>Newest</option>
              </select>
            </div>
          </div>
          <div class="catalog-grid">
            <p>Loading...</p>
          </div>
        </section>
      </section>
    </div>
  </main>
  <footer class="footer">
    <div class="container">
      <div class="footer-top">
        <div class="footer-grid">
          <div class="footer-brand">
            <div class="footer-logo">
              <span class="footer-logo-icon">
                <i class="bi bi-flower1"></i>
              </span>
              <span class="footer-logo-text">Bloomify</span>
            </div>
            <p>
              Bringing beauty and joy through exquisite floral arrangements,
              crafted with love and delivered with care.
            </p>

            <div class="footer-social">
              <a href="#" aria-label="Instagram">
                <i class="bi bi-instagram"></i>
              </a>
              <a href="#" aria-label="Pinterest">
                <i class="bi bi-pinterest"></i>
              </a>
              <a href="#" aria-label="Facebook">
                <i class="bi bi-facebook"></i>
              </a>
            </div>
          </div>
          <div class="footer-column">
            <h4>Shop</h4>
            <ul>
              <li><a href="#">All Flowers</a></li>
              <li><a href="#">Bouquets</a></li>
              <li><a href="#">Arrangements</a></li>
              <li><a href="#">Occasions</a></li>
              <li><a href="#">Gift Sets</a></li>
            </ul>
          </div>
          <div class="footer-column">
            <h4>Customer Care</h4>
            <ul>
              <li><a href="#">My Account</a></li>
              <li><a href="#">Track Order</a></li>
              <li><a href="#">Delivery Info</a></li>
              <li><a href="#">Care Guide</a></li>
              <li><a href="#">Contact Us</a></li>
            </ul>
          </div>
          <div class="footer-column" style="position: relative;">
            <h4>Visit Our Store</h4>
            <div style="position: relative; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 12px rgba(0,0,0,0.1);">
              <a href="https://maps.app.goo.gl/hiUnazLaaeVSnYhG7" target="_blank" style="position: absolute; inset: 0; z-index: 10;" title="Open in Google Maps"></a>
              <iframe
                src="https://maps.google.com/maps?q=-7.788563203049172,110.36921160082893&hl=en&z=14&output=embed"
                width="100%"
                height="160"
                style="border:0; display: block;"
                allowfullscreen=""
                loading="lazy">
              </iframe>
            </div>
          </div>
        </div>

        <hr class="footer-divider" />

        <div class="footer-bottom">
          <p>© 2025 Bloomify. All rights reserved.</p>
          <div class="footer-bottom-links">
            <a href="#">Privacy Policy</a>
            <a href="#">Terms of Service</a>
            <a href="#">Returns</a>
          </div>
        </div>
      </div>
    </div>
  </footer>
  <script src="assets/js/script.js"></script>
  <script src="assets/js/katalog.js"></script>
</body>

</html>