<?php
require_once __DIR__ . '/../api/middleware/is_login.php';

// Get user data from session
$userId = getUserId();
$userName = $_SESSION['name'] ?? 'User';
$userEmail = $_SESSION['email'] ?? '';
$userPhone = $_SESSION['phone'] ?? '';
$userRole = $_SESSION['role'] ?? 'customer';
?>

<!DOCTYPE html>
<html lang="id">
  <head>
    <meta charset="UTF-8" />
    <title>Profil – Bloomify</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
      href="https://fonts.googleapis.com/css2?family=Playfair+Display+SC:wght@400;700&family=Playfair+Display:wght@400;600;700&family=Playfair:wght@400;500&display=swap"
      rel="stylesheet"
    />

    <!-- Bootstrap Icons (kalau dipakai) -->
    <link
      rel="stylesheet"
      href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css"
    />

    <!-- CSS utama kamu -->
    <link rel="stylesheet" href="assets/css/style.css" />
  </head>
  <body>
    <!-- NAVBAR -->
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
            <li><a href="about.html">Tentang Kami</a></li>
          </ul>

          <div class="auth-buttons">
            <button class="icon-btn">
              <i class="bi bi-person-circle"></i>
            </button>
          </div>
        </nav>
      </div>
    </header>

    <!-- PROFILE PAGE -->
    <main class="container">
      <section class="profile-page">
        <div class="profile-layout">
          <!-- SIDEBAR -->
          <aside class="profile-sidebar">
            <div class="profile-sidebar-card">
              <div class="profile-avatar-wrapper">
                <div class="profile-avatar">
                  <img src="assets/images/profile.jpg" alt="Foto profil" />
                </div>
                <p class="profile-name"><?php echo htmlspecialchars($userName); ?></p>
                <p class="profile-role"><?php echo ucfirst(htmlspecialchars($userRole)); ?></p>
              </div>

              <nav class="profile-menu">
                <a href="#" class="profile-menu-item active">
                  <i class="bi bi-person-fill"></i>
                  <span>Informasi Pribadi</span>
                </a>

                <a href="/api/auth/logout.php" class="profile-menu-item">
                  <i class="bi bi-box-arrow-right"></i>
                  <span>Keluar</span>
                </a>
              </nav>
            </div>
          </aside>

          <!-- MAIN CONTENT -->
          <section class="profile-main-card">
            <div class="profile-main-header">
              <h2>Informasi Pribadi</h2>
              <p>
                Perbarui data akunmu untuk pengalaman belanja yang lebih nyaman.
              </p>
            </div>

            <form action="#" method="post" class="profile-form">
              <div class="profile-form-grid">
                <!-- Username -->
                <div class="form-group">
                  <label for="username">Username</label>
                  <div class="input-group">
                    <span class="input-icon">
                      <i class="bi bi-person"></i>
                    </span>
                    <input
                      type="text"
                      id="username"
                      name="username"
                      value="<?php echo htmlspecialchars($_SESSION['username'] ?? ''); ?>"
                      placeholder="Username"
                    />
                  </div>
                </div>

                <!-- Email -->
                <div class="form-group">
                  <label for="email">Email</label>
                  <div class="input-group">
                    <span class="input-icon">
                      <i class="bi bi-envelope"></i>
                    </span>
                    <input
                      type="email"
                      id="email"
                      name="email"
                      value="<?php echo htmlspecialchars($userEmail); ?>"
                      placeholder="Email"
                    />
                  </div>
                </div>

                <!-- Nomor telepon -->
                <div class="form-group profile-form-full">
                  <label for="phone">Nomor Telepon</label>
                  <div class="input-group">
                    <span class="input-icon">
                      <i class="bi bi-telephone"></i>
                    </span>
                    <input
                      type="text"
                      id="phone"
                      name="phone"
                      value="<?php echo htmlspecialchars($userPhone); ?>"
                      placeholder="08xx-xxxx-xxxx"
                    />
                  </div>
                </div>

                <!-- Alamat -->
                <div class="form-group profile-form-full">
                  <label for="address">Alamat Lengkap</label>
                  <textarea
                    id="address"
                    name="address"
                    class="profile-textarea"
                    placeholder="Alamat lengkap..."
                  ></textarea>
                </div>

                <!-- Foto profil -->
                <div class="form-group profile-form-full">
                  <label for="photo">Foto Profil</label>
                  <div class="profile-photo-row">
                    <div class="profile-photo-preview">
                      <img src="images/avatar-demo.jpg" alt="Foto profil" />
                    </div>

                    <div class="profile-upload-info">
                      <input
                        type="file"
                        id="photo"
                        name="photo"
                        accept="image/*"
                      />
                    </div>
                  </div>
                </div>
              </div>

              <!-- tombol bawah -->
              <div class="profile-actions">
                <button type="button" class="btn-profile-ghost">
                  Batalkan Perubahan
                </button>
                <button type="submit" class="btn-profile-save">
                  Simpan Perubahan
                </button>
              </div>
            </form>
          </section>
        </div>
      </section>
    </main>
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
  </body>
</html>