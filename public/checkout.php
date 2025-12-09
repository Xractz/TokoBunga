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

    <!-- Main CSS -->
    <link rel="stylesheet" href="assets/css/style.css" />
    <title>Checkout – Bloomify</title>
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
            <li><a href="index.html">Home</a></li>
            <li><a href="katalog.html">Katalog Bunga</a></li>
            <li><a href="tentang.html">Tentang Kami</a></li>
          </ul>

          <div class="auth-buttons">
            <button
              class="icon-btn"
              aria-label="Cart"
              onclick="window.location.href='cart.html'"
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
          <a href="login.html">Login</a>
          <a href="register.html">Register</a>
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

          <!-- Contact information -->
          <div class="checkout-card">
            <h2 class="checkout-section-title">Contact Information</h2>

            <div class="checkout-row-2">
              <div class="checkout-field">
                <label for="fullName" class="checkout-label"
                  >Full Name <span>*</span></label
                >
                <input
                  id="fullName"
                  type="text"
                  class="checkout-input"
                  placeholder="Enter your full name"
                />
              </div>

              <div class="checkout-field">
                <label for="email" class="checkout-label"
                  >Email address <span>*</span></label
                >
                <input
                  id="email"
                  type="email"
                  class="checkout-input"
                  placeholder="your@email.com"
                />
              </div>
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

          <!-- Delivery address -->
          <div class="checkout-card">
            <h2 class="checkout-section-title">Delivery Address</h2>

            <div class="checkout-field">
              <label for="address" class="checkout-label"
                >Street Address <span>*</span></label
              >
              <input
                id="address"
                type="text"
                class="checkout-input"
                placeholder="Enter your complete address"
              />
            </div>

            <div class="checkout-row-3">
              <div class="checkout-field">
                <label for="city" class="checkout-label"
                  >City <span>*</span></label
                >
                <input
                  id="city"
                  type="text"
                  class="checkout-input"
                  placeholder="City"
                />
              </div>

              <div class="checkout-field">
                <label for="province" class="checkout-label"
                  >Province <span>*</span></label
                >
                <input
                  id="province"
                  type="text"
                  class="checkout-input"
                  placeholder="Province"
                />
              </div>

              <div class="checkout-field">
                <label for="postal" class="checkout-label"
                  >Postal Code <span>*</span></label
                >
                <input
                  id="postal"
                  type="text"
                  class="checkout-input"
                  placeholder="12345"
                />
              </div>
            </div>
          </div>

          <!-- Payment -->
          <div class="checkout-card">
            <h2 class="checkout-section-title">Payment Method</h2>

            <div class="checkout-radio-group">
              <label class="checkout-radio">
                <input type="radio" name="payment" checked />
                <span class="checkout-radio-body">
                  <span class="checkout-radio-title">Bank Transfer</span>
                  <span class="checkout-radio-desc">BCA, Mandiri, BRI</span>
                </span>
              </label>

              <label class="checkout-radio">
                <input type="radio" name="payment" />
                <span class="checkout-radio-body">
                  <span class="checkout-radio-title">E-Wallet</span>
                  <span class="checkout-radio-desc">OVO, Gopay, Dana</span>
                </span>
              </label>

              <label class="checkout-radio">
                <input type="radio" name="payment" />
                <span class="checkout-radio-body">
                  <span class="checkout-radio-title">Cash on Delivery</span>
                  <span class="checkout-radio-desc"
                    >Pay when the flowers arrive</span
                  >
                </span>
              </label>
            </div>
          </div>

          <!-- Additional notes -->
          <div class="checkout-card">
            <h2 class="checkout-section-title">Additional Notes</h2>
            <div class="checkout-field">
              <label for="notes" class="checkout-label"
                >Optional message for your order</label
              >
              <textarea
                id="notes"
                class="checkout-textarea"
                rows="3"
                placeholder="Add delivery instructions or card message here"
              ></textarea>
            </div>
          </div>
        </section>

        <!-- KANAN: ORDER SUMMARY -->
        <aside class="cart-summary checkout-summary">
          <h2 class="cart-summary-title">Order Summary</h2>

          <div class="checkout-summary-item">
            <div class="checkout-summary-thumb">
              <img
                src="assets/images/gardenmix.jpg"
                alt="Garden Rose Bouquet"
              />
            </div>
            <div class="checkout-summary-info">
              <p class="checkout-summary-name" id="checkoutItemName">
                Garden Rose Bouquet
              </p>
              <p class="checkout-summary-meta">
                <span id="checkoutItemCount">0</span> bouquet(s)
              </p>
            </div>
          </div>

          <div class="cart-summary-row">
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
            <button class="checkout-btn" type="button">Place Order</button>

            <button
              class="continue-btn"
              type="button"
              onclick="window.location.href='cart.html'"
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
  </body>
</html>
