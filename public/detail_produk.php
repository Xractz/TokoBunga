<?php
require_once __DIR__ . '/../config/auth.php';
requireCustomer();
?>
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
      rel="stylesheet"
    />

    <!-- Bootstrap Icons -->
    <link
      rel="stylesheet"
      href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css"
    />

    <!-- Main CSS -->
    <link rel="stylesheet" href="assets/css/style.css" />
    <title>Garden Rose Bouquet – Bloomify</title>
  </head>
  <body>
    <!-- ================= HEADER (SAMA SEPERTI INDEX & KATALOG) ================= -->
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
            <li><a href="index.php">Home</a></li>
            <li><a href="katalog.php" class="active">Katalog Bunga</a></li>
            <li><a href="tentang.html">Tentang Kami</a></li>
          </ul>

          <!-- Tombol kanan -->
          <div class="auth-buttons">
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

        <!-- MENU AUTH (MUNCUL SAAT HAMBURGER DIKLIK) -->
        <div class="mobile-menu" id="mobileMenu">
          <?php if (isLoggedIn()): ?>
            <a href="profile.php">Profile</a>
            <a href="/api/auth/logout.php">Logout</a>
          <?php else: ?>
            <a href="auth/login.php">Login</a>
            <a href="auth/register.php">Register</a>
          <?php endif; ?>
        </div>
      </div>
    </header>

    <!-- ================= MAIN DETAIL PRODUK ================= -->
    <main class="product-detail-main">
      <div class="container">
        <!-- breadcrumb + judul -->
        <section class="product-detail-header">
          <p class="breadcrumb">
            <a href="index.php">Home</a>
            <span>/</span>
            <a href="katalog.php">Katalog Bunga</a>
            <span>/</span>
            <span>Garden Rose Bouquet</span>
          </p>
          <h1>Garden Rose Bouquet</h1>
          <p class="product-detail-tagline">
            12 tangkai mawar merah premium dengan wrapping elegan untuk momen
            anniversary, ulang tahun, atau ucapan terima kasih istimewa.
          </p>
        </section>

        <!-- dua kolom: gambar kiri, info kanan -->
        <section class="product-detail-layout">
          <!-- KIRI: gambar utama + thumbnail -->
          <div class="product-detail-media">
            <div class="product-detail-image-main">
              <img
                src="assets/images/gardenmix.jpg"
                alt="Garden Rose Bouquet"
              />
            </div>
          </div>

          <!-- KANAN: info produk -->
          <div class="product-detail-info">
            <span class="product-detail-label" id="detail-category">Fresh bouquet</span>
            <h2 class="product-detail-title" id="detail-name">Loading...</h2>
            <p class="product-detail-short" id="detail-description">
              ...
            </p>

            <div class="product-detail-price-row">
              <span class="product-detail-price" id="detail-price">Rp 0</span>
              <span class="product-detail-badge">Best seller</span>
            </div>

            <ul class="product-detail-meta">
              <li>• Estimasi tinggi rangkaian: ± 45–50 cm</li>
              <li>• Termasuk kartu ucapan gratis</li>
              <li>• Same day delivery untuk area kota</li>
              <li>• Stok: <span id="detail-stock">Tersedia</span></li>
            </ul>

            <!-- opsi -->
            <div class="product-detail-options">
              <div class="product-detail-options-row">
                <label for="sizeSelect">Size</label>
                <select id="sizeSelect">
                  <option value="standard">Standard (12 tangkai)</option>
                  <option value="medium">Medium (18 tangkai)</option>
                  <option value="large">Large (24 tangkai)</option>
                </select>
              </div>

              <div class="product-detail-options-row">
                <label for="colorSelect">Wrapping color</label>
                <select id="colorSelect">
                  <option value="nude">Nude beige</option>
                  <option value="white">Soft white</option>
                  <option value="pink">Blush pink</option>
                </select>
              </div>
            </div>

            <!-- aksi -->
            <div class="product-detail-actions">
              <!-- Qty selector -->
              <div class="product-detail-qty">
                <button type="button" onclick="changeQty(-1)">−</button>
                <input
                  type="text"
                  id="qtyInput"
                  value="1"
                  aria-label="Quantity"
                />
                <button type="button" onclick="changeQty(1)">+</button>
              </div>

              <!-- TOMBOL ADD TO CART (tetap teks) -->
              <button
                id="addToCartBtn"
                class="product-detail-btn-primary"
                type="button"
              >
                <i class="bi bi-bag" style="margin-right: 6px"></i>
                Add to cart
              </button>
            </div>

            <div class="product-detail-description">
              <h3>Flower care</h3>
              <p>
                Simpan buket di ruangan sejuk, jauhkan dari sinar matahari
                langsung dan sumber panas. Jika memungkinkan, potong sedikit
                ujung batang dan masukkan ke dalam vas berisi air bersih untuk
                mempertahankan kesegaran lebih lama.
              </p>
            </div>
          </div>
        </section>

        <!-- rekomendasi di bawah -->
        <section class="detail-recommend">
          <h2 class="detail-recommend-title">You may also like</h2>

          <div class="detail-recommend-grid">
            <!-- Card 1 -->
            <a href="detail_produk.html" class="detail-recommend-card">
              <img src="assets/images/sunrise.jpg" alt="Soft Pastel Bouquet" />
              <div class="detail-recommend-body">
                <h4>Soft Pastel Bouquet</h4>
                <p>Pastel mix • Gentle look</p>
                <span>Rp 510.000</span>
              </div>
            </a>

            <!-- Card 2 -->
            <a href="detail_produk.html" class="detail-recommend-card">
              <img src="assets/images/liliy.jpg" alt="Peony Dream" />
              <div class="detail-recommend-body">
                <h4>Peony Dream</h4>
                <p>Gift box • Blush tones</p>
                <span>Rp 480.000</span>
              </div>
            </a>

            <!-- Card 3 -->
            <a href="detail_produk.html" class="detail-recommend-card">
              <img src="assets/images/sunflowers.jpg" alt="Sunny Days" />
              <div class="detail-recommend-body">
                <h4>Sunny Days</h4>
                <p>Bright & cheerful</p>
                <span>Rp 480.000</span>
              </div>
            </a>
          </div>
        </section>
      </div>
    </main>

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

    <!-- JS (PAKE PUNYA KAMU) -->
    <script src="assets/js/script.js"></script>
    <!-- Custom JS for this page -->
    <script src="assets/js/cart_actions.js"></script>
    <script src="assets/js/detail_produk.js"></script>
  </body>
</html>
