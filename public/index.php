<?php require_once '../config/auth.php'; ?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />

  <!-- Google Fonts -->
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link
    href="https://fonts.googleapis.com/css2?family=Playfair+Display+SC:ital,wght@0,400;0,700;0,900;1,400;1,700;1,900&family=Playfair:ital,opsz,wght@0,5..1200,300..900;1,5..1200,300..900&display=swap"
    rel="stylesheet" />

  <!-- Bootstrap Icons -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" />

  <!-- Main CSS -->
  <link rel="stylesheet" href="assets/css/style.css" />
  <title>Toko Bunga</title>
</head>

<body>
  <header class="header">
    <div class="container">
      <!-- NAVBAR -->
      <nav class="navbar">
        <!-- Logo -->
        <div class="logo">
          <i class="bi bi-flower1"></i>
          <h1>Bloomify</h1>
        </div>

        <!-- Menu utama (desktop) -->
        <ul class="menu">
          <li><a href="index.php" class="active">Home</a></li>
          <li><a href="katalog.php">Katalog Bunga</a></li>
          <li><a href="tentang.html">Tentang Kami</a></li>
        </ul>

        <!-- Tombol kanan -->
        <div class="auth-buttons">
          <!-- Keranjang -->
          <button class="icon-btn" aria-label="Cart" onclick="window.location.href='cart.php'">
            <i class="bi bi-bag"></i>

            <!-- Badge pink kecil -->
            <span id="cartCount" class="cart-badge hidden">0</span>
          </button>

          <!-- Hamburger (untuk auth: login / register / logout) -->
          <button class="hamburger-menu" id="hamburgerBtn">
            <i class="bi bi-list"></i>
          </button>
        </div>
      </nav>

      <!-- MENU AUTH + NAV MOBILE (muncul saat hamburger diklik) -->
     <div class="mobile-menu" id="mobileMenu">
        <?php if (isLoggedIn()): ?>
           <a href="profile.php">Profile</a>
           <a href="orders-history.php">Riwayat Pesanan</a>
           <a href="/api/auth/logout.php">Logout</a>
        <?php else: ?>
           <a href="auth/login.php">Login</a>
           <a href="auth/register.php">Register</a>
        <?php endif; ?>
      </div>

      <!-- HERO -->
      <section class="hero">
        <div class="hero-card">
          <div class="hero-image"></div>
          <div class="hero-text">
            <h1>Bloomify, Crafted With Loved</h1>
            <p>
              Fresh handcrafted bouquets arranged with love for every special
              occasion.
            </p>
            <div class="hero-buttons">
              <a href="/katalog.php" class="btn-secondary">Lihat Katalog</a>
            </div>
          </div>
        </div>
      </section>
    </div>
  </header>

  <main>
    <!-- FEATURES -->
    <section class="features">
      <div class="container">
        <h2 class="section-title">Shop by occasion</h2>
        <p class="section-subtitle">
          Find the perfect flowers for every special moment
        </p>

        <div class="features-grid">
          <div class="feature-card">
            <i class="bi bi-gift"></i>
            <p>Birthday</p>
          </div>
          <div class="feature-card">
            <i class="bi bi-heart"></i>
            <p>Wedding</p>
          </div>
          <div class="feature-card">
            <i class="bi bi-balloon-heart"></i>
            <p>Anniversary</p>
          </div>
          <div class="feature-card">
            <i class="bi bi-flower1"></i>
            <p>Sympathy</p>
          </div>
        </div>
      </div>
    </section>

    <!-- KOLEKSI -->
    <section class="collection">
      <div class="container">
        <h2 class="section-title">Koleksi Terlaris</h2>

        <div class="products-grid">
        </div>

        <div class="view-all-wrapper">
          <a href="katalog.php" class="view-all-btn">View All Product</a>
        </div>
      </div>
    </section>
  </main>

  <!-- WHY -->
  <section class="why">
    <div class="container">
      <div class="why-box">
        <h2 class="section-title">Why Choose Bloomora</h2>
        <p class="section-subtitle">
          We're committed to excellence in every bloom
        </p>

        <div class="why-grid">
          <div class="why-item">
            <div class="why-icon-premium">
              <i class="bi bi-flower3"></i>
            </div>
            <h3>Fresh & Premium</h3>
            <p>
              Only the finest, freshest flowers sourced from trusted growers
            </p>
          </div>

          <div class="why-item">
            <div class="why-icon-delivery">
              <i class="bi bi-truck"></i>
            </div>
            <h3>Same-Day Delivery</h3>
            <p>Fast and reliable delivery to make your moments special</p>
          </div>

          <div class="why-item">
            <div class="why-icon-expert">
              <i class="bi bi-award"></i>
            </div>
            <h3>Expert Florists</h3>
            <p>Crafted by skilled artisans with years of experience</p>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- FOOTER -->
  <footer class="footer">
    <div class="container">
      <div class="footer-top">
        <div class="footer-grid">
          <!-- Brand + text + sosmed -->
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

          <!-- Shop -->
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

          <!-- Customer Care -->
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

          <!-- Stay Connected -->
          <div class="footer-column footer-newsletter">
            <h4>Stay Connected</h4>
            <p>
              Subscribe to get special offers, free giveaways, and floral
              inspiration.
            </p>
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

  <!-- JS -->
  <script src="assets/js/script.js"></script>
  <script src="assets/js/home.js"></script>
</body>

</html>