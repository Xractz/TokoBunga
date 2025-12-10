<?php
require_once __DIR__ . '/../config/auth.php';
requireCustomer();
?>
<!DOCTYPE html>
<html lang="id">
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

    <!-- Leaflet CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
    
    <!-- Main CSS -->
    <link rel="stylesheet" href="assets/css/style.css" />
    <title>Checkout – Bloomify</title>
    
    <!-- Leaflet JS -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
  </head>
  <body>
    <!-- =============== HEADER (SAMA SEPERTI PAGE LAIN) =============== -->
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
              onclick="window.location.href='cart.php'"
            >
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

    <!-- ================= MAIN CHECKOUT ================= -->
    <main class="checkout-main">
      <div class="container checkout-layout">
        <!-- KIRI: FORM -->
        <section class="checkout-left">
          <h1 class="checkout-title">Checkout</h1>
          <p class="checkout-subtitle">
            Complete your order by filling in the details below
          </p>
          
          <!-- Hidden Inputs for Form Data -->
          <input type="hidden" id="inputSubtotal" name="subtotal" value="0">
          <input type="hidden" id="inputShipping" name="shipping_cost" value="0">
          <input type="hidden" id="inputGrandTotal" name="grand_total" value="0">
          <input type="hidden" id="inputLat" name="latitude" value="">
          <input type="hidden" id="inputLng" name="longitude" value="">

          <!-- Contact information -->
          <div class="checkout-card">
            <h2 class="checkout-section-title">Contact Information</h2>

            <div class="checkout-row-2">
              <div class="checkout-field">
                <label for="fullName" class="checkout-label"
                  >Example Recipient Name <span>*</span></label
                >
                <input
                  id="fullName"
                  type="text"
                  class="checkout-input"
                  placeholder="Enter full name"
                />
              </div>

              <div class="checkout-field">
              <label for="phone" class="checkout-label"
                >Phone Number <span>*</span></label
              >
              <input
                id="phone"
                type="tel"
                class="checkout-input"
                placeholder="08xxxxxxxxxx"
              />
            </div>
            </div>
          </div>

          <!-- Delivery address & Map -->
          <div class="checkout-card">
            <h2 class="checkout-section-title">Delivery Details</h2>

            <div class="checkout-field">
              <label for="address" class="checkout-label"
                >Street Address <span>*</span></label
              >
              <input
                id="address"
                type="text"
                class="checkout-input"
                placeholder="Jl. Mawar No. 123"
              />
            </div>

            <div class="checkout-row-3">
              <div class="checkout-field">
                <label for="city" class="checkout-label"
                  >City <span>*</span></label
                >
                <input id="city" type="text" class="checkout-input" placeholder="City" />
              </div>

              <div class="checkout-field">
                <label for="province" class="checkout-label"
                  >Province <span>*</span></label
                >
                <input id="province" type="text" class="checkout-input" placeholder="Province" />
              </div>

              <div class="checkout-field">
                <label for="postal" class="checkout-label"
                  >Postal Code</label
                >
                <input id="postal" type="text" class="checkout-input" placeholder="12345" />
              </div>
            </div>
            
            <div class="checkout-row-2" style="margin-top: 1rem;">
               <div class="checkout-field">
                  <label for="deliveryDate" class="checkout-label">Delivery Date <span>*</span></label>
                  <input type="date" id="deliveryDate" class="checkout-input">
               </div>
               <div class="checkout-field">
                  <label for="deliveryTime" class="checkout-label">Delivery Time <span>*</span></label>
                  <input type="time" id="deliveryTime" class="checkout-input">
               </div>
            </div>

            <!-- Map Container -->
             <div class="checkout-field" style="margin-top: 1rem;">
                <label class="checkout-label">Pin Location (Click on map) <span>*</span></label>
                <div id="map" style="height: 300px; width: 100%; border-radius: 10px; border: 1px solid #ccc;"></div>
             </div>
          </div>

          <!-- Payment -->
          <div class="checkout-card">
            <h2 class="checkout-section-title">Payment Method</h2>

            <div class="checkout-radio-group">
              <label class="checkout-radio">
                <input type="radio" name="payment" value="qris" checked />
                <span class="checkout-radio-body">
                  <span class="checkout-radio-title">QRIS</span>
                  <span class="checkout-radio-desc">Pay instantly with GoPay, OVO, Dana, LinkAja, BCA Mobile, dll</span>
                </span>
              </label>
            </div>
          </div>

          <!-- Additional notes -->
          <div class="checkout-card">
            <h2 class="checkout-section-title">Card Message</h2>
            <div class="checkout-field">
              <label for="notes" class="checkout-label"
                >Message for the card (Optional)</label
              >
              <textarea
                id="notes"
                class="checkout-textarea"
                rows="3"
                placeholder="Happy Birthday! Love, ..."
              ></textarea>
            </div>
          </div>
        </section>

        <!-- KANAN: ORDER SUMMARY -->
        <aside class="cart-summary checkout-summary">
          <h2 class="cart-summary-title">Order Summary</h2>

          <!-- Dynamic Items Container -->
          <div id="checkoutSummaryItems">
             <p style="font-size: 0.9rem; color: #666;">Loading items...</p>
          </div>

          <div class="cart-summary-row" style="margin-top: 1rem;">
            <span>Subtotal</span>
            <span id="checkoutSubtotal">Rp 0</span>
          </div>

          <div class="cart-summary-row">
            <span>Shipping Fee</span>
            <span id="checkoutShipping">Rp 0</span>
          </div>

          <hr class="cart-summary-divider" />

          <div class="cart-summary-total">
            <span>Total</span>
            <span id="checkoutTotal">Rp 0</span>
          </div>

          <div class="summary-actions">
            <!-- ID btnPlaceOrder dipake di JS -->
            <button class="checkout-btn" type="button" id="btnPlaceOrder">Place Order (QRIS)</button>

            <button
              class="continue-btn"
              type="button"
              onclick="window.location.href='cart.php'"
            >
              Back to Cart
            </button>
          </div>

          <p class="checkout-secure-note">
            Secure checkout with SSL encryption.
          </p>
        </aside>
      </div>
    </main>

    <!-- FOOTER (SAMA DENGAN PAGE LAIN) -->
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

    <script src="assets/js/script.js"></script>
    <script src="assets/js/cart_actions.js"></script>
    <script>
      // Inject APP_URL from server-side to client-side
      <?php
        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
        $domainName = $_SERVER['HTTP_HOST'];
        $scriptPath = dirname($_SERVER['SCRIPT_NAME']); 
        // fallbackDynamicUrl points to the directory of checkout.php (public)
        $fallbackDynamicUrl = $protocol . $domainName . $scriptPath;
        
        $appUrl = getenv('APP_URL') ?: $fallbackDynamicUrl;
      ?>
      const APP_URL = "<?php echo rtrim($appUrl, '/'); ?>";
      const PAKASIR_SLUG = "<?php echo getenv('PAKASIR_SLUG') ?: 'toko-bunga-pwd'; ?>";
      const PAKASIR_API_URL = "<?php echo getenv('PAKASIR_API') ?: 'https://app.pakasir.com'; ?>";
    </script>
    <!-- Checkout Logic -->
    <script src="assets/js/checkout.js"></script>
  </body>
</html>
