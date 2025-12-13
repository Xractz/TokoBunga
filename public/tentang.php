<?php require_once '../config/auth.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Our Team - Bloomify</title>
  <link rel="icon" href="assets/images/favicon.png" type="image/png">


  <!-- FONT -->
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display+SC:wght@400;700&family=Playfair:wght@400;500;600&display=swap" rel="stylesheet">

  <!-- ICONS -->
  <link
    rel="stylesheet"
    href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css"
  />

  <!-- CSS MAIN KAMU -->
  <link rel="stylesheet" href="assets/css/style.css" />
  <link rel="stylesheet" href="assets/css/tentang.css" />
</head>

<body>

    <!-- ================= HEADER (baru di ubah bagian container navbar) ================= -->
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
            <li><a href="tentang.php">Tentang Kami</a></li>
            <?php if (isAdmin()) echo '<a href="admin/index.php">Admin Panel</a>'; ?>
          </ul>

          <!-- Tombol kanan -->
          <div class="auth-buttons">
            <!-- Keranjang -->
            <button
              class="icon-btn"
              aria-label="Cart"
              onclick="window.location.href='cart.php'"
            >
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
           <?php if (isCustomer()) echo '<a href="orders-history.php">Riwayat Pesanan</a>'; ?>
           <a href="/api/auth/logout.php">Logout</a>
        <?php else: ?>
           <a href="auth/login.php">Login</a>
           <a href="auth/register.php">Register</a>
        <?php endif; ?>
      </div>
    </header>

        <!-- ========== ABOUT HERO ========== -->
  <section class="about-hero">
    <div class="about-hero-banner">
      <div class="about-hero-overlay container">
        <p class="about-hero-kicker">About Bloomify Floral</p>
        <h1 class="about-hero-title">
          Where passion meets petals, creating memorable moments through the art of flowers.
        </h1>
      </div>
    </div>
  </section>

  <!-- ========== OUR STORY ========== -->
  <section class="about-story">
    <div class="container about-story-grid">
      <div class="about-story-text">
        <p class="about-label">Our Story</p>
        <h2 class="about-heading">Blooming Since 2014</h2>
        <p class="about-paragraph">
          Bloomify Floral began with a simple dream: to bring the beauty and joy of fresh flowers
          into every home and celebration. What started as a small neighborhood flower shop has
          blossomed into a trusted name in floral design.
        </p>
        <p class="about-paragraph">
          Our founders and designers combine a deep love for nature with an artistic vision to
          create arrangements that speak to the heart. Every bouquet tells a story, every petal
          carries emotion, and every arrangement is crafted with care.
        </p>
        <p class="about-paragraph">
          Today, we continue to uphold the same values that started it all: quality, creativity,
          and a genuine passion for making people smile through timeless floral beauty.
        </p>
      </div>

      <div class="about-story-media">
        <div class="store-card">
         <img src="assets/images/hero_bunga.jpg" alt="Bloomify Floral Store" class="store-image" />

         <div class="store-info">
            <p class="store-address">Jl. Melati Raya No. 24, Yogyakarta</p>
            <p class="store-hours">Buka setiap hari • 08.00–21.00</p>
    </div>
  </div>
</div>

    </div>
  </section>

  <!-- ========== STATS + VALUES ========== -->
  <section class="about-stats-values">
    <!-- STATS STRIP -->
    <div class="about-stats-strip">
      <div class="container about-stats-row">
        <div class="about-stat">
          <div class="about-stat-number">10+</div>
          <div class="about-stat-label">Years in Business</div>
        </div>
        <div class="about-stat">
          <div class="about-stat-number">5000+</div>
          <div class="about-stat-label">Happy Customers</div>
        </div>
        <div class="about-stat">
          <div class="about-stat-number">50+</div>
          <div class="about-stat-label">Flower Varieties</div>
        </div>
        <div class="about-stat">
          <div class="about-stat-number">24/7</div>
          <div class="about-stat-label">Customer Support</div>
        </div>
      </div>    
    </div>

    <!-- VALUES CARDS -->
    <div class="about-values">
      <div class="container">
        <div class="values-header">
          <p class="about-label">Our Values</p>
          <h2 class="about-heading">What We Stand For</h2>
          <p class="values-description">
            These core values guide everything we do, from selecting blooms to serving our customers.
          </p>
        </div>

        <div class="values-grid">
          <article class="value-card">
            <h3>Passion</h3>
            <p>
              We pour our heart into every arrangement, treating each order as a work of art.
            </p>
          </article>

          <article class="value-card">
            <h3>Quality</h3>
            <p>
              Only the freshest, premium blooms make it into our bouquets and arrangements.
            </p>
          </article>

          <article class="value-card">
            <h3>Customer Care</h3>
            <p>
              Your satisfaction is our priority. We go above and beyond for every customer.
            </p>
          </article>

          <article class="value-card">
            <h3>Sustainability</h3>
            <p>
              We source responsibly and support eco-friendly practices in floristry.
            </p>
          </article>
        </div>
      </div>
    </div>
  </section>
  <!-- ========== OUR TEAM SECTION ========== -->
  <section class="team-section" id="our-team">
    <div class="container">

      <!-- HEADER -->
      <div class="team-header">
        <p class="team-subtitle section-subtitle">Our Team</p>
        <h2 class="team-title section-title">Meet Our Expert Florists</h2>
        <p class="team-description">
          Talented artisans dedicated to bringing beauty and joy through floral design.
        </p>
      </div>

      <!-- GRID -->
      <div class="team-grid">

        <!-- CARD 1 -->
        <div class="team-card-wrapper">

          <!-- TOOLTIP -->
          <div class="team-tooltip">
            <img src="assets/images/Monik.jpg" alt="Selly Monica" class="team-tooltip-avatar">
            <div class="team-tooltip-info">
              <h4>Selly Monica</h4>
              <p>Lead Floral Designer • 15+ years</p>
            </div>
          </div>

          <!-- CARD -->
          <article class="team-card">
            <img src="assets/images/Monik.jpg" alt="Selly Monica" class="team-img">
            <div class="team-info">
              <h3 class="team-name">Selly Monica</h3>
              <p class="team-role">Lead Floral Designer</p>
              <p class="team-bio">15+ years of floral experience.</p>
            </div>
          </article>

        </div>

        <!-- CARD 2 -->
        <div class="team-card-wrapper">

          <div class="team-tooltip">
            <img src="assets/images/Sam.jpg" alt="Samuel" class="team-tooltip-avatar">
            <div class="team-tooltip-info">
              <h4>Samuel Christaura G.</h4>
              <p>Wedding Floral Expert</p>
            </div>
          </div>

          <article class="team-card">
            <img src="assets/images/Sam.jpg" alt="Samuel" class="team-img">
            <div class="team-info">
              <h3 class="team-name">Samuel Christaura G.</h3>
              <p class="team-role">Wedding Floral Expert</p>
              <p class="team-bio">Specializing in bridal décor.</p>
            </div>
          </article>

        </div>

        <!-- CARD 3 -->
        <div class="team-card-wrapper">

          <div class="team-tooltip">
            <img src="assets/images/Wili.jpg" alt="William" class="team-tooltip-avatar">
            <div class="team-tooltip-info">
              <h4>William Luvianus</h4>
              <p>Corporate Florist</p>
            </div>
          </div>

          <article class="team-card">
            <img src="assets/images/Wili.jpg" alt="William" class="team-img">
            <div class="team-info">
              <h3 class="team-name">William Luvianus</h3>
              <p class="team-role">Corporate Florist</p>
              <p class="team-bio">Event floral specialist.</p>
            </div>
          </article>

        </div>

        <!-- CARD 4 -->
        <div class="team-card-wrapper">

          <div class="team-tooltip">
            <img src="assets/images/Rendra.jpg" alt="Cristensen" class="team-tooltip-avatar">
            <div class="team-tooltip-info">
              <h4>Cristensen Rendra P.</h4>
              <p>Creative Artist</p>
            </div>
          </div>

          <article class="team-card">
            <img src="assets/images/Rendra.jpg" alt="Cristensen" class="team-img">
            <div class="team-info">
              <h3 class="team-name">Cristensen Rendra P.</h3>
              <p class="team-role">Creative Artist</p>
              <p class="team-bio">Unique floral artistry.</p>
            </div>
          </article>

        </div>

        <!-- CARD 5 -->
        <div class="team-card-wrapper">

          <div class="team-tooltip">
            <img src="assets/images/jojo.jpg" alt="Jonathan" class="team-tooltip-avatar">
            <div class="team-tooltip-info">
              <h4>Jonathan Immanuel S.</h4>
              <p>Modern Stylist</p>
            </div>
          </div>

          <article class="team-card">
            <img src="assets/images/jojo.jpg" alt="Jonathan" class="team-img">
            <div class="team-info">
              <h3 class="team-name">Jonathan Immanuel S.</h3>
              <p class="team-role">Modern Stylist</p>
              <p class="team-bio">Contemporary floral style.</p>
            </div>
          </article>

        </div>

      </div>
    </div>
  </section>
<!-- ================= FOOTER (SAMA SEPERTI KATALOG) ================= -->
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
            <!-- Map Location -->
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

    <script src="assets/js/style.js"></script>
    <script src="assets/js/tentang.js"></script>
  </body>
</html>
