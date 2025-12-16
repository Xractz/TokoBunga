<?php
require_once __DIR__ . '/../config/auth.php';
requireCustomer();
require_once __DIR__ . '/../config/db.php';
?>
<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <link rel="icon" href="assets/images/favicon.png" type="image/png">
  <title>Detail Pesanan - Bloomify</title>
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link
    href="https://fonts.googleapis.com/css2?family=Playfair+Display+SC:wght@400;700&family=Playfair:wght@400;500;600&display=swap"
    rel="stylesheet" />
  <link
    rel="stylesheet"
    href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" />
  <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
  <link rel="stylesheet" href="assets/css/style.css" />
  <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>

  <style>
    .detail-value {
      font-weight: 500;
      color: #333;
      display: block;
      margin-top: 0.25rem;
    }

    .detail-label {
      font-size: 0.85rem;
      color: #777;
      text-transform: uppercase;
      letter-spacing: 0.5px;
    }

    .detail-row {
      margin-bottom: 1.25rem;
    }

    .checkout-items-list {
      margin-top: 1rem;
      border-top: 1px solid #eee;
    }

    .item-row {
      display: flex;
      align-items: center;
      gap: 1rem;
      padding: 1rem 0;
      border-bottom: 1px solid #eee;
    }

    .item-thumb {
      width: 60px;
      height: 60px;
      object-fit: cover;
      border-radius: 8px;
      background: #f8f8f8;
    }

    .item-info {
      flex: 1;
    }

    .item-name {
      font-weight: 600;
      font-size: 0.95rem;
      margin: 0;
    }

    .item-meta {
      font-size: 0.85rem;
      color: #777;
      margin: 0.25rem 0 0;
    }

    .item-price {
      font-weight: 600;
      color: #44332b;
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

  <main class="checkout-main">
    <div class="container checkout-layout">
      <section class="checkout-left">
        <div style="display: flex; align-items: center; gap: 1rem; margin-bottom: 0.5rem;">
          <a href="orders-history.php" class="btn-link"><i class="bi bi-arrow-left"></i> Kembali</a>
        </div>
        <h1 class="checkout-title">Detail Pesanan</h1>
        <p class="checkout-subtitle" id="orderCodeDisplay">Loading...</p>

        <div class="checkout-card">
          <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
            <h2 class="checkout-section-title" style="margin: 0;">Status</h2>
            <span id="statusBadge" class="badge">Loading...</span>
          </div>
        </div>
        <div class="checkout-card">
          <h2 class="checkout-section-title">Contact Information</h2>
          <div class="checkout-row-2">
            <div class="detail-row">
              <span class="detail-label">Recipient Name</span>
              <span class="detail-value" id="recipientName">-</span>
            </div>
            <div class="detail-row">
              <span class="detail-label">Phone Number</span>
              <span class="detail-value" id="recipientPhone">-</span>
            </div>
          </div>
        </div>
        <div class="checkout-card">
          <h2 class="checkout-section-title">Delivery Details</h2>

          <div class="detail-row">
            <span class="detail-label">Address</span>
            <span class="detail-value" id="shippingAddress">-</span>
          </div>

          <div class="checkout-row-2">
            <div class="detail-row">
              <span class="detail-label">Date</span>
              <span class="detail-value" id="deliveryDate">-</span>
            </div>
            <div class="detail-row">
              <span class="detail-label">Time</span>
              <span class="detail-value" id="deliveryTime">-</span>
            </div>
          </div>
          <div class="checkout-field" style="margin-top: 1rem;" id="mapContainer">
            <span class="detail-label">Pinned Location</span>
            <div id="map" style="height: 250px; width: 100%; border-radius: 10px; border: 1px solid #eee; margin-top: 0.5rem; z-index: 1;"></div>
          </div>
        </div>
        <div class="checkout-card" id="cardMessageContainer" style="display: none;">
          <h2 class="checkout-section-title">Card Message</h2>
          <div class="detail-row">
            <p id="cardMessage" style="font-style: italic; color: #555; background: #fdfdfd; padding: 1rem; border-radius: 8px; border: 1px dashed #ddd;">
              -
            </p>
          </div>
        </div>
      </section>
      <aside class="cart-summary checkout-summary">
        <h2 class="cart-summary-title">Order Summary</h2>

        <div class="checkout-items-list" id="itemsList">
          <p style="padding: 1rem; color: #777;">Loading items...</p>
        </div>

        <div class="cart-summary-row" style="margin-top: 1rem;">
          <span>Subtotal</span>
          <span id="subtotalDisplay">Rp 0</span>
        </div>

        <div class="cart-summary-row">
          <span>Shipping Fee</span>
          <span id="shippingDisplay">Rp 0</span>
        </div>

        <hr class="cart-summary-divider" />

        <div class="cart-summary-total">
          <span>Total</span>
          <span id="grandTotalDisplay">Rp 0</span>
        </div>


        <div class="summary-actions" style="margin-top: 1.5rem;">
          <a href="#" id="exportInvoiceBtn" class="checkout-btn" style="text-align: center; background-color: #6c757d; display: none;">Export Invoice (PDF)</a>
        </div>

        <div class="summary-actions" id="paymentActionContainer" style="margin-top: 0.5rem; display: none;">
          <a href="#" id="payBtn" class="checkout-btn" style="text-align: center;">Lanjutkan Pembayaran</a>
        </div>

      </aside>
    </div>
  </main>
  <footer class="footer">
    <div class="container">
      <p>&copy; 2025 Bloomify. All rights reserved.</p>
    </div>
  </footer>


  <script src="assets/js/script.js"></script>
  <script>
    const APP_URL = "<?php
                      $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
                      $host = $_SERVER['HTTP_HOST'];
                      echo $protocol . '://' . $host;
                      ?>";
    const PAKASIR_SLUG = "<?php
                          require_once __DIR__ . '/../config/config.php';
                          global $env;
                          echo $env['PAKASIR_SLUG'] ?? '';
                          ?>";
    const PAKASIR_API_URL = "<?php echo $env['PAKASIR_API_URL'] ?? 'https://app.pakasir.com'; ?>";
  </script>

  <script src="assets/js/order-detail.js"></script>
</body>

</html>